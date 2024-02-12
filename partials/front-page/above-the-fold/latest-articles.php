<?php
$query_args = array(
  'category_name' => 'articles',
  'posts_per_page' => 7,
);

if (is_array($args) && count($args) > 0) {
  $query_args = array_merge($query_args, array('post__not_in' => $args));
}

$recent_articles = new WP_Query($query_args);
$i = 0;

if ($recent_articles->have_posts()) {
  while ($recent_articles->have_posts()) {
    $recent_articles->the_post();
    $meta = get_post_meta($post->ID);
    $timestamp = get_post_time('c');
    $sub_category = get_the_sub_category($post->ID);
?>
<div class="margin-bottom-small padding-bottom-small ui-border-bottom">
  <a href="<?php the_permalink(); ?>" class="ui-post-hover">
    <div class="layout-split-level fs-2 mb-2">
      <?php render_post_ui_tags($post_id); ?>
      <span class="js-time-since" data-timestamp="<?php echo $timestamp; ?>"></span>
    </div>
    <?php
      // [temp logic]. to be driven by meta logics based on position and quality of image assets
      if ($i === 1 || $i === 6) { // render small thumb layout
    ?>
      <div class="grid-row grid--nested">
        <div class="grid-item is-xxl-16">
          <h4 class="post__title fs-4-sans font-condensed">
            <?php the_title(); ?>
          </h4>
          <h5 class="fs-3-serif mt-1">
            <?php render_standfirst($post->ID); ?>
          </h5>
          <h5 class="fs-2 font-uppercase mt-1">
            <?php render_bylines($post->ID, false); ?>
      </h5>
        </div>
        <div class="grid-item is-xxl-8">
          <?php render_thumbnail($post->ID, 'col4-square', array(
            'class' => 'ui-rounded-image u-display-block',
            'data-no-lazysizes' => true,
            'loading' => 'eager'
          )); ?>
        </div>
      </div>
     <?php
      } else if ($i === 3) { // render full image layout
    ?>
      <h4 class="post__title s-5-sans font-condensed">
        <?php the_title(); ?>
      </h4>
      <div class="mt-1">
        <?php render_thumbnail($post->ID, 'col12-16to9', array(
        'class' => 'ui-rounded-image u-display-block',
        'data-no-lazysizes' => true,
        'loading' => 'eager'
      )); ?>
      </div>
      <h5 class="fs-3-serif mt-1">
        <?php render_standfirst($post->ID); ?>
      </h5>
      <h5 class="fs-2 font-uppercase mt-1">
        <?php render_bylines($post->ID, false); ?>
      </h5>
     <?php
      } else {
     ?>
      <h4 class="post__title fs-5-sans font-condensed">
        <?php the_title(); ?>
      </h4>
      <h5 class="fs-3-serif mt-1">
        <?php render_standfirst($post->ID); ?>
      </h5>
      <h5 class="fs-2 font-uppercase mt-1">
        <?php render_bylines($post->ID, false); ?>
      </h5>
     <?php
      } ?>
  </a>
</div>
  <?php
      $i++;
    }
  }
?>
