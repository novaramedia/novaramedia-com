<?php
  $signups = NM_get_option('nm_front_page_links_bar', 'nm_front_page_links_bar_options');

  if (!empty($signups)) {
?>
<section class="front-page__products-bar ux-carousel container mt-4 mb-4 pb-4 ui-border-bottom">
  <div class="ux-carousel__wrapper">
    <div class="ux-carousel__nav-left ux-carousel__nav-left--disabled"></div>
    <div class="ux-carousel__nav-right"></div>
    <div class="ux-carousel__inner">
      <?php
        foreach ($signups as $index => $signup) {
          $title = isset($signup['title']) ? $signup['title'] : '';
          $link = isset($signup['link']) ? $signup['link'] : '';
          $copy = isset($signup['description']) ? $signup['description'] : '';
          $image_id = isset($signup['image_id']) ? $signup['image_id'] : false;
      ?>
      <div class="products-bar__item ux-carousel__item">
        <a href="<?php echo $link; ?>" <?php if (!strpos($link, 'novaramedia.com/')) {echo 'target="_blank"';} ?> rel="nofollow">
          <div class="products-bar__item-inner">
            <div class="products-bar__item-image mr-2">
              <?php
                if ($image_id) {
                  echo wp_get_attachment_image($image_id, 'col4-square', false, array('class' => 'ui-rounded-image'));
                }
              ?>
            </div>
            <div class="products-bar__item-text fs-3-sans">
              <h5><?php echo $title; ?></h5>
              <?php echo apply_filters('the_content', $copy); ?>
            </div>
          </div>
        </a>
      </div>
      <?php
        }
      ?>
    </div>
  </div>
</section>
<?php
  }
?>
