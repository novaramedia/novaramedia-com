<?php
$recent_articles = new WP_Query(array(
  'category_name' => 'articles',
  'posts_per_page' => 7,
));

if ($recent_articles->have_posts()) {
  while ($recent_articles->have_posts()) {
    $recent_articles->the_post();
    $meta = get_post_meta($post->ID);
    $timestamp = get_post_time('c');
    $sub_category = get_the_sub_category($post->ID);

    // (3 layout options: default, small image right, full image below)
    // templatize all of these
?>
<div class="margin-bottom-small padding-bottom-small ui-border-bottom">
  <a href="<?php the_permalink(); ?>">
    <div class="layout-split-level fs-2">
      <span class="ui-tag"><?php if ($sub_category) { echo $sub_category; } ?></span> <span class="js-time-since-unlimited" data-timestamp="<?php echo $timestamp; ?>"></span>
    </div>
    <h4 class="fs-5-sans font-bold font-condensed mt-1">
      <?php the_title(); ?>
    </h4>
    <h5 class="fs-3-serif mt-1">
      <?php render_standfirst($post->ID); ?>
    </h5>
    <h5 class="fs-2 font-uppercase mt-1">
      <?php render_bylines($post->ID, false); ?>
    </h5>
  </a>
</div>
  <?php
    }
  }
?>
