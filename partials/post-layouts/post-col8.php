<?php
  $meta = get_post_meta($post->ID);
  $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;
  $author = !empty($meta['_cmb_author'][0]) ? $meta['_cmb_author'][0] : false;
?>

<a href="<?php the_permalink() ?>">
  <article <?php post_class('col col8'); ?> id="post-<?php the_ID(); ?>">

    <?php the_post_thumbnail('col8-16to9', array('class' => 'index-post-thumbnail')); ?>

    <h5 class="index-post-title margin-top-tiny js-fix-widows"><?php render_post_title($post->ID); ?></h5>

<?php
    if ($author) {
        ?>
    <h6 class="margin-top-micro font-bold">by <?php echo $author; ?></h6>
<?php
    }
?>

    <div class="index-post-description margin-top-tiny">
      <?php
        if ($description) {
            echo $description;
        } else {
            the_excerpt();
        }
      ?>
    </div>

  </article>
</a>