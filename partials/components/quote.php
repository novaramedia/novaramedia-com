<?php  
  $copy = !empty($args['copy']) ? $args['copy'] : '';
  $attribution = !empty($args['attribution']) ? $args['attribution'] : '';

  $image_id = !empty($args['image_id']) ? $args['image_id'] : false;
?>
<div class="component-quote">
  <h3 class="font-size-1"><?php echo $copy; ?></h3>
  <h5 class="font-size-3 margin-top-small"><?php echo $attribution; ?></h5>
  <?php
    if ($image_id) {
  ?>
    <div class="component-quote__image-holder">
      <?php echo wp_get_attachment_image($image_id, 'col12', false, array('class' => 'component-quote__image')); ?>
    </div>  
  <?php
    }
  ?>
</div>