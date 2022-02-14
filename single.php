<?php
get_header();
?>
<main id="main-content">
<?php
  $post_status = get_post_status();
  if ($post_status === 'draft' || $post_status === 'pending') {
?>
  <div class="background-yellow">
    <div class="container">
      <div class="flex-grid-row padding-top-small padding-bottom-small">
        <div class="flex-grid-item flex-item-xxl-12">
          <h1 class="font-size-3 text-align-center font-uppercase">
            Caution: <?php echo $post_status; ?> post
          </h1>
        </div>
      </div>
    </div>
  </div>
<?php
   }
?>
  <article id="post" class="container margin-top-small margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    $categories = get_the_category();
    $topLevelCategory = array_filter($categories, 'only_top_level_category_filter');

    if (!$topLevelCategory) { // if there is no top level category set to post
      if ($categories[0]->parent) { // then check the first category set
        $topLevelCategory = get_category($categories[0]->parent); // and if there is a parent
      }
    } else {
      $topLevelCategory = array_values($topLevelCategory); // if there is a top level category
      $topLevelCategory = $topLevelCategory[0]; // get the first one (because there should only be one)
    }

    $category_link = get_category_link($topLevelCategory->term_id);

    $type_category = get_child_level_child_category($post->ID); // check for child level category for display

    $focus_terms = wp_get_post_terms($post->ID, 'focus'); // check for focus on the post
    $focus_tax = count($focus_terms) > 0 ? $focus_terms[0] : false;
?>

<div class="flex-grid-row margin-bottom-basic mobile-margin-bottom-small">
  <div class="flex-grid-item flex-item-xxl-12">
    <h4>
      <?php
        if ($focus_tax) {
          echo '<a href="' . get_term_link($focus_tax) . '">' . $focus_tax->name . '</a>';
        } else if ($type_category) {
          echo '<a href="' . get_term_link($type_category) . '">' . $type_category->name . '</a>';
        } else {
      ?>
        <a href="<?php echo $category_link; ?>"><?php echo $topLevelCategory->cat_name; ?></a>
      <?php
        }
      ?>
    </h4>
  </div>
</div>

<?php
    if ($topLevelCategory->slug === 'articles') {
      get_template_part('partials/singles/single-post-articles');
    } else if ($topLevelCategory->slug === 'audio') {
      get_template_part('partials/singles/single-post-audio');
    } else if ($topLevelCategory->slug === 'video') {
      get_template_part('partials/singles/single-post-video');
    }
  }
} else {
?>
    <div class="flex-grid-row">
      <article class="flex-grid-item flex-item-xxl-12"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
<?php
} ?>
  </article>
  <?php
    get_template_part('partials/support-section');

    get_template_part('partials/singles/single-related');
  ?>
</main>
<?php
get_footer();
?>
