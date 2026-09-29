<?php
add_action( 'cmb2_init', 'nm_cmb_post_episode_metaboxes' );

/**
 * Sanitise a CMB2 number field to a positive integer string.
 * Returns '' for blank or invalid input so CMB2 deletes the meta
 * rather than storing it. filter_var trims surrounding whitespace.
 *
 * @param mixed $value Raw submitted value.
 * @return string
 */
function nm_sanitize_positive_int_meta( $value ) {
  $int = filter_var( $value, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );

  return false === $int ? '' : (string) $int;
}

/**
 * Declares the Season / Episode box for posts that are part of an episodic
 * series (capsule podcasts, seasonal shows, article series). The show is the
 * post's category; these fields only number the post within it.
 *
 * Templates format the values themselves; there is deliberately no shared
 * label helper. Order queries by date, never by _nm_episode: bonus and
 * trailer posts have a label but no number.
 */
function nm_cmb_post_episode_metaboxes() {
  $prefix = '_nm_';

  $cmb_episode = new_cmb2_box( array(
    'id'           => $prefix . 'episode_post_edit',
    'title'        => esc_html__( 'Season / Episode', 'cmb2' ),
    'object_types' => array( 'post' ),
    'context'      => 'side',
    'priority'     => 'default',
  ) );

  $cmb_episode->add_field( array(
    'name'            => esc_html__( 'Season', 'cmb2' ),
    'desc'            => esc_html__( 'Leave blank unless this post is part of an episodic series. Required if an episode number or label is set.', 'cmb2' ),
    'id'              => $prefix . 'season',
    'type'            => 'text_small',
    'sanitization_cb' => 'nm_sanitize_positive_int_meta',
    'attributes'      => array(
      'type'                          => 'number',
      'min'                           => '1',
      'step'                          => '1',
      'data-validation'               => 'true',
      'data-validation-required-with' => $prefix . 'episode,' . $prefix . 'episode_label',
    ),
  ) );

  $cmb_episode->add_field( array(
    'name'            => esc_html__( 'Episode number', 'cmb2' ),
    'id'              => $prefix . 'episode',
    'type'            => 'text_small',
    'sanitization_cb' => 'nm_sanitize_positive_int_meta',
    'attributes'      => array(
      'type' => 'number',
      'min'  => '1',
      'step' => '1',
    ),
  ) );

  $cmb_episode->add_field( array(
    'name'            => esc_html__( 'Episode label', 'cmb2' ),
    'desc'            => esc_html__( 'For posts that are not a numbered episode, e.g. "Bonus 1", "Trailer", "Credits". Shown instead of the number when set.', 'cmb2' ),
    'id'              => $prefix . 'episode_label',
    'type'            => 'text',
    'sanitization_cb' => 'sanitize_text_field',
  ) );
}
