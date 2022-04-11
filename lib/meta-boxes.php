<?php
/**
 * Metabox for Page Slug
 * @author Tom Morton
 * @link https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-show_on-filters
 *
 * @param bool $display
 * @param array $meta_box
 * @return bool display metabox
 */
function be_metabox_show_on_slug($display, $meta_box)
{
    if (! isset($meta_box['show_on']['key'], $meta_box['show_on']['value'])) {
        return $display;
    }

    if ('slug' !== $meta_box['show_on']['key']) {
        return $display;
    }

    $post_id = 0;

    // If we're showing it based on ID, get the current ID
    if (isset($_GET['post'])) {
        $post_id = $_GET['post'];
    } elseif (isset($_POST['post_ID'])) {
        $post_id = $_POST['post_ID'];
    }

    if (! $post_id) {
        return $display;
    }

    $slug = get_post($post_id)->post_name;

    // See if there's a match
    return in_array($slug, (array) $meta_box['show_on']['value']);
}
add_filter('cmb2_show_on', 'be_metabox_show_on_slug', 10, 2);

/* Get post objects for select field options */
function get_post_objects($query_args)
{
    $args = wp_parse_args($query_args, array(
    'post_type' => 'post',
  ));
    $posts = get_posts($args);
    $post_options = array();
    if ($posts) {
        foreach ($posts as $post) {
            $post_options [ $post->ID ] = $post->post_title;
        }
    }
    return $post_options;
}

/**
* Registers CMB2 group fields to capture roles and persons assigned for the about page.
*
* @param class $cmbInstance The CMB2 instance to register the group field with.
* @param integer $numberOfGroups The number of groups of this type to be registered.
* @param string $title The title of these group fields.
* @param string $label The description of these group fields.
*/
function createAboutColumnGroupFields($cmbInstance, $numberOfGroups, $title, $label)
{
    for ($i = 0; $i < $numberOfGroups; $i++) {
        $group_field = $cmbInstance->add_field(array(
      'id'          => 'about_page_team_group_' . sanitize_title($title) . '-' . ($i + 1),
      'type'        => 'group',
      'name'        => $title . ' (column ' . ($i + 1) . ')',
      'description' => __($label, 'cmb2'),
      'options'     => array(
        'group_title'       => __('Entry {#}', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
        'add_button'        => __('Add Another Entry', 'cmb2'),
        'remove_button'     => __('Remove Entry', 'cmb2'),
        'sortable'          => true,
      ),
    ));

        $cmbInstance->add_group_field($group_field, array(
      'name' => 'Role',
      'id'   => 'title',
      'type' => 'text',
    ));

        $cmbInstance->add_group_field($group_field, array(
      'name' => 'Name',
      'id'   => 'name',
      'type' => 'textarea_code',
      'repeatable' => true,
    ));
    }
}

/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */
/**
 * Hook in and add metaboxes. Can only happen on the 'cmb2_init' hook.
 */

add_action('cmb2_init', 'igv_cmb_metaboxes');

function igv_cmb_metaboxes()
{
    // Start with an underscore to hide fields from custom fields list
    $prefix = '_cmb_';

    $meta_boxes = new_cmb2_box(array(
    'id'         => 'post_metabox',
    'title'      => __('Post Meta', 'cmb'),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ));

    $meta_boxes->add_field(array(
    'name'    => __('Short description', 'cmb'),
    'desc'    => __('...', 'cmb'),
    'id'      => $prefix . 'short_desc',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ));

    $meta_boxes->add_field(array(
    'name'    => __('Related Posts', 'cmb'),
    'desc'    => __('If set will show related posts at the bottom of the post. Max 3 shown(optional)', 'cmb'),
    'id'      => $prefix . 'related_posts',
    'type'    => 'post_search_text',
    'post_type'   => array('post'),
    'select_behavior' => 'add',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Misc download', 'cmb'),
    'desc' => __('Upload an file or enter a URL.', 'cmb'),
    'id'   => $prefix . 'dl',
    'type' => 'file',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Alternative social sharing thumbnail', 'cmb'),
    'desc' => __('This image will override the thumbnail as the image shown on social media when sharing. (optional)', 'cmb'),
    'id'   => $prefix . 'alt_social',
    'type' => 'file',
  ));

    $meta_boxes->add_field(array(
    'name'    => __('Support box override', 'cmb'),
    'desc'    => __('If set this will override any red outlined support boxes on the single post page(optional)', 'cmb'),
    'id'      => $prefix . 'support_box_override',
    'type'    => 'textarea_small',
  ));

    // FM

    $audio_metabox = new_cmb2_box(array(
    'id'         => 'fm_metabox',
    'title'      => __('Audio Meta', 'cmb'),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ));

    $audio_metabox->add_field(array(
    'name' => __('Soundcloud URL', 'cmb'),
    'desc' => __('Enter a URL.', 'cmb'),
    'id'   => $prefix . 'sc',
    'type' => 'text_url',
  ));

    $audio_metabox->add_field(array(
    'name' => __('Is a Resonance show?', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'is_resonance',
    'type' => 'checkbox',
  ));

    $audio_metabox->add_field(array(
    'name' => __('Download URL', 'cmb'),
    'desc' => __('Enter a URL.', 'cmb'),
    'id'   => $prefix . 'dl_mp3',
    'type' => 'text_url',
  ));

    $audio_metabox->add_field(array(
    'name' => __('Transcript', 'cmb'),
    'desc' => __('...', 'cmb'),
    'id'   => $prefix . 'transcript',
    'type' => 'wysiwyg',
  ));

    // TV

    $video_metabox = new_cmb2_box(array(
    'id'         => 'video_metabox',
    'title'      => __('Video Meta', 'cmb'),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ));

    $video_metabox->add_field(array(
    'name' => __('YouTube ID', 'cmb'),
    'desc' => __('Id of youtube video. for example if this is the url https://www.youtube.com/watch?v=CmuDcXfBqTg&feature=c4-overview&list=UUOzMAa6IhV6uwYQATYG_2kg then the Id is the value after the ?v= and before the &, for this link CmuDcXfBqTg', 'cmb'),
    'id'   => $prefix . 'utube',
    'type' => 'text',
  ));

    $video_metabox->add_field(array(
    'name' => __('Alternate thumbnail', 'cmb'),
    'desc' => __('Without any text. Just an image', 'cmb'),
    'id'   => $prefix . 'alt_thumb',
    'type' => 'file',
  ));

    // Articles

    $articles_metabox = new_cmb2_box(array(
    'id'         => 'articles_metabox',
    'title'      => __('Articles Meta', 'cmb'),
    'object_types'      => array( 'post' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
    'show_in_rest' => WP_REST_Server::READABLE,
  ));

    $articles_metabox->add_field(array(
    'name' => __('Standfirst', 'cmb'),
    'id'   => $prefix . 'standfirst',
    'type' => 'textarea',
  ));

    $articles_metabox->add_field(array(
    'name' => __('Author', 'cmb'),
    'id'   => $prefix . 'author',
    'type' => 'text',
  ));

    $articles_metabox->add_field(array(
    'name' => __('Author Twitter', 'cmb'),
    'desc' => __('Optional. No @. For multiple authors add extra rows', 'cmb'),
    'id'   => $prefix . 'author_twitter',
    'type' => 'text',
    'repeatable' => true,
  ));

    $articles_metabox->add_field(array(
    'name' => __('Layout', 'cmb'),
    'id'   => $prefix . 'article_layout',
    'type' => 'radio',
    'show_option_none' => false,
    'options'          => array(
      'basic' => __('Basic', 'cmb2'),
      'basic-no-image'     => __('Basic (no image)', 'cmb2'),
      'large-image'   => __('Large splashed image', 'cmb2'),
    ),
  ));

    // Resources

    $resources_metabox = new_cmb2_box(array(
    'id'         => 'resources_metabox',
    'title'      => __('Post Resources', 'cmb'),
    'object_types' => array( 'post' ),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true,
    'show_in_rest' => WP_REST_Server::READABLE,
  ));

    $resources_group_field = $resources_metabox->add_field(array(
    'id'          => $prefix . 'resources',
    'type'        => 'group',
    'options'     => array(
      'group_title'   => __('Resource {#}', 'cmb'), // {#} gets replaced by row number
      'add_button'    => __('Add Another Resource', 'cmb'),
      'remove_button' => __('Remove Resource', 'cmb'),
      'sortable'      => true,
    ),
  ));

    $resources_metabox->add_group_field($resources_group_field, array(
    'name' => 'Resource Title',
    'id'   => 'title',
    'type' => 'text',
  ));

    $resources_metabox->add_group_field($resources_group_field, array(
    'name' => 'Resource Link',
    'id'   => 'link',
    'type' => 'text_url',
  ));

    // Event

    $meta_boxes = new_cmb2_box(array(
    'id'         => 'event_metabox',
    'title'      => __('Event Meta', 'cmb'),
    'object_types'      => array( 'event' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ));

    $meta_boxes->add_field(array(
    'name' => __('Event time', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'time',
    'type' => 'text_datetime_timestamp',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Venue name', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'venue_name',
    'type' => 'text',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Venue postcode', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'venue_postcode',
    'type' => 'text',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Speakers', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'speakers',
    'type' => 'text',
    'repeatable' => true,
  ));

    $meta_boxes->add_field(array(
    'name' => __('Host', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'host',
    'type' => 'text',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Tickets link', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'tickets',
    'type' => 'text_url',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Sold out', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'tickets_sold_out',
    'type' => 'checkbox',
  ));

    $meta_boxes->add_field(array(
    'name' => __('YouTube recording', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'youtube',
    'type' => 'text',
  ));

    $meta_boxes->add_field(array(
    'name' => __('Gallery', 'cmb'),
    'desc' => __('', 'cmb'),
    'id'   => $prefix . 'gallery',
    'type' => 'wysiwyg',
  ));

    // Page

    $page_meta_boxes = new_cmb2_box(array(
    'id'         => 'page_metabox',
    'title'      => __('Page Meta', 'cmb'),
    'object_types'      => array( 'page' ), // Post type
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ));

    $page_meta_boxes->add_field(array(
    'name'    => __('2nd Column', 'cmb'),
    'desc'    => __('(optional) (on Support page this shows under the Already a supporter? heading)', 'cmb'),
    'id'      => $prefix . 'page_extra',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ));

    $page_meta_boxes->add_field(array(
    'name'    => __('Extra Section Title', 'cmb'),
    'desc'    => __('(for About page)', 'cmb'),
    'id'      => $prefix . 'page_extra_section_title',
    'type'    => 'text',
  ));

    $page_meta_boxes->add_field(array(
    'name'    => __('Extra Section', 'cmb'),
    'desc'    => __('(for About page)', 'cmb'),
    'id'      => $prefix . 'page_extra_section',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ));

    // Page: Support

    $support_page_meta_boxes = new_cmb2_box(array(
    'id'         => 'supper_page_metabox',
    'title'      => __('Support Page Meta', 'cmb'),
    'object_types'      => array( 'page' ), // Post type
    'show_on' => array('key' => 'slug', 'value' => 'support'),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ));

    $support_page_meta_boxes->add_field(array(
    'name'    => __('Youtube video', 'cmb'),
    'desc'    => __('(optional)', 'cmb'),
    'id'      => $prefix . 'support_youtube',
    'type'    => 'text',
  ));

    // Page: About

    $about_page_meta_boxes = new_cmb2_box(array(
    'id'         => 'about_page_meta',
    'title'      => __('About Page Meta', 'cmb'),
    'object_types' => array( 'page' ), // Post type
    'show_on' => array('key' => 'slug', 'value' => 'about'),
    'context'    => 'normal',
    'priority'   => 'high',
    'show_names' => true, // Show field names on the left
  ));

    $about_page_meta_boxes->add_field(array(
    'name'    => __('Team and Associates sections', 'cmb'),
    'id'      => $prefix . 'about_title_memebers',
    'type'    => 'title',
  ));

    createAboutColumnGroupFields($about_page_meta_boxes, 4, 'Team roles and members', 'Use an entry for each role and fill with member names');

    createAboutColumnGroupFields($about_page_meta_boxes, 2, 'Associates roles and names', 'Use an entry for each role and fill with associate names');

    $about_page_meta_boxes->add_field(array(
    'name'    => __('Contact and information section', 'cmb'),
    'id'      => $prefix . 'about_title_contact',
    'type'    => 'title',
  ));

    $contact_group_field = $about_page_meta_boxes->add_field(array(
    'id'          => 'about_page_contact_group',
    'type'        => 'group',
    'name'        => 'Contact links',
    'description' => __('Contact and information links go here', 'cmb2'),
    'options'     => array(
      'group_title'       => __('Entry {#}', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
      'add_button'        => __('Add Another Entry', 'cmb2'),
      'remove_button'     => __('Remove Entry', 'cmb2'),
      'sortable'          => true,
    ),
  ));

    $about_page_meta_boxes->add_group_field($contact_group_field, array(
    'name' => 'Title',
    'id'   => 'title',
    'type'    => 'text',
  ));

    $about_page_meta_boxes->add_group_field($contact_group_field, array(
    'name' => 'Email',
    'description' => 'If set this link will create an email to this address',
    'id'   => 'email',
    'type'    => 'text_email',
  ));

    $about_page_meta_boxes->add_group_field($contact_group_field, array(
    'name' => 'Link',
    'id'   => 'link',
    'type' => 'post_search_text',
    'post_type'   => array('page', 'notice'),
    'select_behavior' => 'replace',
  ));

    $about_page_meta_boxes->add_field(array(
    'name'    => __('Funding Section', 'cmb'),
    'id'      => $prefix . 'about_funding',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ));

    $funding_group_field = $about_page_meta_boxes->add_field(array(
    'id'          => 'about_page_funding_group',
    'type'        => 'group',
    'name'        => 'Grant funds',
    'description' => __('Add specific grant and project funding here', 'cmb2'),
    'options'     => array(
      'group_title'       => __('Entry {#}', 'cmb2'), // since version 1.1.4, {#} gets replaced by row number
      'add_button'        => __('Add Another Entry', 'cmb2'),
      'remove_button'     => __('Remove Entry', 'cmb2'),
      'sortable'          => true,
    ),
  ));

    $about_page_meta_boxes->add_group_field($funding_group_field, array(
    'name' => 'Text',
    'id'   => 'text',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 3, ),
  ));

    $about_page_meta_boxes->add_group_field($funding_group_field, array(
    'name' => 'Image',
    'id'   => 'image',
    'type'    => 'file',
  ));

    $about_page_meta_boxes->add_field(array(
    'name'    => __('Regulation Section', 'cmb'),
    'id'      => $prefix . 'about_regulation',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ));

    $about_page_meta_boxes->add_field(array(
    'name'    => __('Legal Section', 'cmb'),
    'id'      => $prefix . 'about_legal',
    'type'    => 'wysiwyg',
    'options' => array( 'textarea_rows' => 5, ),
  ));
}
