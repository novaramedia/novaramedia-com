<?php
/**
 * One-off: strip empty paragraphs from content saved before the save-time
 * filters existed (lib/functions-filters.php).
 *
 * Applies nm_strip_empty_paragraphs() to:
 * - post_content and post_excerpt of every post type that uses the editor
 *   (all statuses except auto-draft, trash and revisions)
 * - every CMB2 text field (text, text_small, textarea, textarea_small,
 *   wysiwyg), including group sub-fields, in post meta, term meta and the
 *   CMB2 options pages
 *
 * Post rows are written with $wpdb directly so post_modified is not bumped —
 * wp_update_post() would mark every changed post as updated in feeds and
 * sitemaps. Meta and options don't carry modified dates.
 *
 * Usage (dry run, lists what would change): wp eval-file scripts/one-off/2026-10-strip-empty-paragraphs.php
 * Apply:                                    wp eval-file scripts/one-off/2026-10-strip-empty-paragraphs.php apply
 *
 * Safe to re-run: the helper is stable on its own output, so a second run
 * changes nothing.
 */

global $wpdb;

$nm_apply      = in_array( 'apply', $args, true );
$nm_text_types = array( 'text', 'text_small', 'textarea', 'textarea_small', 'wysiwyg' );
$nm_counts     = array();
$nm_changed    = array();

// Options pages for the front page, fundraising and products register on
// cmb2_admin_init, which only fires in wp-admin.
if ( ! did_action( 'cmb2_admin_init' ) ) {
  do_action( 'cmb2_admin_init' );
}

/**
 * Strip a stored CMB2 value. Group values are rows of sub-field id => value;
 * only sub-fields of a text type are stripped.
 *
 * @param mixed $value      Unserialised stored value.
 * @param array $field      CMB2 field args.
 * @param array $text_types Field types to strip.
 *
 * @return mixed
 */
$nm_strip_field_value = function ( $value, $field, $text_types ) {
  if ( 'group' !== $field['type'] ) {
    return nm_strip_empty_paragraphs_deep( $value );
  }

  if ( ! is_array( $value ) ) {
    return $value;
  }

  foreach ( $value as $row_key => $row ) {
    foreach ( $field['fields'] as $sub_field ) {
      if ( isset( $row[ $sub_field['id'] ] ) && in_array( $sub_field['type'], $text_types, true ) ) {
        $value[ $row_key ][ $sub_field['id'] ] = nm_strip_empty_paragraphs_deep( $row[ $sub_field['id'] ] );
      }
    }
  }

  return $value;
};

// Collect target fields: meta key => field args, per object type; option key => fields.
$nm_meta_fields   = array(
  'post' => array(),
  'term' => array(),
);
$nm_option_fields = array();

foreach ( CMB2_Boxes::get_all() as $nm_box ) {
  $nm_object_type = $nm_box->mb_object_type();
  $nm_fields      = array_filter(
    $nm_box->prop( 'fields' ),
    function ( $field ) use ( $nm_text_types ) {
      if ( 'group' === $field['type'] ) {
        return (bool) array_intersect( wp_list_pluck( $field['fields'], 'type' ), $nm_text_types );
      }
      return in_array( $field['type'], $nm_text_types, true );
    }
  );

  if ( 'options-page' === $nm_object_type ) {
    foreach ( (array) $nm_box->options_page_keys() as $nm_option_key ) {
      $nm_option_fields[ $nm_option_key ] = array_merge( $nm_option_fields[ $nm_option_key ] ?? array(), array_values( $nm_fields ) );
    }
  } elseif ( isset( $nm_meta_fields[ $nm_object_type ] ) ) {
    foreach ( $nm_fields as $nm_field ) {
      $nm_meta_fields[ $nm_object_type ][ $nm_field['id'] ] = $nm_field;
    }
  } elseif ( $nm_fields ) {
    WP_CLI::warning( "Skipping box {$nm_box->cmb_id}: object type '{$nm_object_type}' not handled." );
  }
}

// Posts.
$nm_post_types = array_values( array_filter( get_post_types(), fn( $type ) => post_type_supports( $type, 'editor' ) ) );
$nm_types_in   = implode( ',', array_fill( 0, count( $nm_post_types ), '%s' ) );
$nm_last_id    = 0;

do {
  // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- placeholders built above.
  $nm_rows = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_type, post_title, post_content, post_excerpt FROM {$wpdb->posts} WHERE ID > %d AND post_type IN ($nm_types_in) AND post_status NOT IN ('auto-draft', 'trash', 'inherit') ORDER BY ID LIMIT 500", array_merge( array( $nm_last_id ), $nm_post_types ) ) );

  foreach ( $nm_rows as $nm_row ) {
    $nm_last_id = (int) $nm_row->ID;
    $nm_update  = array();

    foreach ( array( 'post_content', 'post_excerpt' ) as $nm_column ) {
      $nm_clean = nm_strip_empty_paragraphs( $nm_row->$nm_column );
      if ( $nm_clean !== $nm_row->$nm_column ) {
        $nm_update[ $nm_column ] = $nm_clean;
      }
    }

    if ( ! $nm_update ) {
      continue;
    }

    if ( $nm_apply ) {
      if ( false === $wpdb->update( $wpdb->posts, $nm_update, array( 'ID' => $nm_last_id ) ) ) {
        WP_CLI::warning( "Post {$nm_last_id} not updated: {$wpdb->last_error}" );
        continue;
      }
      clean_post_cache( $nm_last_id );
    }

    $nm_changed[ $nm_last_id ] = "{$nm_row->post_type}\t{$nm_row->post_title}";
    foreach ( array_keys( $nm_update ) as $nm_column ) {
      $nm_counts[ $nm_column ] = ( $nm_counts[ $nm_column ] ?? 0 ) + 1;
    }
  }
} while ( $nm_rows );

// Post and term meta.
foreach ( $nm_meta_fields as $nm_object_type => $nm_fields ) {
  if ( ! $nm_fields ) {
    continue;
  }

  $nm_table    = 'post' === $nm_object_type ? $wpdb->postmeta : $wpdb->termmeta;
  $nm_id_col   = 'post' === $nm_object_type ? 'post_id' : 'term_id';
  $nm_keys_in  = implode( ',', array_fill( 0, count( $nm_fields ), '%s' ) );
  // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- placeholders built above.
  $nm_meta_rows = $wpdb->get_results( $wpdb->prepare( "SELECT meta_id, {$nm_id_col} AS object_id, meta_key, meta_value FROM {$nm_table} WHERE meta_key IN ($nm_keys_in)", array_keys( $nm_fields ) ) );

  foreach ( $nm_meta_rows as $nm_meta ) {
    $nm_value = maybe_unserialize( $nm_meta->meta_value );
    $nm_clean = $nm_strip_field_value( $nm_value, $nm_fields[ $nm_meta->meta_key ], $nm_text_types );

    if ( $nm_clean === $nm_value ) {
      continue;
    }

    if ( $nm_apply && ! update_metadata_by_mid( $nm_object_type, $nm_meta->meta_id, $nm_clean ) ) {
      WP_CLI::warning( "{$nm_object_type} meta {$nm_meta->meta_id} ({$nm_meta->meta_key}) not updated." );
      continue;
    }

    $nm_counts[ "{$nm_object_type} meta {$nm_meta->meta_key}" ] = ( $nm_counts[ "{$nm_object_type} meta {$nm_meta->meta_key}" ] ?? 0 ) + 1;
    if ( 'post' === $nm_object_type ) {
      $nm_changed[ (int) $nm_meta->object_id ] = $nm_changed[ (int) $nm_meta->object_id ] ?? get_post_type( $nm_meta->object_id ) . "\t" . get_the_title( $nm_meta->object_id );
    }
  }
}

// Options pages.
foreach ( $nm_option_fields as $nm_option_key => $nm_fields ) {
  $nm_option = get_option( $nm_option_key );
  if ( ! is_array( $nm_option ) ) {
    continue;
  }

  $nm_clean = $nm_option;
  foreach ( $nm_fields as $nm_field ) {
    if ( isset( $nm_clean[ $nm_field['id'] ] ) ) {
      $nm_clean[ $nm_field['id'] ] = $nm_strip_field_value( $nm_clean[ $nm_field['id'] ], $nm_field, $nm_text_types );
    }
  }

  if ( $nm_clean !== $nm_option ) {
    if ( $nm_apply && ! update_option( $nm_option_key, $nm_clean ) ) {
      WP_CLI::warning( "Option {$nm_option_key} not updated." );
      continue;
    }
    $nm_counts[ "option {$nm_option_key}" ] = 1;
  }
}

foreach ( $nm_counts as $nm_label => $nm_count ) {
  WP_CLI::log( "{$nm_label}: {$nm_count}" );
}

WP_CLI::log( '' );
foreach ( $nm_changed as $nm_id => $nm_label ) {
  WP_CLI::log( "{$nm_id}\t{$nm_label}" );
}

WP_CLI::success( count( $nm_changed ) . ' posts and ' . array_sum( $nm_counts ) . ' values ' . ( $nm_apply ? 'updated.' : 'would change (dry run).' ) );
