<?php

/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class IGV_Admin {

  /**
    * Option key, and option page slug
    * @var string
    */
  private $key = 'IGV_options';

  /**
    * Option prefix
    * @var string
    */
  private $prefix = '_igv_';

  /**
    * Options page metabox id
    * @var string
    */
  private $metabox_id = 'IGV_option_metabox';

  /**
   * Options Page title
   * @var string
   */
  protected $title = 'Site Options';

  /**
   * Options Page hook
   * @var string
   */
  protected $options_page = '';

  /**
   * Constructor
   * @since 0.1.0
   */
  public function __construct() {
    // Set our title
    $this->title = __( 'Site Options', 'IGV' );
  }

  /**
   * Initiate our hooks
   * @since 0.1.0
   */
  public function hooks() {
    add_action( 'admin_init', array( $this, 'init' ) );
    add_action( 'admin_menu', array( $this, 'add_options_page' ) );
    add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
  }


  /**
   * Register our setting to WP
   * @since  0.1.0
   */
  public function init() {
    register_setting( $this->key, $this->key );
  }

  /**
   * Add menu options page
   * @since 0.1.0
   */
  public function add_options_page() {
    $this->options_page = add_menu_page( $this->title, $this->title, 'edit_posts', $this->key, array( $this, 'admin_page_display' ) );
  }

  /**
   * Admin page markup. Mostly handled by CMB2
   * @since  0.1.0
   */
  public function admin_page_display() {
    ?>
    <div class="wrap cmb2-options-page <?php echo $this->key; ?>">
      <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
      <?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
    </div>
    <?php
  }

  /**
   * Add the options metabox to the array of metaboxes
   * @since  0.1.0
   */
  function add_options_page_metabox() {

    $options_metabox = new_cmb2_box( array(
      'id'      => $this->metabox_id,
      'hookup'  => false,
      'show_on' => array(
        // These are important, don't remove
        'key'   => 'options-page',
        'value' => array( $this->key, )
      ),
    ) );

    // FRONT PAGE OPTIONS

    $options_metabox->add_field( array(
      'name' => __( 'Front Page Options', 'cmb2' ),
      'desc' => __( '', 'cmb2' ),
      'id'   => $this->prefix . 'home_title',
      'type' => 'title',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Show radio player when FM show is live', 'IGV' ),
      'desc' => __( '...', 'IGV' ),
      'id'   => $this->prefix . 'home_radio',
      'type' => 'checkbox',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Home Focus', 'IGV' ),
      'desc' => __( 'a Focus to show (optional)', 'IGV' ),
      'id'   => $this->prefix . 'home_focus',
      'type' => 'select',
      'options_cb' => 'home_focus_list',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Show home Focus at the top', 'IGV' ),
      'desc' => __( 'but under a featured post if one is set', 'IGV' ),
      'id'   => $this->prefix . 'home_focus_at_top',
      'type' => 'checkbox',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Show #IMO on home', 'IGV' ),
      'desc' => __( '...', 'IGV' ),
      'id'   => $this->prefix . 'show_imo',
      'type' => 'checkbox',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Front Page Featured', 'IGV' ),
      'desc' => __( '...', 'IGV' ),
      'id'   => $this->prefix . 'front_feature',
      'type' => 'post_search_text',
      'post_type'   => 'post',
      'select_behavior' => 'replace',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Show Special Embed', 'IGV' ),
      'desc' => __( 'Shows the special embed on the homepage', 'IGV' ),
      'id'   => $this->prefix . 'show_special',
      'type' => 'checkbox',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Front Page Special Embed', 'IGV' ),
      'desc' => __( 'This is an embed code that takes over the home. For things like a Facebook livestream. Generally not to be used', 'IGV' ),
      'id'   => $this->prefix . 'front_special',
      'type' => 'textarea_code',
    ) );

    // FRONT PAGE WEEKLYS SECTION

    $options_metabox->add_field( array(
      'name' => __( 'Front Page Weeklys Signup Options', 'cmb2' ),
      'desc' => __( 'Up to 4 signup blocks', 'cmb2' ),
      'id'   => $this->prefix . 'home_signups_title',
      'type' => 'title',
    ) );

    $group_field_id = $options_metabox->add_field( array(
    	'id'          => 'home_signups',
    	'type'        => 'group',
    	'description' => __( 'Signup sections on the homepage', 'cmb2' ),
    	'options'     => array(
    		'group_title'       => __( 'Entry {#}', 'cmb2' ),
    		'add_button'        => __( 'Add Another Entry', 'cmb2' ),
    		'remove_button'     => __( 'Remove Entry', 'cmb2' ),
    		'sortable'          => true,
    	),
    ) );

    $options_metabox->add_group_field( $group_field_id, array(
    	'name' => 'Signup Title',
    	'id'   => 'title',
    	'type' => 'text',
    ) );

    $options_metabox->add_group_field( $group_field_id, array(
    	'name' => 'Link',
    	'id'   => 'link',
    	'type' => 'text_url',
    ) );

    $options_metabox->add_group_field( $group_field_id, array(
    	'name' => 'Copy',
    	'description' => 'Short copy',
    	'id'   => 'description',
    	'type' => 'wysiwyg',
    ) );

    $options_metabox->add_group_field( $group_field_id, array(
    	'name' => 'Alt Signup Text',
    	'description' => __( 'Default is "Sign up here"', 'cmb2' ),
    	'id'   => 'signup_text',
    	'type' => 'text',
    ) );

    $options_metabox->add_group_field( $group_field_id, array(
    	'name' => 'Image',
    	'id'   => 'image',
    	'type' => 'file',
    ) );

    // ANNOUNCEMENT OPTIONS

    $options_metabox->add_field( array(
      'name' => __( 'Announcement Options', 'cmb2' ),
      'id'   => $this->prefix . 'announcement_title',
      'type' => 'title',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Expiration time', 'IGV' ),
      'desc' => __( 'Announcement will show if this value is set and in the future', 'IGV' ),
      'id'   => $this->prefix . 'announcement_time',
      'type' => 'text_datetime_timestamp',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Internal link', 'IGV' ),
      'id'   => $this->prefix . 'announcement_link',
      'type' => 'post_search_text',
      'post_type'   => array('post', 'page', 'event', 'notice'),
      'select_behavior' => 'replace',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'External link', 'IGV' ),
      'id'   => $this->prefix . 'announcement_link_ext',
      'type' => 'text_url',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Text', 'IGV' ),
      'id'   => $this->prefix . 'announcement_text',
      'type' => 'wysiwyg',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Image', 'IGV' ),
      'id'   => $this->prefix . 'announcement_image',
      'type' => 'file',
    ) );

    // FUNDRAISER OPTIONS

    $options_metabox->add_field( array(
      'name' => __( 'Fundraiser Options', 'cmb2' ),
      'id'   => $this->prefix . 'fundraiser_title',
      'type' => 'title',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Expiration time', 'IGV' ),
      'desc' => __( 'Fundraiser will show if this value is set and in the future', 'IGV' ),
      'id'   => $this->prefix . 'fundraiser_end_time',
      'type' => 'text_datetime_timestamp',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Youtube ID', 'IGV' ),
      'desc' => __( 'ID of Youtube video to show on home', 'IGV' ),
      'id'   => $this->prefix . 'fundraiser_youtube_id',
      'type' => 'text',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Support section override text', 'IGV' ),
      'desc' => __( 'Shows in place of Support Us on the support section sidewide', 'IGV' ),
      'id'   => $this->prefix . 'fundraiser_form_text',
      'type' => 'text',
    ) );

    // SOCIAL MEDIA OPTIONS

    $options_metabox->add_field( array(
      'name' => __( 'Social Media', 'cmb2' ),
      'desc' => __( 'urls and accounts for different social media platforms. For use in menus and metadata', 'cmb2' ),
      'id'   => $this->prefix . 'socialmedia_title',
      'type' => 'title',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Facebook Page URL', 'IGV' ),
      'desc' => __( '', 'IGV' ),
      'id'   => $this->prefix . 'socialmedia_facebook_url',
      'type' => 'text',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Twitter Account Handle', 'IGV' ),
      'desc' => __( '', 'IGV' ),
      'id'   => $this->prefix . 'socialmedia_twitter',
      'type' => 'text',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Instagram Account Handle', 'IGV' ),
      'desc' => __( '', 'IGV' ),
      'id'   => $this->prefix . 'socialmedia_instagram',
      'type' => 'text',
    ) );

    // METADATA OPTIONS

    $options_metabox->add_field( array(
      'name' => __( 'Metadata options', 'cmb2' ),
      'desc' => __( 'Settings relating to open graph, facebook and twitter sharing, and other social media metadata', 'cmb2' ),
      'id'   => $this->prefix . 'og_title',
      'type' => 'title',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Open Graph image', 'IGV' ),
      'desc' => __( 'primarily displayed on Facebook, but other locations as well', 'IGV' ),
      'id'   => $this->prefix . 'og_image',
      'type' => 'file',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Logo', 'IGV' ),
      'desc' => __( '(options) ', 'IGV' ),
      'id'   => $this->prefix . 'metadata_logo',
      'type' => 'file',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Facebook App ID', 'IGV' ),
      'desc' => __( '(optional)', 'IGV' ),
      'id'   => $this->prefix . 'og_fb_app_id',
      'type' => 'text',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Google Tag Manager container ID', 'IGV' ),
      'desc' => __( '(optional)', 'IGV' ),
      'id'   => $this->prefix . 'gtm_id',
      'type' => 'text',
    ) );

    $options_metabox->add_field( array(
      'name' => __( 'Cookies/Privacy notice', 'IGV' ),
      'desc' => __( 'The copy that goes in the box people have to agree to to allow cookies', 'IGV' ),
      'id'   => $this->prefix . 'privacy_notice',
      'type' => 'wysiwyg',
    ) );

  }

  /**
   * Public getter method for retrieving protected/private variables
   * @since  0.1.0
   * @param  string  $field Field to retrieve
   * @return mixed          Field value or exception is thrown
   */
  public function __get( $field ) {
    // Allowed fields to retrieve
    if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
      return $this->{$field};
    }

    throw new Exception( 'Invalid property: ' . $field );
  }

}

/**
 * Helper function to get/return the IGV_Admin object
 * @since  0.1.0
 * @return IGV_Admin object
 */
function IGV_admin() {
  static $object = null;
  if ( is_null( $object ) ) {
    $object = new IGV_Admin();
    $object->hooks();
  }

  return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function IGV_get_option( $key = '' ) {
  return cmb2_get_option( IGV_admin()->key, $key );
}

// Get it started
IGV_admin();
