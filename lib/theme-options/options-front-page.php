<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Returns a slug indexed array of all the Audio sub categories for use in select metaboxes
 */
function get_audio_categories_metabox_array() {
  $audio_category = get_category_by_slug( 'audio' );

  if ( ! $audio_category ) {
    return;
  }

  $terms = get_terms(
    array(
      'taxonomy' => 'category',
      'parent'   => $audio_category->term_id, // Use 'parent' instead of 'child_of'
    )
  );

  $return = array();
  $return['none'] = 'None';

  if ( is_wp_error( $terms ) || empty( $terms ) ) {
    return $return;
  }

  foreach ( $terms as $term ) {
    $return[ $term->slug ] = $term->name; // created slug indexed array of categories names
  }

  return $return;
}

/**
 * Returns a slug indexed array of all the Sections taxonomy options for use in select metaboxes
 */
function get_all_theme_sections_array() {
  $terms = get_terms(
    array(
      'taxonomy' => 'section',
    )
  );

  $return = array();
  $return['none'] = 'None';

  if ( is_wp_error( $terms ) || empty( $terms ) ) {
    return $return;
  }

  foreach ( $terms as $term ) {
    $return[ $term->term_id ] = $term->name;
  }

  return $return;
}

/**
 * Get newsletter signup options from the newsletter custom post type.
 *
 * @return array Array of newsletter signup options for banner selection.
 */
function get_newsletter_signup_options() {
  $newsletter_options = array();

  $newsletters = get_posts(
    array(
      'post_type'      => 'newsletter',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'title',
      'order'          => 'ASC',
    )
  );

  if ( $newsletters ) {
    foreach ( $newsletters as $newsletter ) {
      $meta = get_post_meta( $newsletter->ID );
      $mailchimp_key = ! empty( $meta['_nm_mailchimp_key'] ) ? $meta['_nm_mailchimp_key'][0] : false;

      if ( $mailchimp_key ) {
        $newsletter_options[ 'newsletter-signup-' . $newsletter->ID ] = 'Newsletter signup: ' . esc_html( $newsletter->post_title );
      }
    }
  }

  return $newsletter_options;
}

/**
 * Returns the banner options map ( banner key => label ) used by both the
 * legacy banner selects and the front-page block registry.
 *
 * Includes the static banner partials plus the dynamic newsletter-signup
 * options. The `false => 'None'` entry is only meaningful for the legacy
 * single-banner selects; the full registry skips it.
 *
 * Called only on admin screens — by the legacy banner selects and by
 * nm_get_front_page_block_registry() when building the Layout select options.
 * The front-end render path routes banners by slug prefix and never calls this,
 * so its get_newsletter_signup_options() DB query stays off front-page requests.
 * Still cached per request since the admin select and legacy selects both build
 * the list.
 *
 * @return array
 */
function nm_get_front_page_banner_options() {
  static $options = null;

  if ( $options !== null ) {
    return $options;
  }

  $banner_options = array(
    false                                              => 'None',
    'partials/support-section'                         => 'Support section',
    'partials/specials/banners/support-video'          => 'Support Video',
    'partials/specials/banners/podcast-death-in-westminster' => 'Podcast: Death in Westminster',
    'partials/specials/banners/podcast-committed'      => 'Podcast: Committed',
    'partials/specials/banners/podcast-if-i-speak'     => 'Podcast: If I Speak',
    'partials/specials/banners/focus-pro-rev-soccer'   => 'Focus: Pro Rev Soccer',
    'partials/specials/banners/podcast-foreign-agent'  => 'Podcast: Foreign Agent',
    'partials/specials/banners/focus-doing-it-right-sex-on-the-left' => 'Focus: Doing It Right: Sex On The Left',
    'partials/specials/banners/focus-breaking-britain' => 'Focus: Breaking Britain',
    'partials/specials/banners/focus-disability-its-political' => 'Focus: Disability: It’s Political',
    'partials/specials/banners/podcast-planet-b'       => 'Podcast: Planet B',
    'partials/specials/banners/survey-link'            => 'Audience Survey 2026',
  );

  $options = array_merge( $banner_options, get_newsletter_signup_options() );

  return $options;
}

/**
 * Returns the product-block half of the registry, keyed by stable slug.
 *
 * Singleton partials rendered via get_template_part. Most are arg-less; the
 * render loop passes a shared context array (see nm_render_front_page_block())
 * so blocks that need it — e.g. the highlight section, which dedupes against
 * the above-the-fold posts — can read it. Their own content config lives on
 * their existing settings subpages.
 *
 * This is intentionally DB-free and is the only registry half consulted on the
 * front-end render path. Banners are routed by slug prefix at render time (see
 * nm_render_front_page_block()), so the newsletter-options query never runs on
 * a front-page request.
 *
 * @return array<string, array{label:string, partial:string, type:string}>
 */
function nm_get_front_page_product_blocks() {
  $blocks = array(
    'highlight-block' => array(
      'label'   => 'Show block: Highlight section (configured on its own subpage)',
      'partial' => 'partials/front-page/highlight-block',
      'type'    => 'product',
    ),
    'novara-live'     => array(
      'label'   => 'Show block: Novara Live',
      'partial' => 'partials/front-page/show-blocks/novara-live',
      'type'    => 'product',
    ),
    'dyor'            => array(
      'label'   => 'Show block: Do Your Own Research',
      'partial' => 'partials/front-page/show-blocks/dyor',
      'type'    => 'product',
    ),
    'dyor-alt'        => array(
      'label'   => 'Show block: Do Your Own Research (ALT — design comparison)',
      'partial' => 'partials/front-page/show-blocks/dyor-alt',
      'type'    => 'product',
    ),
    'audio'           => array(
      'label'   => 'Show block: Audio (Novara FM + ACFM)',
      'partial' => 'partials/front-page/show-blocks/audio',
      'type'    => 'product',
    ),
    'audio-acfm'      => array(
      'label'   => 'Show block: ACFM (standalone)',
      'partial' => 'partials/front-page/show-blocks/audio-acfm',
      'type'    => 'product',
    ),
    'downstream'      => array(
      'label'   => 'Show block: Downstream',
      'partial' => 'partials/front-page/show-blocks/downstream',
      'type'    => 'product',
    ),
  );

  // Do Your Own Research is still in development. Keep both DYOR blocks
  // available on local/development/staging but hide them on production — they
  // disappear from the admin Layout options and, because the front-end render
  // path looks blocks up in this same registry, never render even if a saved
  // production layout references them. See nm_render_front_page_block().
  if ( nm_is_production() ) {
    unset( $blocks['dyor'], $blocks['dyor-alt'] );
  }

  return $blocks;
}

/**
 * Returns the full block registry — product blocks plus every banner — keyed by
 * stable slug. Used to build the Layout select options in admin.
 *
 * Banners reuse the banner options map and carry the original banner `key`,
 * rendered via render_front_page_banner() (which handles newsletter signups,
 * retired-option no-ops and a path-traversal guard).
 *
 * NOTE: this enumerates banners, which triggers the newsletter-options DB
 * query. It is only called when building the admin select, never on the
 * front-end render path. Rendering uses nm_get_front_page_product_blocks()
 * plus prefix-based banner routing instead.
 *
 * Slugs are stable identifiers — saved layouts reference slugs, not labels or
 * indices, so relabelling or reordering the registry never corrupts a layout.
 *
 * @return array<string, array{label:string, partial:string, type:string, key?:string}>
 */
function nm_get_front_page_block_registry() {
  $blocks = nm_get_front_page_product_blocks();

  foreach ( nm_get_front_page_banner_options() as $key => $label ) {
    if ( ! $key ) {
      continue; // skip the 'None' entry
    }
    $blocks[ 'banner:' . $key ] = array(
      'label'   => 'Banner: ' . $label,
      'partial' => $key,
      'key'     => $key,
      'type'    => 'banner',
    );
  }

  return $blocks;
}

/**
 * Builds the select options ( slug => label ) for a Layout editor row from the
 * full block registry, prefixed with an empty placeholder. Admin-only.
 *
 * @return array
 */
function nm_get_front_page_layout_select_options() {
  $options = array( '' => '— Select a section —' );

  foreach ( nm_get_front_page_block_registry() as $slug => $block ) {
    $options[ $slug ] = $block['label'];
  }

  return $options;
}

/**
 * Returns the ordered front-page layout as an array of registry slugs.
 *
 * Falls back to a default seed reproducing the historic hardcoded order when no
 * layout has been saved. Computed on read (never written), so the migration is
 * non-destructive and reversible.
 *
 * @return string[]
 */
function nm_get_front_page_layout() {
  $saved = NM_get_option( 'nm_front_page_layout', 'nm_front_page_layout_options', array() );

  if ( is_array( $saved ) && ! empty( $saved ) ) {
    $slugs = array();

    foreach ( $saved as $row ) {
      if ( is_array( $row ) && ! empty( $row['block'] ) ) {
        $slugs[] = $row['block'];
      }
    }

    if ( ! empty( $slugs ) ) {
      return $slugs;
    }
  }

  return nm_get_front_page_default_layout();
}

/**
 * Default layout seed reproducing the historic hardcoded order:
 * banner 1, highlight section, Novara Live, banner 2, Audio, banner 3,
 * Downstream, banner 4.
 *
 * Reads the legacy banner option values; banner slots set to None are skipped.
 * The highlight section is included in its historic position but stays hidden
 * until enabled on its own settings subpage.
 *
 * @deprecated 4.7.0 No replacement — superseded by the saved layout
 *   (nm_get_front_page_layout()). Transitional migration shim: remove together
 *   with the legacy banner selects once a layout has been saved in production;
 *   after that, an empty layout should simply render nothing. Earliest removal
 *   4.11.0 (4 minors; see docs/deprecation.md).
 * @return string[]
 */
function nm_get_front_page_default_layout() {
  $banner_slug = function ( $option_key ) {
    $value = NM_get_option( $option_key );

    if ( ! $value || $value === '0' ) {
      return null;
    }

    return 'banner:' . $value;
  };

  $layout = array(
    $banner_slug( 'nm_front_page_banner_option_1' ),
    'highlight-block',
    'novara-live',
    $banner_slug( 'nm_front_page_banner_option_2' ),
    'audio',
    $banner_slug( 'nm_front_page_banner_option_3' ),
    'downstream',
    $banner_slug( 'nm_front_page_banner_option_4' ),
  );

  return array_values( array_filter( $layout ) );
}

/**
 * Renders a single front-page block by its layout slug.
 *
 * Banner slugs (`banner:<key>`) route through render_front_page_banner() using
 * the key alone — no registry lookup, so the newsletter-options query is never
 * triggered on the front end. render_front_page_banner() handles newsletter
 * signups, retired-option no-ops and a path-traversal guard. Product blocks are
 * looked up in the DB-free product registry and render their partial, receiving
 * the shared $context array as template args (blocks that don't need it ignore
 * it). Unknown slugs are ignored.
 *
 * @param string $slug    Layout slug.
 * @param array  $context Shared render context passed to product partials
 *                        (e.g. 'excluded_posts_ids' for the highlight section).
 * @return void
 */
function nm_render_front_page_block( $slug, $context = array() ) {
  if ( ! is_string( $slug ) || $slug === '' ) {
    return; // ignore empty or corrupted layout rows
  }

  $banner_prefix = 'banner:';

  if ( strpos( $slug, $banner_prefix ) === 0 ) {
    render_front_page_banner( substr( $slug, strlen( $banner_prefix ) ) );
    return;
  }

  $products = nm_get_front_page_product_blocks();

  if ( isset( $products[ $slug ] ) ) {
    get_template_part( $products[ $slug ]['partial'], null, $context );
  }
}

/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function nm_register_front_page_options_metabox() {
  $prefix = 'nm_';

  $banner_options = nm_get_front_page_banner_options();

  /**
   * Registers main options page menu item and form.
   */
  $main_options = new_cmb2_box(
    array(
      'id'           => 'nm_front_page_options',
      'title'        => 'Front Page',
      'object_types' => array( 'options-page' ),

      /*
      * The following parameters are specific to the options-page box
      * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
      */

      'option_key'       => 'nm_front_page_options', // The option key and admin menu page slug.
          'icon_url'     => 'dashicons-layout', // Menu icon. Only applicable if 'parent_slug' is left empty.
      // 'menu_title'      => esc_html__( 'Options', 'cmb2' ), // Falls back to 'title' (above).
      // 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
          'capability'   => 'edit_posts', // Cap required to view options-page.
      // 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
      // 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
      // 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
      // 'save_button'     => esc_html__( 'Save Theme Options', 'cmb2' ), // The text for the options-page save button. Defaults to 'Save'.
      // 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
      // 'message_cb'      => 'nm_options_page_message_callback',
    )
  );

  /**
   * Options fields ids only need
   * to be unique within this box.
   * Prefix is not needed.
   */
    $main_options->add_field(
        array(
            'name' => 'Front Page Settings',
            'desc' => 'This is where the various settings for the Front Page can be found and set. There are some subpages to these settings for specific features',
            'id'   => $prefix . 'front_page_settings_title',
            'type' => 'title',
        )
    );

    /**
     * Legacy banner slots.
     *
     * @deprecated 4.7.0 Superseded by the Front Page > Layout editor. Retained
     *   only as the source for nm_get_front_page_default_layout()'s seed until a
     *   layout is saved in production. Earliest removal 4.11.0 (4 minors; see
     *   docs/deprecation.md). Removal must follow a one-time Save on the Layout
     *   page in prod, alongside nm_get_front_page_default_layout().
     */
    $main_options->add_field(
        array(
            'name' => 'Adverts and banners (legacy)',
            'desc' => 'Deprecated — use the Layout page instead. These selects only seed the default Layout order until a layout is saved.',
            'id'   => $prefix . 'front_page_settings_banners_title',
            'type' => 'title',
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'First banner',
            'desc'    => 'Select the content of the banner.',
            'id'      => $prefix . 'front_page_banner_option_1',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'Second banner',
            'id'      => $prefix . 'front_page_banner_option_2',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'Third banner',
            'id'      => $prefix . 'front_page_banner_option_3',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name'    => 'Forth banner',
            'id'      => $prefix . 'front_page_banner_option_4',
            'type'    => 'select',
            'options' => $banner_options,
        )
    );

    $main_options->add_field(
        array(
            'name' => 'Live Schedule',
            'desc' => 'This is where overrides for the live schedule can be set. Can be deleted once in the past',
            'id'   => $prefix . 'front_page_settings_live_schedule_title',
            'type' => 'title',
        )
    );

    $schedule_overrides_group_field_id = $main_options->add_field(
        array(
            'id'          => $prefix . 'front_page_settings_live_schedule_overrides',
            'type'        => 'group',
            'description' => 'Overrides for the live schedule',
            'options'     => array(
                'group_title'   => __( 'Override {#}', 'nm' ),
                'add_button'    => __( 'Add Another Override', 'nm' ),
                'remove_button' => __( 'Remove Override', 'nm' ),
                'sortable'      => false,
            ),
        )
    );

    $main_options->add_group_field(
        $schedule_overrides_group_field_id,
        array(
            'name' => 'Start time',
            'id'   => 'start',
            'type' => 'text_datetime_timestamp',
        )
    );

    $main_options->add_group_field(
        $schedule_overrides_group_field_id,
        array(
            'name' => 'End time',
            'id'   => 'end',
            'type' => 'text_datetime_timestamp',
        )
    );

    $main_options->add_group_field(
        $schedule_overrides_group_field_id,
        array(
            'name'             => 'Status',
            'id'               => 'status',
            'type'             => 'select',
            'show_option_none' => false,
            'default'          => 'false',
            'options'          => array(
                'false' => __( 'Offline', 'cmb2' ),
                'true'  => __( 'Live', 'cmb2' ),
            ),
        )
    );

    $main_options->add_field(
        array(
            'name' => 'Offline messages',
            'id'   => $prefix . 'front_page_settings_live_schedule_offline_messages_title',
            'type' => 'title',
        )
    );

    $offline_messages_group_field_id = $main_options->add_field(
        array(
            'id'          => $prefix . 'front_page_settings_live_schedule_offline_messages',
            'type'        => 'group',
            'description' => 'Randomised messages that will be shown in the top bar of the desktop site when the livestream is offline',
            'options'     => array(
                'group_title'   => __( 'Message {#}', 'nm' ),
                'add_button'    => __( 'Add Another Message', 'nm' ),
                'remove_button' => __( 'Remove Message', 'nm' ),
                'sortable'      => false,
            ),
        )
    );

    $main_options->add_group_field(
        $offline_messages_group_field_id,
        array(
            'name' => 'Quote text',
            'id'   => 'text',
            'type' => 'text',
        )
    );

    $main_options->add_group_field(
        $offline_messages_group_field_id,
        array(
            'name' => 'Link',
            'id'   => 'link',
            'type' => 'text_url',
        )
    );

  /**
   * Register child page for above the fold featured.
   */
    $above_the_fold_featured = new_cmb2_box(
        array(
            'id'           => 'nm_above_the_fold_featured_options_page',
            'title'        => 'Above the Fold: Featured',
            'object_types' => array( 'options-page' ),
            'option_key'   => 'nm_front_page_above_the_fold_featured_options',
            'parent_slug'  => 'nm_front_page_options',
            'capability'   => 'edit_posts',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Above the fold: featured',
            'desc' => 'This is where the settings for the above the fold featured section can be set',
            'id'   => $prefix . 'above_the_fold_featured_options_title',
            'type' => 'title',
        )
    );

  // First featured post: The big one

    $above_the_fold_featured->add_field(
        array(
            'name' => 'First block: Main featured post',
            'desc' => 'This is the first featured post',
            'id'   => $prefix . 'above_the_fold_featured_1_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => 'Main featured post',
            'desc'            => 'Select the post to be primary featured post',
            'id'              => $prefix . 'above_the_fold_featured_1',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Show See Also section (optional)',
            'id'   => $prefix . 'above_the_fold_featured_1_show_related',
            'type' => 'checkbox',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'             => 'More On section to link (optional)',
            'desc'             => 'Select the thematic section to be linked to in the More On section.',
            'id'               => $prefix . 'above_the_fold_featured_1_more_on_section',
            'type'             => 'select',
            'show_option_none' => false,
            'options'          => get_all_theme_sections_array(),
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Link More On section to product (optional)',
            'desc' => 'If selected, the More On section will link to the product page of the selected product. This will override the above selection.',
            'id'   => $prefix . 'above_the_fold_featured_1_is_product_linked',
            'type' => 'checkbox',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Allow video embed (very optional)',
            'desc' => 'If selected, the featured post will allow for a video embed. This is only for very rare things like ElectionSesh',
            'id'   => $prefix . 'above_the_fold_featured_1_has_embed',
            'type' => 'checkbox',
        )
    );

    // Next 3 featured posts: The small ones

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Rest of first featured section',
            'desc' => 'These are the 3 other featured posts that will be shown in the first featured section',
            'id'   => $prefix . 'above_the_fold_featured_234_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '2nd featured post',
            'desc'            => 'Will display with thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_2',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '3rd featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_3',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '4th featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_4',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

  // Second featured block: First big one

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Second block: main featured post',
            'desc' => 'This is the first featured post in the second block. The 5th featured post overall',
            'id'   => $prefix . 'above_the_fold_featured_5_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => 'Main featured post',
            'desc'            => 'Select the post to be primary featured post',
            'id'              => $prefix . 'above_the_fold_featured_5',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Show See Also section (optional)',
            'id'   => $prefix . 'above_the_fold_featured_5_show_related',
            'type' => 'checkbox',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'             => 'More On section to link (optional)',
            'desc'             => 'Select the thematic section to be linked to in the More On section.',
            'id'               => $prefix . 'above_the_fold_featured_5_more_on_section',
            'type'             => 'select',
            'show_option_none' => false,
            'options'          => get_all_theme_sections_array(),
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Link More On section to product (optional)',
            'desc' => 'If selected, the More On section will link to the product page of the selected product. This will override the above selection.',
            'id'   => $prefix . 'above_the_fold_featured_5_is_product_linked',
            'type' => 'checkbox',
        )
    );

  // Next 3 featured posts: The small ones

    $above_the_fold_featured->add_field(
        array(
            'name' => 'Rest of second featured section',
            'desc' => 'These are the 3 other featured posts that will be shown in the second featured section',
            'id'   => $prefix . 'above_the_fold_featured_678_title',
            'type' => 'title',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '2nd featured post',
            'desc'            => 'Will display with thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_6',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '3rd featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_7',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

    $above_the_fold_featured->add_field(
        array(
            'name'            => '4th featured post',
            'desc'            => 'Displays without thumbnail',
            'id'              => $prefix . 'above_the_fold_featured_8',
            'type'            => 'post_search_text',
            'post_type'       => 'post',
            'select_type'     => 'radio',
            'select_behavior' => 'replace',
        )
    );

  /**
   * Register child page for the highlight section
   */
    $highlight_section = new_cmb2_box(
        array(
            'id'           => 'nm_highlight_section_options_page',
            'title'        => 'Highlight Section',
            'object_types' => array( 'options-page' ),
            'option_key'   => $prefix . 'front_page_highlight_section_options',
            'parent_slug'  => $prefix . 'front_page_options',
            'capability'   => 'edit_posts',
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Highlight Section',
            'desc' => 'This is where the settings for the front page Highlight Section can be set',
            'id'   => $prefix . 'front_page_highlight_section_options_title',
            'type' => 'title',
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Show section',
            'desc' => 'If selected, the Highlight Section will be displayed below the fold immediately after the support signup section',
            'id'   => $prefix . 'front_page_highlight_section_options_is_displayed',
            'type' => 'checkbox',
        )
    );

    $highlight_section->add_field(
        array(
            'name'             => 'Content thematic Section to display',
            'desc'             => 'Select the thematic section to display in the Highlight Section. Only posts in this Section will be shown',
            'id'               => $prefix . 'front_page_highlight_section_options_section',
            'type'             => 'select',
            'show_option_none' => false,
            'options'          => get_all_theme_sections_array(),
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Section title (optional)',
            'desc' => 'The text shown above the posts in the Highlight Section. If not set will default to the name of the Section',
            'id'   => $prefix . 'front_page_highlight_section_options_display_title',
            'type' => 'text',
        )
    );

    $highlight_section->add_field(
        array(
            'name' => 'Section description (optional)',
            'desc' => 'The text shown after the title. This should be no longer than 2 sentences',
            'id'   => $prefix . 'front_page_highlight_section_options_description',
            'type' => 'textarea_small',
        )
    );

    /**
     * Registers secondary options page, and set main item as parent.
     */
    $secondary_options = new_cmb2_box(
        array(
            'id'           => 'nm_secondary_options_page',
            'title'        => 'Products Bar',
            'object_types' => array( 'options-page' ),
            'option_key'   => 'nm_front_page_links_bar_options',
            'parent_slug'  => 'nm_front_page_options',
            'capability'   => 'edit_posts',
        )
    );

    $secondary_options->add_field(
        array(
            'name' => 'Front Page Product Links',
            'desc' => 'Blocks displayed in the top bar of the desktop site. Used to directly promote product offerings',
            'id'   => $prefix . 'home_signups_title',
            'type' => 'title',
        )
    );

    $group_field_id = $secondary_options->add_field(
        array(
            'id'          => $prefix . 'front_page_links_bar',
            'type'        => 'group',
            'description' => 'Product sections on the Front Page',
            'options'     => array(
                'group_title'   => __( 'Product Block {#}', 'nm' ),
                'add_button'    => __( 'Add Another Product Block', 'nm' ),
                'remove_button' => __( 'Remove Product Block', 'nm' ),
                'sortable'      => true,
            ),
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name' => 'Title',
            'id'   => 'title',
            'type' => 'text',
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name' => 'Link',
            'id'   => 'link',
            'type' => 'text_url',
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name'        => 'Copy',
            'description' => 'Short copy. Needs to fit on 2 lines in the small box. This will likely be a short snappy sentence that is a call to action',
            'id'          => 'description',
            'type'        => 'textarea_small',
            'attributes'  => array(
                'data-validation'             => 'true',
                'data-validation-word-length' => 12,
            ),
        )
    );

    $secondary_options->add_group_field(
        $group_field_id,
        array(
            'name'         => 'Image',
            'id'           => 'image',
            'type'         => 'file',
            'preview_size' => 'thumbnail',
        )
    );

    /**
     * Registers the Layout subpage: an ordered, sortable list of the sections
     * (banners + product blocks) shown between the Above the Fold area and the
     * Mega Block. Falls back to the historic order when empty (see
     * nm_get_front_page_layout()).
     */
    $layout_options = new_cmb2_box(
        array(
            'id'           => 'nm_front_page_layout_options_page',
            'title'        => 'Layout',
            'object_types' => array( 'options-page' ),
            'option_key'   => 'nm_front_page_layout_options',
            'parent_slug'  => 'nm_front_page_options',
            'capability'   => 'edit_posts',
        )
    );

    $layout_options->add_field(
        array(
            'name' => 'Front Page Layout',
            'desc' => 'Order the sections shown between the Above the Fold area and the Mega Block. Drag to reorder, remove a row to hide that section. Leave empty to use the default order. Keep to a sensible number of sections (~12 max).',
            'id'   => $prefix . 'front_page_layout_title',
            'type' => 'title',
        )
    );

    $layout_group_field_id = $layout_options->add_field(
        array(
            'id'      => $prefix . 'front_page_layout',
            'type'    => 'group',
            'options' => array(
                'group_title'   => __( 'Section {#}', 'nm' ),
                'add_button'    => __( 'Add Section', 'nm' ),
                'remove_button' => __( 'Remove Section', 'nm' ),
                'sortable'      => true,
            ),
        )
    );

    $layout_options->add_group_field(
        $layout_group_field_id,
        array(
            'name'    => 'Section',
            'id'      => 'block',
            'type'    => 'select',
            'options' => nm_get_front_page_layout_select_options(),
        )
    );

  /**
 * Registers tertiary options page and sets the main item as parent.
 *
 * The following code is an example of how to create a tertiary options page using CMB2.
 * Uncomment the code below to use it in your implementation.
 */

  /*
  $tertiary_options = new_cmb2_box( array(
      'id'           => 'nm_tertiary_options_page',
      'title'        => esc_html__( 'Tertiary Options', 'nm' ),
      'object_types' => array( 'options-page' ),
      'option_key'   => 'nm_tertiary_options',
      'parent_slug'  => 'nm_main_options',
  ) );

  $tertiary_options->add_field( array(
      'name' => esc_html__( 'Test Text Area for Code', 'nm' ),
      'desc' => esc_html__( 'Field description (optional)', 'nm' ),
      'id'   => 'textarea_code',
      'type' => 'textarea_code',
  ) );
  */
}
add_action( 'cmb2_admin_init', 'nm_register_front_page_options_metabox' );

// phpcs:disable WordPress.NamingConventions.ValidFunctionName

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  0.1.0
 * @param  string $key     Options array key.
 * @param  string $key_group Options group key (default: 'nm_front_page_options').
 * @return mixed           Option value
 */
function NM_get_option( $key = '', $key_group = 'nm_front_page_options', $default_value = false ) {
  if ( function_exists( 'cmb2_get_option' ) ) {
    // Use cmb2_get_option as it passes through some key filters.
    return cmb2_get_option( $key_group, $key, $default_value );
  }

  // Fallback to get_option if CMB2 is not loaded yet.
  $opts = get_option( $key_group, $default_value );

  $val = $default_value;

  if ( $key === 'all' ) {
    $val = $opts;
  } elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && $opts[ $key ] !== false ) {
    $val = $opts[ $key ];
  }

  return $val;
}
