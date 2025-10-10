<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action( 'cmb2_init', 'nm_cmb_metaboxes_instructions' );

function nm_cmb_metaboxes_instructions() {
  $prefix = '_nm_';

  $post_instructions = new_cmb2_box( array (
    'id'         => 'pot_instructions_metabox',
    'title'      => __( 'Instructions', 'cmb' ),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ) );

  $post_instructions->add_field( array(
    'name' => 'How to post',
    'desc' => 'This is a guide on how to post.',
    'id'   => $prefix . 'g1',
    'type' => 'title',
  ) );

  $post_instructions->add_field( array(
    'name' => 'Post titling',
    'desc' => 'The post title should be according to the AP titling guidelines. This should happen automatically after you\'ve entered the title.<br/>The titles should not contain any all caps words. The titles should not use abbreviations e.g. w/ or inc.<br/>The title should not contain extra data like the show name or date, if different display is needed then make a request to the Digital Team',
    'id'   => $prefix . 'g2',
    'type' => 'title',
  ) );

  $post_instructions->add_field( array(
    'name' => 'Categories, tags, focuses & sections',
    'desc' => 'Every post should have a top level category: Articles, Audio or Video. Then most posts should have a second category for the show or content type.<br/>A top level section should be chosen; 2 or possibly 3 top level sections are allowed—especially when associating geography as well as content. Sub sections should also be chosen for each top level; multiples are possible inside a top level but no more than 3. If nothing is relevant then don\'t add a new section but discuss with the head of your production section.<br/>Tags are not essential but desirable. Not too many are needed: 5-10. They should cover general themes, topics, and persons relevant.',
    'id'   => $prefix . 'g3',
    'type' => 'title',
  ) );

  $post_instructions->add_field( array(
    'name' => 'Post meta',
    'desc' => 'Every post should have a short description. This is the summary of the content but not the full content. It should be a single paragraph of a few sentences. It should not contain links or credits.<br/>It is preferable to set related posts when possible. When set they will be shown beneath the content. Max 3.<br/>',
    'id'   => $prefix . 'g4',
    'type' => 'title',
  ) );

  $post_instructions->add_field( array(
    'name' => 'Featured image',
    'desc' => 'Every post needs a featured image. This is the key image displayed for the post on the website and when the post is shared. This image should almost always be a photograph. This image should never be a screenshot or a found graphic. In certain circumstances it will be a composite image. It should not contain the NM logomark nor any other graphical text. It should stand alone as an illustrative piece. It should not necessarily be the same as a YouTube thumbnail. The tone of the website is different so the language we use is different.',
    'id'   => $prefix . 'g5',
    'type' => 'title',
  ) );

}
