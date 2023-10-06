<?php
  $postId = get_the_ID();
  $meta = get_post_meta($postId);
  $isArticle = nm_is_article($postId);
?>
<div>
  <div>
    <h2 class="fs-5-sans font-bold js-fix-widows"><?php the_title(); ?></h2>
    <?php
      $meta = get_post_meta(get_the_ID());
    ?>
    <h5 class="fs-2 font-uppercase mt-1">
      <?php
        if ($isArticle) {
          render_bylines($postId);
        } else {
          render_standfirst($postId);
        }
      ?>
    </h5>
    <p class="mt-1">
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
