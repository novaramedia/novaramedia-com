<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
/**
 * Generate custom titles for web and social with conditional author/standfirst
 */
function nm_generate_custom_title( $for_social = false ) {
  global $post;

  // Categories that should include author in title
  $author_categories = array(
    'opinion',
    'analysis',
  );

  // Categories that should include standfirst (only if no author)
  $standfirst_categories = array(
    'downstream',
  );

  $title_parts = array();

  // Start with post title (always first)
  if ( is_singular() && ! empty( $post ) ) {
    // Check for custom SEO title override first
    $seo_title = get_post_meta( $post->ID, '_cmb_seo_title', true );

    if ( ! empty( $seo_title ) ) {
      $title_parts[] = $seo_title;
    } else {
      $title_parts[] = get_the_title( $post );
    }

    // Get post categories for conditional logic
    $post_categories = wp_get_post_categories( $post->ID, array( 'fields' => 'slugs' ) );

    // Get meta in single query for performance
    $meta = get_post_meta( $post->ID );

    // Check for author (priority over standfirst)
    $should_add_author = array_intersect( $author_categories, $post_categories );
    $author_added = false;

    if ( $should_add_author ) {
      $author_text = nm_get_post_authors( $post->ID, 'text' );
      if ( $author_text !== false ) { // Only add if we have a specific author
        $title_parts[] = $author_text;
        $author_added = true;
      }
    }

    // Check for standfirst (only if no author was added)
    if ( ! $author_added ) {
      $should_add_standfirst = array_intersect( $standfirst_categories, $post_categories );

      if ( $should_add_standfirst && ! empty( $meta['_cmb_standfirst'][0] ) ) {
        $title_parts[] = trim( $meta['_cmb_standfirst'][0] );
      }
    }
  } elseif ( is_home() || is_front_page() ) {
    $title_parts[] = get_bloginfo( 'name' );

    if ( ! $for_social ) {
      $title_parts[] = get_bloginfo( 'description' );
      return implode( ' | ', $title_parts ); // Early return for homepage
    }
  } elseif ( is_archive() ) {
    if ( is_category() || is_tag() || is_tax() ) {
      $title_parts[] = single_term_title( '', false );
    } else {
      $title_parts[] = get_the_archive_title();
    }
  } elseif ( is_search() ) {
    $title_parts[] = 'Search: ' . get_search_query();

  } elseif ( is_404() ) {
    $title_parts[] = 'Page Not Found';

  } else {
    $title_parts[] = get_the_title();
  }

  // Add site name (only for web, not social)
  if ( ! $for_social ) {
    $title_parts[] = get_bloginfo( 'name' );
  }

  return implode( ' | ', $title_parts );
}

/**
 * Hook into WordPress document title system for web titles
 */
function nm_customize_document_title() {
  return nm_generate_custom_title( false ); // false = for web (include site name)
}
add_filter( 'pre_get_document_title', 'nm_customize_document_title' );

/**
 * Generate social media title (without site name)
 */
function nm_get_social_title() {
  return nm_generate_custom_title( true ); // true = for social (no site name)
}
