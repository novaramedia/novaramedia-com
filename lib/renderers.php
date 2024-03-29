<?php
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
        $banner_text = !empty($meta['_nm_banner_text']) ? $meta['_nm_banner_text'][0] : false;

        if ($mailchimp_key) {
          get_template_part('partials/email-signup', null, array(
            'newsletter' => $mailchimp_key,
            'copy' => $banner_text,
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

function render_front_page_video_block($video_category_slug, $excluded_category_slug = false) {
  $category = get_term_by('slug', $video_category_slug, 'category');

  if ($category) {
    $category_link = get_category_link($category->term_id);
?>
<div class="row">
  <div class="col col24 margin-bottom-small">
    <h4><a href="<?php echo $category_link; ?>"><?php echo $category->name; ?></a></h4>
  </div>
</div>

<div class="row">
  <?php
    $args = array(
      'posts_per_page' => 4,
      'cat' => $category->term_id,
    );

    if ($excluded_category_slug) {
      $excluded_category = get_term_by('slug', $excluded_category_slug, 'category');

      if ($excluded_category) {
        $args['category__not_in'] = array($excluded_category->term_id);
      }
    }

    $latest_video = new WP_Query($args);

    render_video_query($latest_video);
  ?>
</div>
<?php
  }
}

function render_video_query($query) {
  global $post;

  // First large video
  if ($query->have_posts()) {
    $query->the_post();
  ?>
  <div class="col col18 video-block-main-video mobile-margin-bottom-basic">
    <a href="<?php the_permalink(); ?>">
      <?php the_post_thumbnail('col18-16to9'); ?>
    </a>

    <a href="<?php the_permalink(); ?>">
      <h6 class="js-fix-widows font-size-2 font-semibold margin-top-micro"><?php the_title(); ?></h6>
    </a>
  </div>
  <div class="col col6">
    <?php

    // Side 3 remaining vids
    if ($query->have_posts()) {
      while($query->have_posts()) {
        $query->the_post();
        $meta = get_post_meta($post->ID);
    ?>
    <a href="<?php the_permalink(); ?>">
      <div class="video-related-video margin-bottom-small">
        <?php
          if (!empty($meta['_cmb_alt_thumb_id'])) {
            echo wp_get_attachment_image($meta['_cmb_alt_thumb_id'][0], 'col6-16to9 video-related-video__thumbnail');
          } else {
            the_post_thumbnail('col6-16to9 video-related-video__thumbnail');
          }
        ?>

        <h6 class="js-fix-widows font-size-1 font-semibold margin-top-micro"><?php the_title(); ?></h6>
      </div>
   </a>
    <?php
      }
    }
    wp_reset_postdata();
    ?>
  </div>
<?php
  }
}

function render_post_title($postId) {
  $title = get_the_title($postId);

  $sub_category = get_the_sub_category($postId, true);

  if ($sub_category && !is_category($sub_category->term_id)) {
    $title = '<span class="font-small-caps">' . $sub_category->name . ':</span> ' . $title;
  }

  echo $title;
}

function render_resources_row($resources) {
?>
<div id="single-resources-section" class="row margin-bottom-basic">
  <div class="col col24">
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

  echo '<a class="share-action share-action-twitter" href="' . $twitter_url . '" target="_blank">' . $link_text . '</a>';
}

function render_facebook_share_link($url, $link_text = 'Facebook share') {
  if (empty($url)) {
    return;
  }

  $facebook_url = 'https://www.facebook.com/sharer/sharer.php?';

  $facebook_url .= '&u=' . urlencode($url);

  echo '<a class="share-action share-action-facebook" href="' . $facebook_url . '" target="_blank">' . $link_text . '</a>';
}

function render_email_share_link($url, $subject = '', $link_text = 'Email') {
  if (empty($url)) {
    return;
  }

  $mailto_scheme = 'mailto:?subject=' . urlencode($subject) . '&body='. urlencode($url);

  echo '<a class="share-action share-action-email" href="' . $mailto_scheme . '" target="_blank">' . $link_text . '</a>';
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

  echo '<a class="share-action share-action-reddit" href="' . $reddit_url . '" target="_blank">' . $link_text . '</a>';
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
