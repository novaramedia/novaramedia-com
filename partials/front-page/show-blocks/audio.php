<?php
  function render_show($slug, $description, $logo = null) {
    $category = get_term_by('slug', $slug, 'category');

    if (!$category) {
      return;
    }

    $latest = get_posts(array(
      'posts_per_page' => 5,
      'category' => $category->term_id,
    ));

    if ($latest) {
?>
<div class="fs-5-sans mb-5">
  <?php echo $description; ?>
</div>

<?php
  $post_id = $latest[0]->ID;
  $meta = get_post_meta($post_id);
?>
<div class="grid-row grid--nested mb-5">
  <div class="grid-item is-s-24 is-xxl-12">
    <?php render_thumbnail($post_id, 'col12'); ?>
  </div>
  <div class="grid-item is-s-24 is-xxl-12">
    <h3 class="fs-5-sans font-weight-bold mb-3"><?php echo get_the_title($post_id); ?></h3>
    <div class="fs-4-sans font-weight-regular mb-4">
      <?php render_short_description($post_id); ?>
    </div>
    <?php
      if (!empty($meta['_cmb_sc'][0])) {
    ?>
      <iframe width="100%" height="20" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>&inverse=false&auto_play=false&show_user=true"></iframe>
    <?php
    }
    ?>
  </div>
</div>

<div>
  <h4 class="fs-3-sans font-weight-bold font-uppercase mb-3">Recent Episodes</h4>
  <div class="grid-row grid--nested">
  <?php
    array_shift($latest);
    foreach ($latest as $post) {
      $post_id = $post->ID;
  ?>
    <div class="grid-item is-m-24 is-xxl-12 pb-4 mb-4">
      <a href="<?php get_the_permalink($post_id); ?>">
        <div class="fs-2 mb-1">
          <?php echo get_the_time('j F Y', $post_id); ?>
        </div>
        <h4 class="post__title fs-4-sans">
          <?php render_post_ui_tags($post_id, false, true, 'no-fill--white'); ?> <?php echo get_the_title($post_id); ?>
        </h4>
        <div class="pb-3 ui-border-bottom">
          <?php render_short_description($post_id); ?>
        </div>
      </a>
    </div>
  <?php
    }
  ?>
  </div>
</div>
<?php
    }
  }
?>
<section id="front-page-audio-posts" class="container mt-7 mb-7">
  <div class="grid-row">
    <div class="grid-item is-xxl-12">
      <?php render_show('novarafm', 'Novara Media’s flagship podcast is about the ideas that shape our past, present and future. With a desire to change the world—and ourselves along the way—Novara FM interrogates the people, ideologies and movements that wield power in our lives, from politics and culture to technology and the environment.'); ?>
    </div>
    <div class="grid-item is-xxl-12">
      <?php render_show('acfm', 'The home of the weird left. Nadia Idle, Jeremy Gilbert and Keir Milburn examine the links between left-wing politics, culture, music and experiences of collective joy.'); ?>
    </div>
  </div>
</section>
