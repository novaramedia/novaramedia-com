<?php
/**
 * Generate complete YouTube embed iframe HTML.
 * Defaults to native iframe lazy loading; pass 'eager' for above-the-fold embeds.
 *
 * @param string  $youtube_id YouTube video ID.
 * @param boolean $autoplay   Set true to enable autoplay (only effective when browser policy allows, typically after internal navigation).
 * @param string  $loading    Iframe loading strategy: 'lazy' (default) or 'eager'.
 * @param string  $title      Optional context for the iframe title attribute (e.g. the post title). A "YouTube video player" prefix is applied automatically.
 *
 * @return string Complete iframe HTML element for YouTube embed.
 */
function render_youtube_embed_iframe( $youtube_id, $autoplay = false, $loading = 'lazy', $title = '' ) {
  $url        = generate_youtube_embed_url( $youtube_id, $autoplay );
  $allow_attr = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
  $loading    = $loading === 'eager' ? 'eager' : 'lazy';
  $title_attr = $title !== '' ? 'YouTube video player: ' . $title : 'YouTube video player';

  $iframe = sprintf(
    '<iframe class="youtube-player" type="text/html" src="%s" title="%s" allow="%s" loading="%s" allowfullscreen></iframe>',
    esc_url( $url ),
    esc_attr( $title_attr ),
    esc_attr( $allow_attr ),
    esc_attr( $loading )
  );

  return $iframe;
}

/**
 * Renders a Mailchimp signup form.
 *
 * @param string $mailchimp_key The Mailchimp key.
 * @param string $background_color The background color. Default is 'black'.
 * @param string $button_color The button color. Default is 'red'.
 */
function render_mailchimp_signup_form( $mailchimp_key, $background_color = 'black', $button_color = 'red' ) {
  if ( ! $mailchimp_key ) {
    return;
  }

  $form_unique_id = wp_unique_id( 'email-signup-form-' );
  $netlify_url = nm_get_netlify_url();
  ?>
<form id="<?php echo esc_attr( $form_unique_id ); ?>" class="email-signup__form" action="<?php echo esc_url( $netlify_url ); ?>" method="post" target="_blank">
  <input type="hidden" name="newsletter" value="<?php echo esc_attr( $mailchimp_key ); ?>" />

  <div class="email-signup__inputs">
    <div class="form-group mb-2">
      <label class="u-visuallyhidden" for="<?php echo esc_attr( $form_unique_id ); ?>-firstName">First name:</label>
      <input name="firstName" id="<?php echo esc_attr( $form_unique_id ); ?>-firstName" class="email-signup__name-input ui-input <?php echo $background_color === 'white' ? 'ui-input--border-gray' : ''; ?>" type="text" autocomplete="given-name" placeholder="First name" />
    </div>

    <div class="form-group mb-2">
      <label class="u-visuallyhidden" for="<?php echo esc_attr( $form_unique_id ); ?>-email">Email:</label>
      <input name="email" id="<?php echo esc_attr( $form_unique_id ); ?>-email" class="email-signup__email-input ui-input <?php echo $background_color === 'white' ? 'ui-input--border-gray' : ''; ?>" type="email" autocomplete="email" placeholder="Email" required />
    </div>

    <div class="email-signup__email-gdpr-group form-group layout-flex-align-center mb-2">
      <label for="<?php echo esc_attr( $form_unique_id ); ?>-gdpr" class="font-size-8 font-weight-bold">I agree to the <a target="_blank" rel="noopener" href="<?php echo esc_url( site_url( 'privacy-policy/' ) ); ?>">Privacy Policy</a></label>
      <input name="gdpr" id="<?php echo esc_attr( $form_unique_id ); ?>-gdpr" class="email-signup__email-gdpr-input ui-checkbox <?php echo $background_color === 'white' ? 'ui-checkbox--border-gray' : ''; ?> ml-2" type="checkbox" value="accepted" required/>
    </div>

    <input class="email-signup__submit ui-button ui-button--<?php echo esc_attr( $button_color ); ?> fs-6" type="submit" value="Sign up" />
  </div>
  <div class="email-signup__feedback-processing email-signup__overlay ui-rounded-box">
    <div class="spinner spinner--black">
      <div class="double-bounce1"></div>
      <div class="double-bounce2"></div>
    </div>
  </div>

  <div class="email-signup__feedback-failed email-signup__overlay ui-rounded-box font-weight-bold text-align-center">
    <div class="text-wrap-pretty">
      <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" x="0" y="0" version="1.1" viewBox="0 0 51 51" class="email-signup__icon mb-2 u-pointer" >
        <path d="M25.5 51C11.4 51 0 39.6 0 25.5S11.4 0 25.5 0 51 11.4 51 25.5 39.6 51 25.5 51zm0-50C12 1 1 12 1 25.5S12 50 25.5 50 50 39 50 25.5 39 1 25.5 1z"/>
        <path d="M36.9 14.4c-.2-.2-.5-.2-.7 0L25.5 25 14.9 14.4c-.2-.2-.5-.2-.7 0s-.2.5 0 .7l10.6 10.6-9.9 9.9c-.2.2-.2.5 0 .7.1.1.2.1.4.1s.3 0 .4-.1l9.9-9.9 9.9 9.9c.1.1.2.1.4.1s.3 0 .4-.1c.2-.2.2-.5 0-.7l-9.9-9.9L37 15.1c0-.2 0-.6-.1-.7z"/>
      </svg>
      <br />
      Sign up error: <span class="email-signup__feedback-message"></span>. Try again later.
    </div>
  </div>

  <div class="email-signup__feedback-completed email-signup__overlay ui-rounded-box font-weight-bold text-align-center">
    <div class="text-wrap-pretty">
      <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" x="0" y="0" version="1.1" viewBox="0 0 52 52" class="email-signup__icon mb-2">
        <path d="M38.4 16.2c-.3 0-.6.1-.8.3L20.9 33.2 14 26.3c-.1-.1-.2-.2-.4-.2-.1-.1-.2-.1-.3-.1-.1 0-.3 0-.4.1-.1.1-.3.1-.4.2-.1.1-.2.2-.2.4-.1.1-.1.3-.1.4 0 .1 0 .3.1.4.1.1.1.3.2.4l7.6 7.7c.2.2.5.3.8.3s.6-.1.8-.3L39.2 18c.2-.2.3-.4.3-.6 0-.2 0-.4-.1-.6-.1-.2-.2-.4-.4-.5-.2-.1-.4-.1-.6-.1z"/>
        <path d="M26 51.5C11.9 51.5.5 40.1.5 26S11.9.5 26 .5 51.5 11.9 51.5 26 40.1 51.5 26 51.5zm0-50C12.5 1.5 1.5 12.5 1.5 26s11 24.5 24.5 24.5 24.5-11 24.5-24.5S39.5 1.5 26 1.5z" />
      </svg>
      <br />
      Thanks for signing up.
    </div>
  </div>
</form>
  <?php
}
/**
 * Render a simplified UI tag link.
 *
 * @param string       $label         The text to display inside the tag.
 * @param string       $url           The link to wrap the tag in.
 * @param string[]|string $variants   Optional variant class(es) for styling. Can be a string or array.
 */
function render_ui_tag( $label, $url, $variants = array() ) {
  $variant_classes = is_array( $variants ) ? $variants : explode( ' ', $variants );
  $classes =
    array_merge(
      array( 'ui-tag-block' ),
      array_map(
        function ( $v ) {
          return 'ui-tag-block--' . $v;
        },
        $variant_classes
      )
    );

  ?>
  <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
    <span class="ui-tag"><?php echo esc_html( $label ); ?></span>
  </a>
  <?php
}
/**
 * Renders the schedule buttons for the support form, one off or regular.
 *
 * This function outputs the schedule buttons for the support form.
 *
 * @return void Outputs the HTML form directly.
 */
function render_support_form_schedule_buttons( $schedule_classes = '' ) {
  ?>
    <p class="u-visuallyhidden" id="donation-frequency-label">Choose donation frequency</p>
    <div class="grid-row mb-3 <?php echo esc_attr( $schedule_classes ); ?> font-weight-bold" role="radiogroup" aria-labelledby="donation-frequency-label">
      <div class="is-xxl-12">
        <button class="support-form__button ui-button ui-button--fill-width ui-button--active support-form__schedule-option support-form__schedule-option-left grid-item--tight" data-action="set-type" data-value="regular" role="radio" tabindex="0">Monthly</button>
      </div>
      <div class="is-xxl-12">
        <button class="support-form__button ui-button ui-button--fill-width support-form__schedule-option support-form__schedule-option-right grid-item--tight" data-action="set-type" data-value="oneoff" role="radio" tabindex="-1">One-off</button>
      </div>
    </div>
  <?php
}
/**
 * Renders the amount and submit buttons for the support form.
 *
 * This function outputs the amount and submit buttons for the support form.
 *
 * @param object $values The values object containing the donation amounts.
 * @param int $instance The unique instance identifier for the form.
 *
 * @return void Outputs the HTML form directly.
 */
function render_support_form_amount_buttons( $values, $instance, $button_classes = '' ) {
  ?>
  <div class="<?php echo esc_attr( $button_classes ); ?>">
    <p class="u-visuallyhidden" id="donation-amount-label">Choose your donation amount</p>
    <div class="grid-row grid-row--nested-tight mb-4" role="radiogroup" aria-labelledby="donation-amount-label">
      <?php
      foreach ( array( 'low', 'medium', 'high' ) as $tier ) {
        ?>
        <div class="grid-item grid-item--tight is-xxl-3 is-s-8 mb-s-2">
          <button
            class="support-form__button ui-button ui-button--fill-width support-form__value-option"
            role="radio"
            aria-checked="false"
            tabindex="-1"
            data-action="set-value"
            data-value="<?php echo esc_attr( $values->{"regular_$tier"} ); ?>"
            data-name="<?php echo esc_attr( $tier ); ?>"
          >
            £<?php echo esc_html( $values->{"regular_$tier"} ); ?>
          </button>
        </div>
        <?php
      }
      ?>
      <div class="grid-item grid-item--tight is-xxl-15 is-s-24">
        <label for="<?php echo esc_attr( $instance ); ?>__custom-input" class="u-visuallyhidden">
          Custom donation amount in pounds
        </label>
        <div class="support-form__custom-input-container u-position-relative">
          <span class="support-form__custom-input-prefix font-weight-bold font-size-11">£</span>
          <input
            id="<?php echo esc_attr( $instance ); ?>__custom-input"
            class="support-form__custom-input ui-input ui-input--red-border-white"
            type="number"
            min="1"
            placeholder="Custom amount"
          />
        </div>
      </div>
      <div class="grid-item grid-item--tight is-xxl-24 font-size-9 mt-2 mb-2">
        You can log in and edit, or cancel your monthly donation at any time.
      </div>
    </div>
    <div class="grid-row grid-row--nested-tight">
      <div class="grid-item grid-item--tight is-xxl-24">
        <input
          class="support-form__submit ui-button ui-button--white ui-button--fill-width"
          type="submit"
          value="Donate"
        />
      </div>
    </div>
  </div>
  <?php
}
/**
 * Render the support section heading and text based on the donation mode.
 *
 * @param string $donation_mode The donation mode, either 'regular' or 'oneoff'.
 * @param string $text_classes Optional additional classes for the text container.
 */
function render_support_heading_and_text( $donation_mode, $text_classes = '' ) {
  $data = nm_get_support_heading_text_data();

  // Set standard defaults
  $heading = 'Help build people-powered media';
  $text = 'Fund truthful, independent journalism. Join our supporters from just £1 per month, or whatever you can afford today.';

  // Check for heading override in donation mode data
  if ( isset( $data[ $donation_mode ]['heading'] ) && ! empty( $data[ $donation_mode ]['heading'] ) ) {
    $heading = $data[ $donation_mode ]['heading'];
  } elseif ( isset( $data['default']['heading'] ) && ! empty( $data['default']['heading'] ) ) {
    // Fall back to default array heading if available
    $heading = $data['default']['heading'];
  }

  // Check for text override in donation mode data
  if ( isset( $data[ $donation_mode ]['text'] ) && ! empty( $data[ $donation_mode ]['text'] ) ) {
    $text = $data[ $donation_mode ]['text'];
  } elseif ( isset( $data['default']['text'] ) && ! empty( $data['default']['text'] ) ) {
    // Fall back to default array text if available
    $text = $data['default']['text'];
  }

  ?>
  <div class="<?php echo esc_attr( $text_classes ); ?>" aria-live="polite">
    <h4 class="support-form__dynamic-heading font-size-13 font-weight-bold mb-3">
      <?php echo esc_html( $heading ); ?>
    </h4>
    <?php if ( $text ) { ?>
    <a href="<?php echo esc_url( home_url( 'support/' ) ); ?>" class="support-form__dynamic-text u-display-block mb-4">
      <?php echo esc_html( $text ); ?>
    </a>
    <?php } ?>
  </div>
  <?php
}
/**
 * Render payment icons for the support section.
 */
function render_payment_icons( $payment_classes = '' ) {
  $img_base = get_template_directory_uri() . '/dist/img/support-form/';
  $payment_methods = array(
    'Visa'       => 'Visa icon',
    'Mastercard' => 'Mastercard icon',
    'Stripe'     => 'Stripe icon',
    // 'PayPal'     => 'PayPal icon',
    'ApplePay'   => 'ApplePay icon',
    'GooglePay'  => 'GooglePay icon',
  );
  ?>
  <div class="<?php echo esc_attr( $payment_classes ); ?>">
    <?php foreach ( $payment_methods as $filename => $alt_text ) { ?>
      <img
        class="support-form__payment-type ui-rounded-box mr-2"
        src="<?php echo esc_url( $img_base . $filename . '.svg' ); ?>"
        alt="<?php echo esc_attr( $alt_text ); ?>"
      />
    <?php } ?>
  </div>
  <?php
}
/**
 * Render the support donation form with the heading, text, and form elements.
 *
 * @param string $variant Form display variant ('banner' or 'condensed').
 * @param bool $white_mobile_schedule Whether to use white background for mobile schedule buttons.
 * @param string $container_classes Additional CSS classes for the container element.
 * @return void Outputs the HTML form directly.
 */
function render_support_form( $variant = 'banner', $white_mobile_schedule = false, $container_classes = '' ) {
  // Generate unique instance ID
  $instance = uniqid( 'support-form-' );

  // Get support section values
  $support_section_autovalues = nm_get_support_autovalues();
  $active_values = $support_section_autovalues['default'];

  // Determine donation mode
  if ( isset( $active_values->show_first ) && in_array( $active_values->show_first, array( 'regular', 'oneoff' ), true ) ) {
    $donation_mode = $active_values->show_first;
  } else {
    $donation_mode = 'regular';
  }

  $variant_classes = 'support-section--' . $variant;

  if ( $white_mobile_schedule ) {
    $variant_classes .= ' support-section--white-mobile-schedule';
  }

  $support_section_classes = $variant_classes . ' ' . $container_classes;
  ?>
  <div class="support-section <?php echo esc_attr( $support_section_classes ); ?>">
    <form class="support-form background-red font-color-white ui-rounded-box" action="https://donate.novaramedia.com/regular" id="<?php echo esc_attr( $instance ); ?>">
      <input type="hidden" name="amount" class="support-form__value-input" value="<?php echo esc_attr( $active_values->regular_low ); ?>" />
      <?php render_support_form_schedule_buttons( 'support-form__schedule-mobile support-form__tab-schedule-buttons' ); ?>
      <div class="support-form__padding-container">
        <?php render_support_heading_and_text( $donation_mode, 'support-form__text-mobile' ); ?>
        <div class="support-form__desktop-container grid-row">
          <div class="grid-item is-xxl-12 support-form__left-column-desktop">
            <?php render_support_heading_and_text( $donation_mode, 'support-form__text-desktop pr-6' ); ?>
            <?php render_payment_icons( 'support-form__payment-type-desktop' ); ?>
          </div>
          <div class="grid-item is-xxl-12 support-form__right-column-desktop">
            <?php render_support_form_schedule_buttons( 'support-form__schedule-desktop' ); ?>
            <?php render_support_form_amount_buttons( $active_values, $instance, 'support-form__buttons-desktop' ); ?>
          </div>
        </div>
        <?php render_support_form_amount_buttons( $active_values, $instance, 'support-form__buttons-mobile' ); ?>
        <?php render_payment_icons( 'support-form__payment-type-mobile mt-3' ); ?>
      </div>
    </form>
  </div>
  <?php
}
/**
 * Render the see also block
 * Based on a passed query. Can render more than 1 post but will only show one on mobile
 * Not a complete component which is why it is a renderer. Use this inside other conditionals
 *
 * @param WP_Query $query           The query to render.
 * @param integer  $number_of_posts The number of posts to render.
 */
function render_see_also( $query, $number_of_posts = 1 ) {
  if ( ! $query ) {
    return;
  }

  if ( $query->have_posts() ) {
    ?>
    <h4 class="font-size-8 font-weight-bold text-uppercase mb-2 mb-s-1">See Also</h4>
    <div class="related-posts">
      <?php
      $i = 0;
      while ( $query->have_posts() ) {
        if ( $i >= $number_of_posts ) {
          break;
        }
        $query->the_post();
        $post_id = get_the_id();
        ?>
        <div class="mb-2
        <?php
        if ( $i !== 0 ) {
          echo 'only-desktop';
        }
        ?>
        ">
          <a href="<?php the_permalink(); ?>" class="ui-hover">
            <h5 class="font-size-10 font-weight-bold"><?php the_title(); ?></h5>
            <h6 class="font-size-8 font-weight-bold text-uppercase mt-1">
              <?php
              if ( nm_is_article( $post_id ) ) {
                render_bylines( $post_id );
              } else {
                render_standfirst( $post_id );
              }
              ?>
            </h6>
          </a>
        </div>
        <?php
        ++$i;
      }
      ?>
    </div>
    <?php
  }
}
/**
 * Renders post UI tags
 *
 * @param integer $post_id        Post ID.
 * @param Boolean $show_text      If the rendered tag should show the text.
 * @param Boolean $show_av_icons  If the rendered tag should show the audio/video icon.
 * @param string $block_style_varient Additional BEM varient class.
 */
function render_post_ui_tags( $post_id, $show_text = true, $show_av_icons = false, $block_style_varient = false ) {
  $sub_category = get_the_sub_category( $post_id, true );

  if ( empty( $sub_category ) ) {
    return;
  }

  $category_link = get_category_link( $sub_category->term_id );

  $tag_class = 'ui-tag-block';
  if ( $block_style_varient ) {
    $tag_class .= ' ui-tag-block--' . $block_style_varient;
  }
  echo '<a href="' . esc_url( $category_link ) . '" class="' . esc_attr( $tag_class ) . '">';

  if ( $show_text ) {
    echo '<span class="ui-tag">' . esc_html( $sub_category->name ) . '</span>';
  }

  if ( $show_av_icons ) {
    $top_category = get_the_top_level_category( $post_id );

    $default_classes = $show_text ? 'ml-1 ui-av-tag' : 'ui-av-tag';

    if ( $top_category->slug === 'video' ) {
      echo '<span class="' . $default_classes . ' ui-av-tag--video">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 7 7"><path fill="#000" d="M0 0v7l6.222-3.5L0 0Z"/></svg>
      </span>';
    } elseif ( $top_category->slug === 'audio' ) {
      echo '<span class="' . $default_classes . ' ui-av-tag--audio">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 75 65"><path fill="#000" d="M35.421 65V0l-35 32.5 35 32.5Z"/><path fill="#000" fill-rule="evenodd" d="M61.722 60.044C69.767 53.44 74.9 43.42 74.9 32.2 74.9 20.777 69.577 10.595 61.277 4l-7.135 7.136c6.518 4.724 10.757 12.4 10.757 21.065 0 8.46-4.04 15.976-10.296 20.724l7.12 7.119Zm-13.2-13.2A19.945 19.945 0 0 0 54.9 32.2c0-5.986-2.63-11.359-6.799-15.024l-7.1 7.1a9.983 9.983 0 0 1 3.9 7.924c0 3.021-1.34 5.73-3.458 7.563l7.08 7.08Z" clip-rule="evenodd"/></svg>
      </span>';
    }
  }

  echo '</a>';
}
/**
 * Renders a post thumbnail.
 *
 * @param integer $post_id Post ID.
 * @param string  $size    Thumbnail size.
 */
function render_thumbnail( $post_id, $size = 'col12-16to9', $attributes = null ) {
  if ( ! is_numeric( $post_id ) ) {
    return;
  }

  $markup = get_the_post_thumbnail( $post_id, $size, $attributes );
  $meta = get_post_meta( $post_id );

  if ( isset( $meta['_cmb_alt_thumb_id'] ) && is_numeric( $meta['_cmb_alt_thumb_id'][0] ) ) {
    $alt_markup = wp_get_attachment_image( $meta['_cmb_alt_thumb_id'][0], $size, false, $attributes );

    if ( $alt_markup !== '' ) {
      $markup = $alt_markup;
    }
  }

  echo $markup;
}
/**
 * Echos the standfirst for a post if set and not empty
 *
 * @param integer $post_id Post ID.
 */
function render_standfirst( $post_id = null ) {
  if ( $post_id === null ) {
    return;
  }

  $meta = get_post_meta( $post_id );

  if ( isset( $meta['_cmb_standfirst'] ) && ! empty( $meta['_cmb_standfirst'] ) ) {
    // The standfirst field is a plain-text textarea (no rich-text/link UI), so
    // render it as pure text. esc_html is stricter than wp_kses_post and also
    // makes it safe inside linked-card tiles that wrap it in an outer <a> (no
    // nested anchors possible). Covers render_video_title_and_standfirst too.
    echo esc_html( $meta['_cmb_standfirst'][0] );
  } else {
    return;
  }
}
/**
 * Render video title and the standfirst as single line
 *
 * Conditionally adds a period if the title does not end with a letter or number
 *
 * @param integer $post_id Post ID.
 */
function render_video_title_and_standfirst( $post_id = null ) {
  if ( $post_id === null ) {
    return;
  }

  $meta = get_post_meta( $post_id );

  echo esc_html( get_the_title( $post_id ) );

  if ( isset( $meta['_cmb_standfirst'] ) && ! empty( $meta['_cmb_standfirst'] ) ) {
    if ( preg_match( '/[a-zA-Z0-9]$/', get_the_title( $post_id ) ) !== 0 ) {
      echo '. ';
    } else {
      echo ' ';
    }

    render_standfirst( $post_id );
  }
}
/**
 * Echo the meta short description. If not set then render the excerpt.
 *
 * @param integer $post_id Post ID.
 */
function render_short_description( $post_id = null ) {
  if ( $post_id === null ) {
    return;
  }

  $meta = get_post_meta( $post_id );

  if ( isset( $meta['_cmb_short_desc'] ) && $meta['_cmb_short_desc'][0] ) {
    echo wp_kses_post( apply_filters( 'the_content', $meta['_cmb_short_desc'][0] ) );
  } else {
    echo get_the_excerpt( $post_id );
  }
}

/**
 * Renders bylines on a post.
 *
 * Checks post metadata for either contributors or authors. Prioritises contributors. Optionally can link the rendered bylines. Reverts to Novara Reporters if nothing found.
 *
 * @param integer $post_id   Post ID.
 * @param Boolean $is_linked If the rendered bylines should be linked, to either contributor page or Twitter metadata.
 */
function render_bylines( $post_id, $is_linked = false ) {
  // Use the shared nm_get_post_authors function with simplified interface
  $format = $is_linked ? 'html' : 'text';
  $authors = nm_get_post_authors( $post_id, $format );

  // Text mode returns a raw string — escape it. HTML mode returns anchor markup
  // already escaped inside nm_get_post_authors(), so pass it through wp_kses with
  // an anchor allowlist rather than esc_html(), which would mangle the links.
  if ( $authors === false ) {
    echo 'Novara Reporters';
  } elseif ( $is_linked ) {
    echo wp_kses(
      $authors,
      array(
        'a' => array(
          'href'   => array(),
          'target' => array(),
          'rel'    => array(),
        ),
      )
    );
  } else {
    echo esc_html( $authors );
  }
}

/**
 * Renders a newsletter signup form for a front-page layout slug.
 *
 * The slug ( `newsletter-signup-<id>` ) carries the newsletter post ID. Renders
 * the email-signup partial only when the post exists, is a newsletter, and has a
 * Mailchimp key. The ID is the only stored data — never a template path — so no
 * path-traversal guard is required.
 *
 * @param string $slug Layout slug of the form `newsletter-signup-<id>`.
 * @return void
 */
function nm_render_newsletter_signup( $slug ) {
  $prefix = 'newsletter-signup-';

  if ( ! str_starts_with( $slug, $prefix ) ) {
    return;
  }

  $newsletter_id = (int) substr( $slug, strlen( $prefix ) );
  $newsletter    = get_post( $newsletter_id );

  if ( ! $newsletter || $newsletter->post_type !== 'newsletter' ) {
    return;
  }

  $mailchimp_key = get_post_meta( $newsletter->ID, '_nm_mailchimp_key', true );

  if ( $mailchimp_key ) {
    get_template_part( 'partials/email-signup', null, array( 'newsletter_post_id' => $newsletter->ID ) );
  }
}
/**
 * Renders a row of resources.
 *
 * This function takes an array of resources, each with a 'title' and 'link' property,
 * and generates a row of HTML list items. Each list item contains a link to the resource.
 * Only resources with both a 'title' and 'link' are included.
 *
 * @param array $resources An array of resources, each with a 'title' and 'link'.
 *
 * @return void
 */
function render_resources_row( $resources ) {
  ?>
  <div id="single-resources-section" class="grid-row mb-4">
    <div class="grid-item is-s-24">
      <ul class="inline-action-list">
        <?php
        foreach ( $resources as $resource ) {
          if ( ! empty( $resource['title'] ) && ! empty( $resource['link'] ) ) {
            printf(
              '<li><a target="_blank" rel="noopener noreferrer" href="%s">%s</a></li>',
              esc_url( $resource['link'] ),
              esc_html( $resource['title'] )
            );
          }
        }
        ?>
      </ul>
    </div>
  </div>
  <?php
}

/**
 * Renders a social share link for a given platform.
 *
 * Builds the platform-specific share URL and outputs the anchor with
 * rel="noopener noreferrer" and escaped attributes.
 *
 * @param string $platform  One of: twitter, facebook, email, reddit.
 * @param string $url       The URL to share.
 * @param array  $args {
 *     Optional. Platform-specific arguments.
 *     @type string $title     Tweet text or Reddit post title.
 *     @type string $hashtag   Twitter hashtag (without #).
 *     @type string $subject   Email subject line.
 *     @type string $link_text Anchor label. Falls back to platform default.
 * }
 */
function render_share_link( string $platform, string $url, array $args = [] ): void {
  if ( empty( $url ) ) {
    return;
  }

  $title     = $args['title'] ?? null;
  $hashtag   = $args['hashtag'] ?? null;
  $subject   = $args['subject'] ?? '';
  $link_text = $args['link_text'] ?? null;

  switch ( $platform ) {
    case 'twitter':
      $href = 'https://twitter.com/intent/tweet?via=novaramedia';
      if ( $hashtag ) {
        $href .= '&hashtags=' . rawurlencode( $hashtag );
      }
      if ( $title ) {
        $href .= '&text=' . rawurlencode( $title );
      }
      $href .= '&url=' . rawurlencode( $url );
      $link_text = $link_text ?? 'Tweet';
      break;

    case 'facebook':
      $href      = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url );
      $link_text = $link_text ?? 'Facebook share';
      break;

    case 'email':
      $href      = 'mailto:?subject=' . rawurlencode( $subject ) . '&body=' . rawurlencode( $url );
      $link_text = $link_text ?? 'Email';
      break;

    case 'reddit':
      $href = 'https://www.reddit.com/submit?url=' . rawurlencode( $url );
      if ( $title ) {
        $href .= '&title=' . rawurlencode( $title );
      }
      $link_text = $link_text ?? 'Post to Reddit';
      break;

    default:
      return;
  }

  printf(
    '<a class="ui-action-link ui-action-link--small share-action-%s" href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
    esc_attr( $platform ),
    esc_url( $href ),
    esc_html( $link_text )
  );
}

/**
 * Renders a CMB2 meta field for the About page containing an array of roles and persons in those roles.
 *
 * @param array $data The return value of get_meta_field with single true.
 */
function render_about_group_field( $data ) {
  if ( ! $data ) {
    return;
  }

  foreach ( $data as $person ) {
    ?>
    <div class="mb-4">
      <h6 class="font-size-8"><?php echo esc_html( $person['title'] ); ?></h6>
      <?php
      foreach ( $person['name'] as $name ) {
        ?>
        <div class="about-page__person"><?php echo wp_kses_post( $name ); ?></div>
        <?php
      }
      ?>
    </div>
    <?php
  }
}

/**
 * Render a quotes carousel for the Support page
 *
 * @since 4.2.1
 *
 * @param array $quotes Array of quote strings to display in the carousel.
 * @return void Outputs the carousel HTML directly.
 */
function render_support_quotes_carousel( $quotes ) {
  if ( empty( $quotes ) || ! is_array( $quotes ) ) {
    return;
  }

  // Filter out empty quotes
  $quotes = array_filter( $quotes );

  if ( empty( $quotes ) ) {
    return;
  }
  ?>
  <section class="container support-page__quote-carousel ux-gallery-carousel mb-5" data-autoplay="true">
    <div class="swiper">
      <div class="swiper-wrapper">
      <?php foreach ( $quotes as $quote ) { ?>
          <div class="swiper-slide text-align-center ui-rounded-box">
            <h5 class="ui-boxed-title ui-boxed-title--grey mb-s-2">Supporters Say</h5>
            <div class="support-page__quote-container">
              <div class="font-serif quote support-page__quote-mark text-align-center">“</div>
              <p class="font-serif font-size-13 font-size-s-13 text-extra-leading text-wrap-balance"><?php echo esc_html( $quote ); ?></p>
            </div>
          </div>
        <?php } ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </section>
  <?php
}

/**
 * Render complete SoundCloud embed iframe HTML.
 * Handles both lazy loading and regular loading scenarios.
 *
 * @param string $soundcloud_url SoundCloud track URL.
 * @param string $size Player size: 'mini', 'small', 'medium', 'full'.
 * @param boolean $lazyload Set true to use lazy loading (uses data-src instead of src).
 * @param array $params Optional SoundCloud embed parameters.
 */
function render_soundcloud_embed_iframe( $soundcloud_url, $size = 'full', $lazyload = false, $params = array() ) {
  if ( empty( $soundcloud_url ) ) {
    return;
  }

  $height = get_soundcloud_player_height( $size );
  $url = generate_soundcloud_embed_url( $soundcloud_url, $params );

  if ( $lazyload ) {
    // Lazy loading placeholder with fallback
    ?>
    <div class="soundcloud-lazy"
      data-src="<?php echo esc_url( $url ); ?>"
      data-width="100%"
      data-height="<?php echo esc_attr( $height ); ?>"
      style="min-height: <?php echo esc_attr( $height ); ?>px;">
      <noscript>
        <a href="<?php echo esc_url( $soundcloud_url ); ?>" target="_blank" rel="noopener noreferrer">Listen on SoundCloud</a>
      </noscript>
    </div>
    <?php
  } else {
    ?>
    <iframe src="<?php echo esc_url( $url ); ?>"
      width="100%"
      height="<?php echo esc_attr( $height ); ?>"
      allow="autoplay"></iframe>
    <?php
  }
}
