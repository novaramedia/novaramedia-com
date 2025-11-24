<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action( 'cmb2_init', 'nm_cmb_seo_metaboxes' );

/**
 * Define SEO meta box for posts and pages
 *
 * @since 4.2.0
 * @return void
 */
function nm_cmb_seo_metaboxes() {
  $prefix = '_cmb_';

  $seo_meta_box = new_cmb2_box(
    array(
      'id'           => 'seo_metabox',
      'title'        => __( 'SEO', 'cmb2' ),
      'object_types' => array( 'post', 'page' ), // Show on both posts and pages
      'context'      => 'normal',
      'priority'     => 'low',
      'show_names'   => true, // Show field names on the left
      'show_in_rest' => WP_REST_Server::READABLE,
    )
  );

  $seo_meta_box->add_field(
    array(
      'name'       => __( 'SEO Title Override', 'cmb2' ),
      'desc'       => __( 'Custom title for search engines and social sharing. Leave blank to auto-generate. Character limit: 60.', 'cmb2' ),
      'id'         => $prefix . 'seo_title',
      'type'       => 'text',
      'attributes' => array(
        'maxlength'   => 60,
        'placeholder' => 'Custom SEO title...',
      ),
    )
  );
}
