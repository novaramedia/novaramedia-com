<?php
  $signups = IGV_get_option('home_signups');

  if (!empty($signups)) {
    $number_of_signups = count($signups);

    // only show max 4 signups
    if ($number_of_signups > 4) {
      $number_of_signups = 4;
    }

    $cols = 24 / $number_of_signups;
?>
<section id="home-signups" class="container margin-bottom-large mobile-margin-bottom-basic">
  <div class="row">
     <div class="col col24 margin-bottom-small">
      <h4>Signups</h4>
    </div>
  </div>
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
      <a href="<?php echo $link; ?>" target="_blank" rel="nofollow">
        <h5 class="margin-bottom-tiny"><?php echo $title; ?></h5>
        <?php
          if ($image_id) {
            echo wp_get_attachment_image($image_id, 'col' . $cols);
          }
        ?>
        <div class="signup-copy">
          <?php echo apply_filters('the_content', $copy); ?>
        </div>
        <button class="button"><?php if (!empty($signup_text)) {
          echo $signup_text;
        } else {
          echo 'Sign up here';
        }?></button>
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