
<?php
if (!isset($args['block_number'])) {
  $args['block_number'] = 1;
}

if (!isset($args['featured_posts_ids'])) {
  return;
}

$to_render = array();

for ($i = 0; $i < 4; $i++) { // depending on which block number, get the featured posts ids to display
  $index = $i + (4 * ($args['block_number'] - 1));
  if (is_numeric($args['featured_posts_ids'][$index])) {
    $to_render[] = $args['featured_posts_ids'][$index];
  }
}
?>
<div class="grid-row grid--nested<?php if ($args['block_number'] % 2 === 0) { echo " grid-row--reverse"; } ?>">
  <div class="featured-posts__primary grid-item is-xxl-16">
    <?php
    if (is_numeric($to_render[0])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-primary', null, array(
        'post_id' => $to_render[0],
        'show_related' => NM_get_option('nm_above_the_fold_featured_' . (1 + (4 * ($args['block_number'] - 1))) . '_show_related', 'nm_front_page_above_the_fold_featured_options'),
        'more_on_section' => NM_get_option('nm_above_the_fold_featured_' . (1 + (4 * ($args['block_number'] - 1))) . '_more_on_section', 'nm_front_page_above_the_fold_featured_options'),
        'is_product_linked' => NM_get_option('nm_above_the_fold_featured_' . (1 + (4 * ($args['block_number'] - 1))) . '_is_product_linked', 'nm_front_page_above_the_fold_featured_options'),
      ));
    }
    ?>
  </div>
  <div class="grid-item is-xxl-8">
  <?php
    if (is_numeric($to_render[1])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-secondary', null, array(
        'post_id' => $to_render[1],
      ));
    }

    if (is_numeric($to_render[2])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
        'post_id' => $to_render[2],
      ));
    }

    if (is_numeric($to_render[3])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
        'post_id' => $to_render[3],
      ));
    }
  ?>
  </div>
</div>
