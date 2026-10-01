<?php
/**
 * Site search: query scope and destinations.
 *
 * Destinations are places a search can point at that WP search itself never
 * returns: category archives (terms, not posts), newsletters, and fixed
 * sections of the site such as the shop or the events archive. They render
 * as "Suggested" cards above the post results on the search page via
 * partials/search-destinations.
 */

/**
 * Include custom post types in front-end search results.
 *
 * WP main query defaults to post_type='post' for search even when CPTs are
 * registered with exclude_from_search=false. This hook makes the intent explicit.
 * notice is excluded (registered with exclude_from_search=true; no standalone value).
 * newsletter is excluded here because newsletters surface as search destinations instead.
 *
 * @param WP_Query $query The current query.
 */
function nm_search_include_cpts( WP_Query $query ) {
  if ( $query->is_search() && $query->is_main_query() && ! is_admin() ) {
    $existing = $query->get( 'post_type' );
    if ( empty( $existing ) ) {
      $query->set( 'post_type', array( 'post', 'contributor', 'event', 'job' ) );
    }
  }
}
add_action( 'pre_get_posts', 'nm_search_include_cpts' );

/**
 * Minimum search query length before destinations are matched.
 *
 * Keeps one- and two-letter queries from matching most of the category list.
 */
const NM_SEARCH_DESTINATIONS_MIN_LENGTH = 3;

/**
 * Maximum number of destinations shown as Suggested above search results.
 */
const NM_SEARCH_DESTINATIONS_LIMIT = 6;

/**
 * Fixed site sections that can be matched by keyword.
 *
 * Each entry is an array with 'label', 'url' and 'keywords'. A destination
 * matches when the query contains one of its keywords, or a keyword contains
 * the query. Filterable via `nm_search_destinations`.
 *
 * @since 4.11.0
 *
 * @return array[] List of destinations.
 */
function nm_get_search_destinations() {
  $destinations = array(
    array(
      'label'    => 'Merch Shop',
      'url'      => 'https://shop.novaramedia.com',
      'keywords' => array( 'shop', 'merch', 'merchandise', 't-shirt', 'tshirt', 'tote', 'clothing' ),
    ),
    array(
      'label'    => 'Events',
      'url'      => get_post_type_archive_link( 'event' ),
      'keywords' => array( 'event', 'events', 'tickets', 'live show' ),
    ),
    array(
      'label'    => 'Newsletters',
      'url'      => get_post_type_archive_link( 'newsletter' ),
      'keywords' => array( 'newsletter', 'newsletters', 'email' ),
    ),
    array(
      'label'    => 'Jobs',
      'url'      => home_url( '/jobs/' ),
      'keywords' => array( 'job', 'jobs', 'careers', 'vacancies', 'work for us' ),
    ),
    array(
      'label'    => 'Support Us',
      'url'      => home_url( '/support/' ),
      'keywords' => array( 'support', 'donate', 'donation', 'subscribe', 'membership' ),
    ),
  );

  return apply_filters( 'nm_search_destinations', $destinations );
}

/**
 * Extra search keywords for categories and newsletters, keyed by slug.
 *
 * Short names and aliases a reader might type that are not in the name.
 * Filterable via `nm_search_destination_keywords`.
 *
 * @since 4.11.0
 *
 * @return array<string, string[]> Slug => keywords.
 */
function nm_get_search_destination_keywords() {
  $keywords = array(
    'do-your-own-research' => array( 'dyor' ),
  );

  return apply_filters( 'nm_search_destination_keywords', $keywords );
}

/**
 * Whether a query matches any keyword: the query contains the keyword, or
 * the keyword contains the query.
 *
 * @param string   $query    Lowercased search query.
 * @param string[] $keywords Keywords.
 * @return bool
 */
function nm_search_query_matches_keywords( $query, $keywords ) {
  foreach ( $keywords as $keyword ) {
    $keyword = mb_strtolower( $keyword );

    if ( str_contains( $query, $keyword ) || str_contains( $keyword, $query ) ) {
      return true;
    }
  }

  return false;
}

/**
 * Rank how closely a label matches the query. Lower is closer.
 *
 * @param string $label Destination label.
 * @param string $query Lowercased search query.
 * @return int 0 exact, 1 label contains query, 2 other match.
 */
function nm_search_destination_rank( $label, $query ) {
  $label = mb_strtolower( $label );

  if ( $label === $query ) {
    return 0;
  }

  if ( str_contains( $label, $query ) ) {
    return 1;
  }

  return 2;
}

/**
 * Collect destinations matching a search query.
 *
 * Combines category archives, newsletters and keyword-matched site sections,
 * ranked closest match first and de-duplicated by where each link lands.
 *
 * @since 4.11.0
 *
 * @param string $query Raw search query.
 * @return array[] Matches, each with 'label', 'url', 'type'
 *                 (category|newsletter|destination), 'object_id' and
 *                 'description'.
 */
function nm_get_search_destination_matches( $query ) {
  $query = mb_strtolower( trim( $query ) );

  if ( mb_strlen( $query ) < NM_SEARCH_DESTINATIONS_MIN_LENGTH ) {
    return array();
  }

  $matches  = array();
  $keywords = nm_get_search_destination_keywords();

  // Matched in PHP rather than with get_terms( 'search' ) so keywords work.
  // The non-empty category list is small.
  $terms = get_terms(
    array(
      'taxonomy'   => 'category',
      'hide_empty' => true,
      'exclude'    => array( (int) get_option( 'default_category' ) ),
    )
  );

  if ( ! is_wp_error( $terms ) ) {
    foreach ( $terms as $term ) {
      $term_keywords = array_merge( array( $term->name ), $keywords[ $term->slug ] ?? array() );

      if ( ! nm_search_query_matches_keywords( $query, $term_keywords ) ) {
        continue;
      }

      $matches[] = array(
        'label'       => $term->name,
        'url'         => get_term_link( $term ),
        'type'        => 'category',
        'object_id'   => $term->term_id,
        'description' => wp_strip_all_tags( $term->description ),
      );
    }
  }

  $newsletters = get_posts(
    array(
      'post_type'      => 'newsletter',
      'posts_per_page' => -1,
      'no_found_rows'  => true,
    )
  );

  foreach ( $newsletters as $newsletter ) {
    $label = get_the_title( $newsletter );

    $newsletter_keywords = array_merge( array( $label ), $keywords[ $newsletter->post_name ] ?? array() );

    if ( ! nm_search_query_matches_keywords( $query, $newsletter_keywords ) ) {
      continue;
    }

    // Newsletters often share a name with their category, so say which is which.
    if ( ! str_contains( mb_strtolower( $label ), 'newsletter' ) ) {
      $label .= ' newsletter';
    }

    $matches[] = array(
      'label'       => $label,
      'url'         => get_permalink( $newsletter ),
      'type'        => 'newsletter',
      'object_id'   => $newsletter->ID,
      'description' => wp_strip_all_tags( get_the_excerpt( $newsletter ) ),
    );
  }

  foreach ( nm_get_search_destinations() as $destination ) {
    if ( empty( $destination['url'] ) ) {
      continue;
    }

    if ( ! nm_search_query_matches_keywords( $query, array_merge( array( $destination['label'] ), $destination['keywords'] ) ) ) {
      continue;
    }

    $matches[] = array(
      'label'       => $destination['label'],
      'url'         => $destination['url'],
      'type'        => 'destination',
      'object_id'   => 0,
      'description' => '',
    );
  }

  // Stable sort: closest label match first, source order otherwise. Rank and
  // index are unique together, so the match arrays are never compared.
  $ranked = array();
  foreach ( $matches as $index => $match ) {
    $ranked[] = array( nm_search_destination_rank( $match['label'], $query ), $index, $match );
  }
  sort( $ranked );

  // De-duplicate by where the link really lands. When two matches resolve to
  // the same place, keep the one that already lives there (the category, not
  // a newsletter that redirects to it), so its name and image describe it.
  $results = array();
  foreach ( $ranked as $entry ) {
    $match = $entry[2];

    if ( is_wp_error( $match['url'] ) ) {
      continue;
    }

    $canonical = nm_resolve_canonical_url( $match['url'] );

    if ( isset( $results[ $canonical ] ) && ( $results[ $canonical ]['url'] === $canonical || $match['url'] !== $canonical ) ) {
      continue;
    }

    $results[ $canonical ] = $match;
  }

  return array_slice( array_values( $results ), 0, NM_SEARCH_DESTINATIONS_LIMIT );
}

/**
 * Pick the image for a search suggestion card.
 *
 * Product logo tile, then the item's own image, then the newest post's
 * thumbnail (categories only), then a branded tile.
 *
 * @since 4.11.0
 *
 * @param array $destination Destination match from nm_get_search_destination_matches().
 * @return array 'kind' (logo|image|tile) and 'attachment_id'.
 */
function nm_get_search_destination_image( $destination ) {
  $id = (int) $destination['object_id'];

  if ( $destination['type'] === 'category' ) {
    $logo_id = (int) get_term_meta( $id, '_nm_category_logo_id', true );
    if ( $logo_id ) {
      return array(
        'kind'          => 'logo',
        'attachment_id' => $logo_id,
      );
    }

    $og_id = (int) get_term_meta( $id, '_nm_category_og_image_id', true );
    if ( $og_id ) {
      return array(
        'kind'          => 'image',
        'attachment_id' => $og_id,
      );
    }

    $latest = get_posts(
      array(
        'cat'            => $id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
      )
    );

    if ( $latest ) {
      // Same preference as render_thumbnail(): alt thumbnail over featured image.
      $alt_id   = (int) get_post_meta( $latest[0], '_cmb_alt_thumb_id', true );
      $thumb_id = $alt_id ? $alt_id : (int) get_post_thumbnail_id( $latest[0] );

      if ( $thumb_id ) {
        return array(
          'kind'          => 'image',
          'attachment_id' => $thumb_id,
        );
      }
    }
  }

  if ( $destination['type'] === 'newsletter' ) {
    $thumb_id = (int) get_post_thumbnail_id( $id );
    if ( $thumb_id ) {
      return array(
        'kind'          => 'image',
        'attachment_id' => $thumb_id,
      );
    }
  }

  return array(
    'kind'          => 'tile',
    'attachment_id' => 0,
  );
}

/**
 * Corner tag for a search suggestion card.
 *
 * @since 4.11.0
 *
 * @param array $destination Destination match from nm_get_search_destination_matches().
 * @return string
 */
function nm_get_search_destination_tag( $destination ) {
  $tags = array(
    'category'    => 'Show',
    'newsletter'  => 'Newsletter',
    'destination' => 'Section',
  );

  return $tags[ $destination['type'] ] ?? 'Section';
}
