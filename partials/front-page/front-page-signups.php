<?php
  $signups = NM_get_option('nm_front_page_links_bar', 'nm_front_page_links_bar_options');

  if (!empty($signups)) {
    $number_of_signups = count($signups);

    // only show max 4 signups
    if ($number_of_signups > 4) {
      $number_of_signups = 4;
    }

    $cols = 24 / $number_of_signups;
?>
<section id="front-page-signups" class="container margin-bottom-small">
  <div class="row">
    <?php
      $i = 0;
      while ($i <= ($number_of_signups - 1)) {
        $title = isset($signups[$i]['title']) ? $signups[$i]['title'] : '';
        $link = isset($signups[$i]['link']) ? $signups[$i]['link'] : '';
        $copy = isset($signups[$i]['description']) ? $signups[$i]['description'] : '';
        $signup_text = isset($signups[$i]['signup_text']) ? $signups[$i]['signup_text'] : false;
        $image_id = isset($signups[$i]['image_id']) ? $signups[$i]['image_id'] : false;
    ?>
    <div class="col col<?php echo $cols;?>">
      <a class="front-page-signup" href="<?php echo $link; ?>" <?php if (!strpos($link, 'novaramedia.com/')) {echo 'target="_blank"';} ?> rel="nofollow">
        <div class="front-page-signup__image">
            <?php
              if ($image_id) {
                echo wp_get_attachment_image($image_id, 'col4-square');
              }
            ?>
        </div>

        <div class="front-page-signup__text font-smaller font-tracking-medium">
            <h5><?php echo $title; ?></h5>
            <?php echo apply_filters('the_content', $copy); ?>
        </div>
      </a>
    </div>
    <?php
        $i++;
      }
    ?>
  </div>
</section>
<?php
  }
?>