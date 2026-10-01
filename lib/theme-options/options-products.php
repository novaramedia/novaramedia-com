<?php
/**
 * Products options: per-product settings that don't belong on every
 * category. Top-level "Products" menu; each product is a sub-page.
 */
function nm_register_products_options_metabox() {
  $products_options = new_cmb2_box(
    array(
      'id'           => 'nm_products_options',
      'title'        => 'Products',
      'object_types' => array( 'options-page' ),
      'option_key'   => 'nm_products_options',
      'icon_url'     => 'dashicons-products',
      'capability'   => 'edit_posts',
    )
  );

  $products_options->add_field(
    array(
      'name' => 'Product settings',
      'desc' => 'Settings for individual products live in the sub-pages of this menu.',
      'id'   => 'products_intro',
      'type' => 'title',
    )
  );

  $dyor_options = new_cmb2_box(
    array(
      'id'           => 'nm_products_dyor_options',
      'title'        => 'Do Your Own Research',
      'object_types' => array( 'options-page' ),
      'option_key'   => 'nm_products_dyor_options',
      'parent_slug'  => 'nm_products_options',
      'capability'   => 'edit_posts',
    )
  );

  $dyor_options->add_field(
    array(
      'name'    => 'Support box heading',
      'desc'    => 'Heading for the support box between seasons on the archive. Leave blank for the site-wide support copy.',
      'id'      => 'support_heading',
      'type'    => 'text',
    )
  );

  $dyor_options->add_field(
    array(
      'name'    => 'Support box copy',
      'id'      => 'support_text',
      'desc'    => 'Leave blank for the site-wide support copy.',
      'type'    => 'textarea_small',
    )
  );

  $dyor_category      = get_category_by_slug( 'do-your-own-research' );
  $dyor_category_link = $dyor_category ? get_edit_term_link( $dyor_category->term_id, 'category' ) : '';

  $dyor_options->add_field(
    array(
      'name' => 'Do Your Own Research',
      'desc' => 'Settings only this product needs. Everything a category holds for any show (description, formatted description, podcast and YouTube links, logo, Open Graph image) stays on the category.'
        . ( $dyor_category_link ? ' <a href="' . esc_url( $dyor_category_link ) . '">Edit the Do Your Own Research category →</a>' : '' ),
      'id'   => 'dyor_intro',
      'type' => 'title',
    )
  );

  $seasons_group = $dyor_options->add_field(
    array(
      'id'          => 'seasons',
      'type'        => 'group',
      'description' => 'One entry per season. The archive shows a block for each season that has published episodes, newest season first. Set each episode\'s season in its Season / Episode box. Until season 1 has an entry here it uses the map fields on the Do Your Own Research category. Use each season number once.',
      'options'     => array(
        'group_title'   => 'Season entry {#}',
        'add_button'    => 'Add another season',
        'remove_button' => 'Remove season',
        'sortable'      => true,
        'closed'        => true,
      ),
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name'            => 'Season number',
      'id'              => 'number',
      'type'            => 'text_small',
      'sanitization_cb' => 'nm_sanitize_positive_int_meta',
      'attributes'      => array(
        'type' => 'number',
        'min'  => '1',
        'step' => '1',
      ),
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name' => 'Title',
      'desc' => 'Heading for this season\'s block. Defaults to "Season N".',
      'id'   => 'title',
      'type' => 'text',
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name'    => 'Description',
      'desc'    => 'Optional. Shown beside the season heading; nothing renders when blank.',
      'id'      => 'description',
      'type'    => 'wysiwyg',
      'options' => array(
        'media_buttons' => false,
        'teeny'         => true,
        'textarea_rows' => 6,
      ),
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name' => 'Figma file key',
      'desc' => 'The file key from this season\'s FigJam board URL (e.g. Twc9z7w8yaEzaO6m0PM1Kj). The map only shows when set.',
      'id'   => 'figma_file_key',
      'type' => 'text',
    )
  );

  $dyor_options->add_group_field(
    $seasons_group,
    array(
      'name' => 'Default map node ID',
      'desc' => 'Node the map opens on when no episode in this season has its own node ID. Use an invisible bounding rectangle to control the zoom level.',
      'id'   => 'figma_default_node_id',
      'type' => 'text',
    )
  );
}
add_action( 'cmb2_admin_init', 'nm_register_products_options_metabox' );

/**
 * DYOR seasons from Products → Do Your Own Research, keyed by season number,
 * newest first. Entries without a season number are dropped. Season 1 comes
 * from nm_get_dyor_seasons_seed() until it has a saved entry of its own.
 * Two entries with the same number: the later one in the list wins.
 *
 * @return array[] { number, title, description, figma_file_key, figma_default_node_id }
 */
function nm_get_dyor_seasons() {
  $saved   = NM_get_option( 'seasons', 'nm_products_dyor_options', array() );
  $seasons = array();

  if ( is_array( $saved ) ) {
    foreach ( $saved as $entry ) {
      $number = isset( $entry['number'] ) ? (int) $entry['number'] : 0;

      if ( $number < 1 ) {
        continue;
      }

      $seasons[ $number ] = array(
        'number'                => $number,
        'title'                 => isset( $entry['title'] ) ? trim( $entry['title'] ) : '',
        'description'           => isset( $entry['description'] ) ? trim( $entry['description'] ) : '',
        'figma_file_key'        => isset( $entry['figma_file_key'] ) ? trim( $entry['figma_file_key'] ) : '',
        'figma_default_node_id' => isset( $entry['figma_default_node_id'] ) ? trim( $entry['figma_default_node_id'] ) : '',
      );
    }
  }

  // Until season 1 has its own entry it keeps the legacy category map, so
  // saving only a new season can't drop season 1's map.
  if ( ! isset( $seasons[1] ) ) {
    $seasons += nm_get_dyor_seasons_seed();
  }

  krsort( $seasons );

  return $seasons;
}

/**
 * Context copy for the DYOR archive's between-seasons support box, the same
 * for both donation modes. A blank field is dropped by render_support_form(),
 * so the site-wide support copy shows for it.
 *
 * @return array Shape accepted by render_support_form()'s $copy.
 */
function nm_get_dyor_support_copy() {
  $copy = array(
    'heading' => trim( (string) NM_get_option( 'support_heading', 'nm_products_dyor_options', '' ) ),
    'text'    => trim( (string) NM_get_option( 'support_text', 'nm_products_dyor_options', '' ) ),
  );

  return array(
    'regular' => $copy,
    'oneoff'  => $copy,
  );
}

/**
 * Season 1 built from the legacy DYOR category map fields, so the archive
 * keeps today's map until season 1 has an entry on Products → Do Your Own
 * Research.
 * Computed on read, never written.
 *
 * @deprecated 4.10.0 Remove in 4.11.0 with the category map fields, once production has saved the Products page.
 * @return array[]
 */
function nm_get_dyor_seasons_seed() {
  $term = get_category_by_slug( 'do-your-own-research' );

  if ( ! $term ) {
    return array();
  }

  return array(
    1 => array(
      'number'                => 1,
      'title'                 => 'Season 1',
      'description'           => '',
      'figma_file_key'        => (string) get_term_meta( $term->term_id, '_nm_dyor_figma_file_key', true ),
      'figma_default_node_id' => (string) get_term_meta( $term->term_id, '_nm_dyor_figma_default_node_id', true ),
    ),
  );
}
