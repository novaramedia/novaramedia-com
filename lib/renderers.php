<?php
/**
 * Render the see also block
 * Based on a passed query. Can render more than 1 post but will only show one on mobile
 * Not a complete component which is why it is a renderer. Use this inside other conditionals
 *
 * @param WP_Query $query           The query to render
 * @param integer  $number_of_posts The number of posts to render
 */
function render_see_also($query, $number_of_posts = 1) {
  if (!$query) {
    return;
  }

  if ($query->have_posts()) {
?>
<h4 class="font-size-8 font-weight-bold text-uppercase mb-2 mb-s-1">See Also</h4>
<div class="related-posts">
<?php
    $i = 0;
    while ($query->have_posts()) {
      if ($i >= $number_of_posts) {
        break;
      }
      $query->the_post();
      $post_id = get_the_id();
?>
  <div class="mb-2 <?php if ($i != 0) { echo 'only-desktop'; } ?>">
    <a href="<?php the_permalink(); ?>" class="ui-hover">
      <h5 class="font-size-10 font-weight-bold"><?php the_title(); ?></h5>
      <h6 class="font-size-8 font-weight-bold text-uppercase mt-1">
        <?php
          if (nm_is_article($post_id)) {
            render_bylines($post_id);
          } else {
            render_standfirst($post_id);
          }
        ?>
      </h6>
    </a>
  </div>
<?php
      $i++;
    }
?>
</div>
<?php
  }
}
/**
 * Renders post UI tags
 *
 * @param integer $post_id        Post ID
 * @param Boolean $show_text      If the rendered tag should show the text
 * @param Boolean $show_av_icons  If the rendered tag should show the audio/video icon
 * @param string $block_style_varient Additional BEM varient class
 */
function render_post_ui_tags($post_id, $show_text = true, $show_av_icons = false, $block_style_varient = false) {
  $sub_category = get_the_sub_category($post_id, true);

  if (!$sub_category) {
    return;
  }

  $category_link = get_category_link($sub_category->term_id);

  echo '<a href="' . $category_link . '" class="ui-tag-block';
  echo $block_style_varient ? ' ui-tag-block--' . $block_style_varient : '';
  echo '">';

  if ($show_text) {
    echo '<span class="ui-tag">' . $sub_category->name . '</span>';
  }

  if ($show_av_icons) {
    $top_category = get_the_top_level_category($post_id);

    $default_classes = $show_text ? 'ml-1 ui-av-tag' : 'ui-av-tag';

    if ($top_category->slug === 'video') {
      echo '<span class="' . $default_classes . ' ui-av-tag--video">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 7 7"><path fill="#000" d="M0 0v7l6.222-3.5L0 0Z"/></svg>
      </span>';
    } else if ($top_category->slug === 'audio') {
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
 * @param integer $post_id Post ID
 * @param string  $size    Thumbnail size
 */
function render_thumbnail($post_id, $size = 'col12-16to9', $attributes = null) {
  if (!is_numeric($post_id)) {
    return;
  }

  $markup = get_the_post_thumbnail($post_id, $size, $attributes);
  $meta = get_post_meta($post_id);

  if (isset($meta['_cmb_alt_thumb_id']) && is_numeric($meta['_cmb_alt_thumb_id'][0])) {
    $alt_markup = wp_get_attachment_image($meta['_cmb_alt_thumb_id'][0], $size, false, $attributes);

    if ($alt_markup !== '') {
      $markup = $alt_markup;
    }
  }

  echo $markup;
}
/**
 * Echos the standfirst for a post if set and not empty
 *
 * @param integer $postId Post ID
 */
function render_standfirst($postId = null) {
  if ($postId === null) {
    return;
  }

  $meta = get_post_meta($postId);

  if (isset($meta['_cmb_standfirst']) && !empty($meta['_cmb_standfirst'])) {
    echo $meta['_cmb_standfirst'][0];
  } else {
    return;
  }
}
/**
 * Echo the meta short description. If not set then render the excerpt.
 *
 * @param integer $postId Post ID
 */
function render_short_description($postId = null) {
  if ($postId === null) {
    return;
  }

  $meta = get_post_meta($postId);

  if (isset($meta['_cmb_short_desc']) && $meta['_cmb_short_desc'][0]) {
    echo apply_filters('the_content', $meta['_cmb_short_desc'][0]);
  } else {
    echo get_the_excerpt($postId);
  }
}

/**
 * Renders bylines on a post.
 *
 * Checks post metadata for either contributors or authors. Prioritises contributors. Optionally can link the rendered bylines. Reverts to Novara Reporters if nothing found.
 *
 * @param integer $post_id   Post ID
 * @param Boolean $is_linked If the rendered bylines should be linked, to either contributor page or Twitter metadata
 */
function render_bylines($post_id, $is_linked = false) {
  $contributors_posts_array = get_contributors_array($post_id);

  $author = get_post_meta($post_id, '_cmb_author', true);
  $twitter = get_post_meta($post_id, '_cmb_author_twitter', true);

  $twitter_url = false;

  if ($twitter &&(!is_array($twitter) || count($twitter) === 1)) { // if twitter is set and it either isn't an array (old support) or it only has 1 value then we can display it
    if (is_array($twitter)) {
      $twitter_url = $twitter[0];
    } else {
      $twitter_url = $twitter;
    }
  }

  if ($contributors_posts_array) {
    $number_of_contributors = count($contributors_posts_array);

    foreach($contributors_posts_array as $index => $contributor) {
      if ($number_of_contributors > 1) {
        if ($number_of_contributors === $index + 1) {
          echo ' & ';
        } else if ($index !== 0) {
          echo ', ';
        }
      }

      echo $is_linked ? '<a href="' . get_the_permalink($contributor->ID) . '">' . $contributor->post_title . '</a>' : $contributor->post_title;
    }

  } else if (!empty($author)) {
    if ($twitter_url && $is_linked) {
      echo '<a href="https://twitter.com/' . $twitter_url . '" target="_blank" rel="nofollow">' . $author . '</a>';
    } else {
      echo $author;
    }
  } else {
    echo 'Novara Reporters';
  }
}

/**
* Renders a banner from template parts according to value from meta field select. Has ability to custom render for template parts that require arguements like email signup
*
* @param string $key A key from the meta select. Default is the path to a template part, otherwise the key needs to be unique but descriptive and used to hook custom logic.
*/
function render_front_page_banner($key) {
  switch ($key) {
    case (false || '0'): // if empty or set none
      break;
    case (preg_match('/^newsletter-signup-/', $key) ? true : false) : // if key has newsletter signup prefix
      $newsletter_id = str_replace('newsletter-signup-', '', $key);
      $newsletter = get_post($newsletter_id);

      if ($newsletter) {
        $meta = get_post_meta($newsletter->ID);

        $mailchimp_key = !empty($meta['_nm_mailchimp_key']) ? $meta['_nm_mailchimp_key'][0] : false;

        if ($mailchimp_key) {
          get_template_part('partials/email-signup', null, array(
            'newsletter_page_id' => $newsletter_id
          ));
        }
      }
      break;
    case 'email-the-cortado': // custom logic for email sign ups with variables depreciated 3.9.0
      get_template_part('partials/email-signup', null, array(
        'newsletter' => 'The Cortado',
        'copy' => 'Sign up to The Cortado—your weekly shot of political analysis from Ash Sarkar, plus a round up of the week’s content. It’s brewed every Friday morning.'
      ));
      break;
    case 'email-the-pick': // depreciated 3.9.0
      get_template_part('partials/email-signup', null, array(
        'newsletter' => 'The Pick',
        'copy' => 'Novara Media’s best articles, every week, straight to your inbox.'
      ));
      break;
    default: // default behavior to render the template part from path provided
      get_template_part($key);
  }
}
/**
 * Renders the title of a post.
 *
 * This function retrieves the title of the post with the given ID and echoes it.
 * If the post has a sub-category and the current page is not that sub-category,
 * it prepends the name of the sub-category to the title.
 *
 * @param int $postId The ID of the post.
 *
 * @return void
 * @deprecated 3.9.0
 */
function render_post_title($postId) {
  $title = get_the_title($postId);

  $sub_category = get_the_sub_category($postId, true);

  if ($sub_category && !is_category($sub_category->term_id)) {
    $title = '<span class="font-small-caps">' . $sub_category->name . ':</span> ' . $title;
  }

  echo $title;
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
function render_resources_row($resources) {
?>
<div id="single-resources-section" class="grid-row mb-4">
  <div class="grid-item is-s-24">
    <ul class="inline-action-list">
      <?php
        foreach($resources as $resource) {
          if (!empty($resource['title']) && !empty($resource['link'])) {
            echo '<li><a target="_black" href="' . $resource['link'] . '">' . $resource['title'] . '</a><li>';
          }
        }
      ?>
    </ul>
  </div>
</div>
<?php
}

/**
 * Renders a Twitter share link.
 *
 * @param string $url The URL to be shared.
 * @param string|null $title The title of the tweet. Default is null.
 * @param string $link_text The text to be displayed for the link. Default is 'Tweet'.
 * @param string|null $hashtag The hashtag to be included in the tweet. Default is null.
 *
 * @return void
 */
function render_tweet_link($url, $title = null, $link_text = 'Tweet', $hashtag = null) {
  if (empty($url)) {
    return;
  }

  $twitter_url = 'https://twitter.com/intent/tweet?via=novaramedia';

  if ($hashtag) {
    $twitter_url .= '&hashtags=' . $hashtag;
  }

  if ($title) {
    $twitter_url .= '&text=' . $title;
  }

  $twitter_url .= '&url=' . urlencode($url);

  echo '<a class="ui-action-link ui-action-link--small share-action-twitter" href="' . $twitter_url . '" target="_blank">' . $link_text . '</a>';
}

function render_facebook_share_link($url, $link_text = 'Facebook share') {
  if (empty($url)) {
    return;
  }

  $facebook_url = 'https://www.facebook.com/sharer/sharer.php?';

  $facebook_url .= '&u=' . urlencode($url);

  echo '<a class="ui-action-link ui-action-link--small share-action-facebook" href="' . $facebook_url . '" target="_blank">' . $link_text . '</a>';
}

function render_email_share_link($url, $subject = '', $link_text = 'Email') {
  if (empty($url)) {
    return;
  }

  $mailto_scheme = 'mailto:?subject=' . urlencode($subject) . '&body='. urlencode($url);

  echo '<a class="ui-action-link ui-action-link--small share-action-email" href="' . $mailto_scheme . '" target="_blank">' . $link_text . '</a>';
}

function render_reddit_share_link($url, $title = null, $link_text = 'Post to Reddit') {
  if (empty($url)) {
    return;
  }

  $reddit_url = 'http://www.reddit.com/submit?';

  $reddit_url .= '&url=' . urlencode($url);

  if ($title) {
    $reddit_url .= '&title=' . urlencode($title);
  }

  echo '<a class="ui-action-link ui-action-link--small share-action-reddit" href="' . $reddit_url . '" target="_blank">' . $link_text . '</a>';
}

/**
* Renders a CMB2 meta field for the About page containing an array of roles and persons in those roles.
*
* @param array $data The return value of get_meta_field with single true.
*/
function render_about_group_field($data) {
  if (!$data) {
    return;
  }

  foreach($data as $person) {
?>
  <div class="margin-bottom-small">
    <h6 class="font-small-caps"><?php echo $person['title']; ?></h6>
<?php
    foreach($person['name'] as $name) {
?>
      <div class="about-page__person"><?php echo $name; ?></div>
<?php
    }
  ?>
  </div>
<?php
  }
}
