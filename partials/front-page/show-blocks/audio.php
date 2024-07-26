<?php
  function render_show($slug, $description, $logo_url = null, $background_color = 'black', $font_color = 'white', $logo = null) {
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
<div class="grid-item is-s-24 is-xxl-12 mb-4 font-color-<?php echo $font_color; ?> ui-rounded-box">
  <div class="front-page__audio-product front-page__audio-product--<?php echo $slug; ?> background-<?php echo $background_color; ?> pt-4 pl-4 pr-4 pb-4 ui-rounded-box">
    <a href="<?php echo get_term_link($category); ?>" class="ui-hover">
      <?php
        if ($logo_url) {
      ?>
      <div class="front-page__audio-product-logo mt-2 mb-5">
        <?php echo nm_get_file($logo_url); ?>
      </div>
      <?php
        }
      ?>
      <div class="font-size-11 mb-4">
        <?php echo $description; ?>
      </div>
    </a>
  <?php
    $post_id = $latest[0]->ID;
    $meta = get_post_meta($post_id);
  ?>
    <div class="background-white font-color-black pt-4 pb-4 pl-4 pr-4 mb-4 ui-rounded-box">
      <div class="grid-row grid--nested">
        <div class="grid-item is-xxl-10">
          <div class="layout-thumbnail-frame">
            <div class="layout-thumbnail-frame__inner mt-1 ml-1">
              <?php render_post_ui_tags($post_id, true, true, 'no-border'); ?>
            </div>
            <a href="<?php echo get_the_permalink($post_id); ?>" class="ui-hover">
              <?php render_thumbnail($post_id, 'col12', array(
                'class' => 'ui-rounded-image'
              )); ?>
            </a>
          </div>
        </div>
        <div class="grid-item is-xxl-14">
          <a href="<?php echo get_the_permalink($post_id); ?>" class="ui-hover">
            <h3 class="font-size-11 font-weight-bold mb-2"><?php echo get_the_title($post_id); ?></h3>
            <div class="font-size-10 font-weight-regular mb-3">
              <?php render_short_description($post_id); ?>
            </div>
          </a>
          <?php
            if (!empty($meta['_cmb_sc'][0])) {
          ?>
            <iframe width="100%" height="20" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>&inverse=false&auto_play=false&show_user=true"></iframe>
          <?php
          }
          ?>
        </div>
      </div>
    </div>

    <div class="ui-border-top ui-border--black pt-4">
      <a href="<?php echo get_term_link($category); ?>" class="ui-hover">
        <div class="grid-row grid--nested">
          <div class="grid-item is-xxl-12">
            <h4 class="font-size-9 font-weight-bold text-uppercase">Recent Episodes</h4>
          </div>
          <div class="grid-item is-xxl-12 text-align-right">
            <span class="font-size-9 font-weight-bold">See All</span>
          </div>
        </div>
      </a>
      <div class="grid-row grid--nested">
      <?php
        array_shift($latest);
        foreach ($latest as $post) {
          $post_id = $post->ID;
      ?>
        <div class="grid-item is-m-24 is-xxl-12 mt-2 mb-2">
          <a href="<?php echo get_the_permalink($post_id); ?>" class="ui-hover">
            <div class="font-size-8 font-weight-bold mb-2">
              <?php echo get_the_time('j F Y', $post_id); ?>
            </div>
          </a>
          <h4 class="font-size-10 font-weight-bold mb-2">
            <?php render_post_ui_tags($post_id, false, true); ?> <a href="<?php echo get_the_permalink($post_id); ?>" class="ui-hover"><?php echo get_the_title($post_id); ?></a>
          </h4>
          <a href="<?php echo get_the_permalink($post_id); ?>" class="ui-hover">
            <div>
              <?php render_short_description($post_id); ?>
            </div>
          </a>
        </div>
      <?php
        }
      ?>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
<section class="front-page__audio-products container mt-4 mb-4">
  <div class="grid-row">
    <?php
      render_show(
        'novarafm',
        'Novara Media’s flagship podcast is about the ideas that shape our past, present and future. With a desire to change the world—and ourselves along the way—Novara FM interrogates the people, ideologies and movements that wield power in our lives, from politics and culture to technology and the environment.',
        '/dist/img/products/novara-fm/novarafm-wordmark.svg',
        'green',
        'black'
      );
      render_show(
        'acfm',
        'The home of the weird left. Nadia Idle, Jeremy Gilbert and Keir Milburn examine the links between left-wing politics, culture, music and experiences of collective joy.',
        '/dist/img/products/acfm/acfm-logo.svg',
        'light-blue',
        'black'
      ); ?>
  </div>
</section>
