
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
  <div class="featured-posts__primary grid-item is-l-24 is-xxl-16 mb-4">
    <?php
    if (is_numeric($to_render[0])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-primary', null, array(
        'post_id' => $to_render[0],
        'show_related' => NM_get_option('nm_above_the_fold_featured_' . (1 + (4 * ($args['block_number'] - 1))) . '_show_related', 'nm_front_page_above_the_fold_featured_options'),
        'more_on_section' => NM_get_option('nm_above_the_fold_featured_' . (1 + (4 * ($args['block_number'] - 1))) . '_more_on_section', 'nm_front_page_above_the_fold_featured_options'),
        'is_product_linked' => NM_get_option('nm_above_the_fold_featured_' . (1 + (4 * ($args['block_number'] - 1))) . '_is_product_linked', 'nm_front_page_above_the_fold_featured_options'),
        'has_embed' => ($args['block_number'] === 1 && NM_get_option('nm_above_the_fold_featured_1_has_embed', 'nm_front_page_above_the_fold_featured_options')) ? true : false,
      ));
    }
    ?>
  </div>
  <div class="grid-item is-l-24 is-xxl-8">
    <div class="grid-row grid--nested">
      <div class="grid-item is-l-12 is-xxl-24">
  <?php
    if (is_numeric($to_render[1])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-secondary', null, array(
        'post_id' => $to_render[1],
        'container_classes' => 'mb-4',
      ));
    }
  ?>
      </div>
      <div class="grid-item is-l-12 is-xxl-24">
  <?php
    if (is_numeric($to_render[2])) {
      get_template_part('partials/front-page/above-the-fold/featured-post-tertiary', null, array(
        'post_id' => $to_render[2],
        'container_classes' => 'mb-4',
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
  </div>
</div>
