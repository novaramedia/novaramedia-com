<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

add_action( 'cmb2_init', 'nm_cmb_posttype_newsletters_metaboxes' );
/**
 * Register CMB2 meta boxes for newsletter post type.
 *
 * Adds meta boxes for newsletter configuration including banner settings,
 * Mailchimp integration, YouTube embeds, and display options.
 *
 * @return void
 */
function nm_cmb_posttype_newsletters_metaboxes() {
  $prefix = '_nm_';

  $cmb_term = new_cmb2_box(
      array(
      'id'           => $prefix . 'newsletters',
      'title'        => esc_html__( 'Newsletters Metabox', 'cmb2' ),
      'object_types' => array( 'newsletter' ),
      'context'      => 'normal',
      'priority'     => 'high',
      'show_names'   => true,
      'show_in_rest' => WP_REST_Server::READABLE,
    )
  );

  $cmb_term->add_field(
      array(
      'name' => esc_html__( 'Mailchimp Newsletter name', 'cmb2' ),
      'desc' => esc_html__( 'Exact case sensitive string match to the name of the Newsletter in Mailchimp', 'cmb2' ),
      'id'   => $prefix . 'mailchimp_key',
      'type' => 'text',
    )
  );

  $cmb_term->add_field(
    array(
      'name' => esc_html__( 'Banner headline', 'cmb2' ),
      'desc' => esc_html__( 'Headline for the banner. Should be a CTA', 'cmb2' ),
      'id'   => $prefix . 'banner_headline',
      'type' => 'text',
    )
  );

  $cmb_term->add_field(
    array(
      'name' => esc_html__( 'Banner text', 'cmb2' ),
      'desc' => esc_html__( 'Copy for the banner version of the newsletter signup form (optional)', 'cmb2' ),
      'id'   => $prefix . 'banner_text',
      'type' => 'textarea_small',
    )
  );

  $cmb_term->add_field(
    array(
      'name' => esc_html__( 'Banner image', 'cmb2' ),
      'desc' => esc_html__( 'Square fitting image for the banner (optional)', 'cmb2' ),
      'id'   => $prefix . 'banner_image',
      'type' => 'file',
    )
  );

  $cmb_term->add_field(
    array(
    'name'             => esc_html__( 'Banner background color', 'cmb2' ),
    'desc'             => esc_html__( 'Background color for the banner (optional—defaults to black)', 'cmb2' ),
    'id'               => $prefix . 'banner_background',
    'type'             => 'select',
    'show_option_none' => true,
    'options'          => array(
        'black'      => __( 'Black', 'cmb2' ),
        'white'      => __( 'White', 'cmb2' ),
        'gray-base'  => __( 'Gray Base', 'cmb2' ),
        'acfm-pink'  => __( 'ACFM Pink', 'cmb2' ),
        'yellow'     => __( 'Yellow', 'cmb2' ),
        'green'      => __( 'Green', 'cmb2' ),
        'lilac'      => __( 'Lilac', 'cmb2' ),
        'light-blue' => __( 'Light Blue', 'cmb2' ),
      ),
    )
  );

  $cmb_term->add_field(
    array(
      'name'             => esc_html__( 'Banner text color', 'cmb2' ),
      'desc'             => esc_html__( 'Text color for the banner (optional—defaults to white)', 'cmb2' ),
      'id'               => $prefix . 'banner_text_color',
      'type'             => 'select',
      'show_option_none' => true,
      'options'          => array(
        'black' => __( 'Black', 'cmb2' ),
        'white' => __( 'White', 'cmb2' ),
      ),
    )
  );

  $cmb_term->add_field(
    array(
      'name'             => esc_html__( 'Banner button color', 'cmb2' ),
      'desc'             => esc_html__( 'Button color for the banner (optional—defaults to red)', 'cmb2' ),
      'id'               => $prefix . 'banner_button_color',
      'type'             => 'select',
      'show_option_none' => true,
      'options'          => array(
        'black' => __( 'Black', 'cmb2' ),
        'white' => __( 'White', 'cmb2' ),
        'red'   => __( 'Red', 'cmb2' ),
      ),
    )
  );

  $cmb_term->add_field(
    array(
      'name'             => esc_html__( 'Title text size', 'cmb2' ),
      'desc'             => esc_html__( 'Change the display size of the title. To create more dynamic pages. The design direction is to make the text as big as possible but keeping the title on one line. (optional)', 'cmb2' ),
      'id'               => $prefix . 'title_size',
      'type'             => 'select',
      'show_option_none' => true,
      'options'          => array(
        'huge'    => __( 'Huge', 'cmb2' ),
        'big'     => __( 'Big', 'cmb2' ),
        'medium'  => __( 'Medium', 'cmb2' ),
        'smaller' => __( 'Smaller', 'cmb2' ),
      ),
    )
  );

  $cmb_term->add_field(
    array(
      'name' => esc_html__( 'YouTube embed code', 'cmb2' ),
      'desc' => esc_html__( 'ID of YouTube video. For example, if this is the URL https://www.youtube.com/watch?v=CmuDcXfBqTg&feature=c4-overview&list=UUOzMAa6IhV6uwYQATYG_2kg then the ID is the value after the ?v= and before the &, for this link CmuDcXfBqTg. Overrides featured image with YouTube embed for promo video (optional)', 'cmb2' ),
      'id'   => $prefix . 'youtube_id',
      'type' => 'text',
    )
  );

  $cmb_term->add_field(
    array(
      'name' => esc_html__( 'Support section override text', 'cmb2' ),
      'desc' => esc_html__( 'override copy for the support section below the email signup (optional)', 'cmb2' ),
      'id'   => $prefix . 'support_text',
      'type' => 'textarea_small',
    )
  );
}
