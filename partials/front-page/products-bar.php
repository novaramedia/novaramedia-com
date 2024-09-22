<?php
  $signups = NM_get_option('nm_front_page_links_bar', 'nm_front_page_links_bar_options');

  if (!empty($signups)) {
?>
<section class="front-page__products-bar ux-carousel container container--padded mt-4 mb-4 mt-s-3 mb-s-3">
  <div class="swiper">
    <div class="swiper-button-prev swiper-button-prev--disabled"><span class="only-desktop ui-chevron ui-chevron--left"></span></div>
    <div class="swiper-button-next"><span class="only-desktop ui-chevron ui-chevron--right"></span></div>
    <div class="swiper-wrapper">
      <?php
        if ($apology_post = check_for_apology_notice()) { // Temporary fix for the apology notice
          $post_id = $apology_post[0]->ID;
        ?>
        <div class="swiper-slide products-bar__item ux-carousel__item">
          <a href="<?php echo get_permalink($post_id); ?>">
            <div class="products-bar__item-inner" style="width: 100%;">
              <div class="products-bar__item-image mr-2">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAA1JREFUGFdj+P///38ACfsD/QVDRcoAAAAASUVORK5CYII=" alt="Blank image" class="ui-rounded-image" data-no-lazysizes="true" loading="eager">
              </div>
              <div class="products-bar__item-text font-size-9">
                <h5 class="font-weight-bold">Correction</h5>
                <?php echo get_the_title($post_id); ?>
              </div>
            </div>
          </a>
        </div>
        <?php
        }

        foreach ($signups as $index => $signup) {
          $title = isset($signup['title']) ? $signup['title'] : '';
          $link = isset($signup['link']) ? $signup['link'] : '';
          $copy = isset($signup['description']) ? $signup['description'] : '';
          $image_id = isset($signup['image_id']) ? $signup['image_id'] : false;
      ?>
      <div class="swiper-slide products-bar__item ux-carousel__item">
        <a href="<?php echo $link; ?>" <?php if (!strpos($link, 'novaramedia.com/')) {echo 'target="_blank"';} ?> rel="nofollow">
          <div class="products-bar__item-inner">
            <div class="products-bar__item-image mr-2">
              <?php
                if ($image_id) {
                  echo wp_get_attachment_image($image_id, 'col4-square', false, array('class' => 'ui-rounded-image', 'data-no-lazysizes' => true, 'loading' => 'eager'));
                }
              ?>
            </div>
            <div class="products-bar__item-text font-size-9">
              <h5 class="font-weight-bold"><?php echo $title; ?></h5>
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
  <div class="ui-border-bottom pt-4 pt-s-3"></div>
</section>
<?php
  }
?>
