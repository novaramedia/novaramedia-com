<?php
  $postId = get_the_ID();
  $meta = get_post_meta($postId);
  $isArticle = nm_is_article($postId);
?>
<div><?php // This is the secondary featured post with image ?>
  <?php the_post_thumbnail(); ?>
  <div>
    <h2 class="fs-5-sans font-bold js-fix-widows"><?php the_title(); ?></h2>
    <h5 class="fs-2 font-uppercase">
      <?php
        if ($isArticle) {
          render_bylines($postId);
        } else {
          render_standfirst($postId);
        }
      ?>
    </h5>
    <p>
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
