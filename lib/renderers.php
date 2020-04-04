<?php

function render_home_focus($focus, $classes) {
  $focus_object = get_term($focus);
  $focus_link = get_term_link($focus_object);
?>
  <section id="home-focus-posts" class="container <?php echo $classes; ?>">
    <div class="row">
      <div class="col col24 margin-bottom-small">
        <h4><a href="<?php echo $focus_link; ?>">Focus: <?php echo $focus_object->name; ?></a></h4>
      </div>
    </div>

    <div class="row margin-bottom-small mobile-margin-bottom-none">
<?php
      $focusPosts = new WP_Query(array(
        'posts_per_page' => 3,
        'tax_query' => array(
          array(
            'taxonomy' => 'focus',
            'field' => 'term_id',
            'terms' => $focus
          ),
        ),
      ));
      if ($focusPosts->have_posts()) {
        $i = 0;
        while ($focusPosts->have_posts()) {
          $focusPosts->the_post();

          if ($i % 3 === 0 && $i !== 0) {
            echo "</div>\n<div class=\"row margin-bottom-small mobile-margin-bottom-none\">";
          }

          get_template_part('partials/post-layouts/home-focus-post-col8');

          $i++;
        }
      }
?>
    </div>
  </section>
 <?php
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
      <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
    </a>
  </div>
  <div class="col col6">
    <?php

    // Side 3 remaining vids
    if ($query->have_posts()) {
      while($query->have_posts()) {
        $query->the_post();
    ?>
    <a href="<?php the_permalink(); ?>">
     <div class="video-related-video margin-bottom-small">
       <?php the_post_thumbnail('col6-16to9'); ?>
       <h6 class="js-fix-widows margin-top-micro"><?php the_title(); ?></h6>
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
  $is_article = has_category('articles', $postId);

  $title = get_the_title($postId);

  if ($is_article) {
    $categories = get_the_category($postId);

    $child_categories = array_filter($categories, 'only_child_category_filter');
    $child_categories = array_values($child_categories);

    if (isset($child_categories[0])) {
      $title = '<span class="font-small-caps">' . $child_categories[0]->name . ':</span> ' . $title;
    }

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
  $twitter_url = 'https://twitter.com/intent/tweet?via=novaramedia';

  if ($hashtag) {
    $twitter_url .= '&hashtags=' . $hashtag;
  }

  if ($title) {
    $twitter_url .= '&text=' . $title;
  }

  $twitter_url .= '&url=' . urlencode($url);

  echo '<a href="' . $twitter_url . '" target="_blank">' . $link_text . '</a>';
}

function render_facebook_share_link($url, $link_text = 'Facebook Share') {
  $facebook_url = 'https://www.facebook.com/sharer/sharer.php?';

  $facebook_url .= '&u=' . urlencode($url);

  echo '<a href="' . $facebook_url . '" target="_blank">' . $link_text . '</a>';
}
