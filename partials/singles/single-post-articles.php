<?php
  $meta = get_post_meta($post->ID);
  
  $category_id = get_cat_ID('Articles');
  $category_link = get_category_link( $category_id );
  
  $layout = 'basic';
  
  if (!empty($meta['_cmb_article_layout'][0])) {
    $layout = $meta['_cmb_article_layout'][0];
  }
  
//   pr($layout);
?>

<div class="row margin-bottom-basic mobile-margin-bottom-small">
  <div class="col col24">
    <h4>
      <a href="<?php echo $category_link; ?>">Articles</a>
      <?php
        $categories = get_the_category();
        $child_categories = array_filter($categories, 'only_child_category_filter');
        $child_categories = array_values($child_categories);

        if (isset($child_categories[0])) {
          echo '| <a href="' . get_term_link($child_categories[0]) . '">' . $child_categories[0]->name . '</a>';
        }
      ?>
    </h4>
  </div>
</div>

<?php
  get_template_part('partials/singles/articles/articles-header-' . $layout);
?>

<div id="single-articles-copy-row" class="flex-grid-row margin-bottom-basic">
  <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-10 flex-offset-l-1 flex-item-xl-8 flex-offset-xl-2 flex-item-xxl-6 flex-offset-xxl-3">
    
<?php
  if (!empty($meta['_cmb_sc'][0])) {
?>
    <div class="text-copy margin-top-basic margin-bottom-basic">
      <p class="font-smaller">Listen to this article as audio:</p>
      <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="120" scrolling="no" frameborder="no"></iframe>
    </div>
<?php
  }
?>  
    <div id="single-articles-copy" class="text-copy margin-top-basic margin-bottom-basic">
      <?php the_content(); ?>
    </div>

    <div id="single-articles-meta" class="font-smaller">
      <?php
      if (!empty($meta['bitly_url'])) {
        echo '<p>Share URL: <span class="u-pointer js-select">' . $meta['bitly_url'][0] . '</span></p> ';
      }
      ?>
      <p id="single-articles-publication-date">Published <?php the_time('j F Y'); ?></p>
    </div>
  </div>
</div>