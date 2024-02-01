<?php
  $content_type = get_the_top_level_category(get_the_ID()); // get top level catergory for content type
  $is_article = $content_type->category_nicename === 'articles' ? true : false; // check if is article for display layout

  $meta = get_post_meta($post->ID);
  $timestamp = get_post_time('c');
?>
<a href="<?php the_permalink() ?>">
  <article <?php post_class('front-page-featured margin-bottom-basic'); ?> id="post-<?php the_ID(); ?>">
    <?php
      if (!empty($meta['_cmb_utube'])) {
    ?>
      <div class="u-video-embed-container margin-bottom-tiny">
        <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($meta['_cmb_utube'][0]); ?>"></iframe>
      </div>
    <?php
      } else {
        the_post_thumbnail('col12-16to9', array('class' => 'margin-bottom-tiny u-display-block', 'data-no-lazysizes' => true, 'loading' => 'eager'));
      }

      $sub_category = get_the_sub_category($post->ID);

      if ($sub_category) {
    ?>
    <h4 class="front-page-featured__meta font-small-caps"><?php echo $sub_category; ?> <span class="js-time-since" data-timestamp="<?php echo $timestamp; ?>"></span></h4>
    <?php
      }
    ?>
    <h3 class="front-page-featured__title js-fix-widows"><?php the_title(); ?></h3>
    <?php if ($is_article) { ?>
    <h6 class="front-page-featured__author font-larger">by <?php render_bylines($post->ID, false); ?></h6>
    <?php }

      if (!empty($meta['_cmb_short_desc'])) {
    ?>
      <div class="margin-top-small"><?php echo $meta['_cmb_short_desc'][0]; ?></div>
    <?php
      }
    ?>
  </article>
</a>
