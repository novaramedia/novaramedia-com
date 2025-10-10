<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action( 'cmb2_init', 'nm_cmb_post_metaboxes' );

function nm_cmb_post_metaboxes() {
  $prefix = '_cmb_';

  $meta_boxes = new_cmb2_box( array (
    'id'         => 'post_metabox',
    'title'      => __( 'Post Meta', 'cmb' ),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Standfirst', 'cmb' ),
    'desc' => __( 'Required!', 'cmb' ),
    'id'   => $prefix . 'standfirst',
    'type' => 'textarea_small',
  ) );

  $meta_boxes->add_field( array(
    'name'    => __( 'Short description', 'cmb' ),
    'desc'    => __( '...', 'cmb' ),
    'id'      => $prefix . 'short_desc',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ) );

  $meta_boxes->add_field( array(
    'name'    => __( 'Contributor[s]', 'cmb' ),
    'desc'    => __( 'Associate with contributor[s]. Will display multiple contributors in order of selection, with first selected displayed first. (optional)', 'cmb' ),
    'id'      => $prefix . 'contributors',
    'type'    => 'post_search_text',
    'post_type'   => array('contributor'),
    'select_behavior' => 'add',
  ) );

  $meta_boxes->add_field( array(
    'name'    => __( 'Related Posts', 'cmb' ),
    'desc'    => __( 'If set will show related posts at the bottom of the post. Max 3 shown(optional)', 'cmb' ),
    'id'      => $prefix . 'related_posts',
    'type'    => 'post_search_text',
    'post_type'   => array('post'),
    'select_behavior' => 'add',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Misc download', 'cmb' ),
    'desc' => __( 'Upload an file or enter a URL.', 'cmb' ),
    'id'   => $prefix . 'dl',
    'type' => 'file',
  ) );

  $meta_boxes->add_field( array(
    'name' => __( 'Alternative social sharing thumbnail', 'cmb' ),
    'desc' => __( 'This image will override the thumbnail as the image shown on social media when sharing. (optional)', 'cmb' ),
    'id'   => $prefix . 'alt_social',
    'type' => 'file',
  ) );

  $meta_boxes->add_field( array(
    'name'    => __( 'Support box override', 'cmb' ),
    'desc'    => __( 'If set this will override any red outlined support boxes on the single post page(optional)', 'cmb' ),
    'id'      => $prefix . 'support_box_override',
    'type'    => 'textarea_small',
  ) );

  // CURATION

  $curation_boxes = new_cmb2_box( array (
    'id'         => 'post_curation_metabox',
    'title'      => __( 'Curation Meta', 'cmb' ),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => false,
  ) );

  $curation_boxes->add_field( array(
    'name' => __( 'Featurable?', 'cmb' ),
    'desc' => __( 'This will allow the post to be featured automatically above the fold and in other priority locations. This is a judgement on prominence', 'cmb' ),
    'id'   => $prefix . 'featurable',
    'type' => 'checkbox',
  ) );

  // AUDIO

  $audio_metabox = new_cmb2_box( array (
    'id'         => 'fm_metabox',
    'title'      => __( 'Audio Meta', 'cmb' ),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ) );

  $audio_metabox->add_field( array(
    'name' => __( 'Soundcloud URL', 'cmb' ),
    'desc' => __( 'Required! Enter a full URL.', 'cmb' ),
    'id'   => $prefix . 'sc',
    'type' => 'text_url',
  ) );

  $audio_metabox->add_field( array(
    'name' => __( 'Is a Resonance show?', 'cmb' ),
    'desc' => __( '', 'cmb' ),
    'id'   => $prefix . 'is_resonance',
    'type' => 'checkbox',
  ) );

  $audio_metabox->add_field( array(
    'name' => __( 'Download URL', 'cmb' ),
    'desc' => __( 'Enter a URL.', 'cmb' ),
    'id'   => $prefix . 'dl_mp3',
    'type' => 'text_url',
  ) );

  $audio_metabox->add_field( array(
    'name' => __( 'Transcript', 'cmb' ),
    'desc' => __( '(optional) Shows below the content', 'cmb' ),
    'id'   => $prefix . 'transcript',
    'type' => 'wysiwyg',
  ) );

  // VIDEO

  $video_metabox = new_cmb2_box( array (
    'id'         => 'video_metabox',
    'title'      => __( 'Video Meta', 'cmb' ),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ) );

  $video_metabox->add_field( array(
    'name' => __( 'YouTube ID', 'cmb' ),
    'desc' => __( 'Required! ID of YouTube video. For example if this is the url https://www.youtube.com/watch?v=CmuDcXfBqTg&feature=c4-overview&list=UUOzMAa6IhV6uwYQATYG_2kg then the ID is the value after the ?v= and before the &, for this link CmuDcXfBqTg', 'cmb' ),
    'id'   => $prefix . 'utube',
    'type' => 'text',
  ) );

  $video_metabox->add_field( array(
    'name' => __( 'Alternate thumbnail', 'cmb' ),
    'desc' => __( 'Without any text. Just an image', 'cmb' ),
    'id'   => $prefix . 'alt_thumb',
    'type' => 'file',
  ) );

  // Articles

  $articles_metabox = new_cmb2_box( array (
    'id'         => 'articles_metabox',
    'title'      => __( 'Articles Meta', 'cmb' ),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ) );

  $articles_metabox->add_field( array(
    'name' => __( 'Author', 'cmb' ),
    'desc' => __( 'Required! Full name or multiple names.', 'cmb' ),
    'id'   => $prefix . 'author',
    'type' => 'text',
  ) );

  $articles_metabox->add_field( array(
    'name' => __( 'Author Twitter', 'cmb' ),
    'desc' => __( 'Optional. No @. For multiple authors add extra rows', 'cmb' ),
    'id'   => $prefix . 'author_twitter',
    'type' => 'text',
    'repeatable' => true,
  ) );

  $articles_metabox->add_field( array(
    'name' => __( 'Layout', 'cmb' ),
    'desc' => __( 'Adjusts layout style. Use large splash for features with high quality imagery. Us no image if artwork is repeated in the copy or is incredibly dull.', 'cmb' ),
    'id'   => $prefix . 'article_layout',
    'type' => 'radio',
    'show_option_none' => false,
    'options'          => array(
      'basic' => __( 'Basic', 'cmb2' ),
      'basic-no-image'     => __( 'Basic (no image)', 'cmb2' ),
      'large-image'   => __( 'Large splashed image', 'cmb2' ),
    ),
  ) );

  // Resources

  $resources_metabox = new_cmb2_box( array(
    'id'         => 'resources_metabox',
    'title'      => __( 'Post Resources', 'cmb' ),
    'object_types' => array( 'post' ),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true,
    'show_in_rest' => WP_REST_Server::READABLE,
  ) );

  $resources_group_field = $resources_metabox->add_field( array(
    'id'          => $prefix . 'resources',
    'type'        => 'group',
    'options'     => array(
      'group_title'   => __( 'Resource {#}', 'cmb' ), // {#} gets replaced by row number
      'add_button'    => __( 'Add Another Resource', 'cmb' ),
      'remove_button' => __( 'Remove Resource', 'cmb' ),
      'sortable'      => true,
    ),
  ) );

  $resources_metabox->add_group_field( $resources_group_field, array(
    'name' => 'Resource Title',
    'id'   => 'title',
    'type' => 'text',
  ) );

  $resources_metabox->add_group_field( $resources_group_field, array(
    'name' => 'Resource Link',
    'id'   => 'link',
    'type' => 'text_url',
  ) );
  }
