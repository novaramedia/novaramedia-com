<?php
  $meta = get_post_meta($post->ID);
  
  $category_id = get_cat_ID('Articles');
  $category_link = get_category_link( $category_id );
  
  $layout = !empty($meta['_cmb_article_layout'][0]) ? $meta['_cmb_article_layout'][0] : 'basic';
  
  $articles_support_box_text = IGV_get_option('_igv_articles_support_box_text');
  $support_box_override_text = !empty($meta['_cmb_support_box_override'][0]) ? $meta['_cmb_support_box_override'][0] : false;

  $type_category = get_child_level_child_category($post->ID);
?>

<div class="row margin-bottom-basic mobile-margin-bottom-small">
  <div class="col col24">
    <h4>
      <?php
        if ($type_category) {
          echo '<a href="' . get_term_link($type_category) . '">' . $type_category->name . '</a>';
        } else {
      ?>
        <a href="<?php echo $category_link; ?>">Articles</a>
      <?php
        }
      ?>
    </h4>
  </div>
</div>

<?php
  get_template_part('partials/singles/articles/articles-header-' . $layout);
?>

<div class="flex-grid-row margin-top-mid margin-bottom-basic">
  <div class="flex-grid-item only-desktop flex-item-m-12 flex-item-l-12 flex-item-xl-2 flex-item-xxl-2 margin-bottom-basic">
    <?php      
      if ($articles_support_box_text || $support_box_override_text) {
    ?>    
    <a href="<?php echo home_url('support'); ?>">
      <div id="single-article-support-box">
        <?php 
          if ($support_box_override_text) {
            echo $support_box_override_text;
          } else {
            echo $articles_support_box_text; 
          }
        ?>
      </div>
    </a>
    <?php
      }
    ?>
  </div>

  <div class="flex-grid-item flex-item-s-12 flex-offset-s-0 flex-item-m-10 flex-offset-m-1 flex-item-l-10 flex-offset-l-1 flex-item-xl-8 flex-offset-xl-0 flex-item-xxl-6 flex-offset-xxl-1">
    
<?php
  if (!empty($meta['_cmb_sc'][0])) {
?>
    <div class="text-copy margin-bottom-basic">
      <p class="font-smaller">Listen to this article as audio:</p>
      <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="120" scrolling="no" frameborder="no"></iframe>
    </div>
<?php
  }
?>  
    <div id="single-articles-copy" class="text-copy margin-bottom-basic">
      <?php the_content(); ?>
    </div>
  </div>
</div>