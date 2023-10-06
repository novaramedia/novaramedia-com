<?php
  $postId = get_the_ID();
  $meta = get_post_meta($postId);
  $isArticle = nm_is_article($postId);
?>
<div class="featured-posts__primary">
  <?php the_post_thumbnail(); ?>
  <div>
    <h2 class="fs-8 js-fix-widows"><?php the_title(); ?></h2>
    <h5 class="fs-2 font-uppercase mt-3">
      <?php
        if ($isArticle) {
          render_bylines($postId);
        } else {
          render_standfirst($postId);
        }
      ?>
    </h5>
    <p class="mt-2">
      <?php
        if ($isArticle) {
          render_standfirst($postId);
        } else {
          render_short_description($postId);
        }
      ?>
    </p>
  </div>
</div>
