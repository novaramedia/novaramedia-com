<?php
$categories = get_the_category();

/**
 * Checks to see if category matches slug. For array_filter. !Needs to be made to check against an array next time we do a serial podcast
 *
 * @param object $category WP Term object to check
 *
 * @return Boolean
 */
function nm_is_serial_podcast_category($category) {
  return $category->slug === 'foreign-agent' ? true : false;
}

if ($serial_category_match = array_filter($categories, 'nm_is_serial_podcast_category')) {
  $serial_category_match = array_values($serial_category_match);
  $link = get_term_link($serial_category_match[0]);

  if (isset($link) && isset($post->post_name)) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $link . '#' . $post->post_name);
    exit;
  }
}

get_header();
?>
<main id="main-content">
<?php
  $post_status = get_post_status();
  if ($post_status === 'draft' || $post_status === 'pending') {
?>
  <div class="background-yellow">
    <div class="container">
      <div class="grid-row pt-4 pb-4">
        <div class="grid-item is-xxl-24">
          <h1 class="fs-6 text-align-center font-uppercase">
            Caution: <?php echo $post_status; ?> post
          </h1>
        </div>
      </div>
    </div>
  </div>
<?php
   }
?>
  <article id="post" class="container mt-4 mb-4">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    $top_level_category = get_the_top_level_category($post->ID);

    $category_link = get_category_link($top_level_category->term_id);

    $type_category = get_child_level_child_category($post->ID); // check for child level category for display

    $focus_terms = wp_get_post_terms($post->ID, 'focus'); // check for focus on the post
    $focus_tax = count($focus_terms) > 0 ? $focus_terms[0] : false;
?>

<div class="grid-row mb-4 mb-s-2">
  <div class="grid-item is-xxl-24">
    <h4 class="fs-3-sans font-uppercase font-bold">
      <?php
        if ($focus_tax) {
          echo '<a href="' . get_term_link($focus_tax) . '">' . $focus_tax->name . '</a>';
        } else if ($type_category) {
          echo '<a href="' . get_term_link($type_category) . '">' . $type_category->name . '</a>';
        } else {
      ?>
        <a href="<?php echo $category_link; ?>"><?php echo $top_level_category->cat_name; ?></a>
      <?php
        }
      ?>
    </h4>
  </div>
</div>

<?php
    if ($top_level_category->slug === 'articles') {
      get_template_part('partials/singles/single-post-articles');
    } else if ($top_level_category->slug === 'audio') {
      get_template_part('partials/singles/single-post-audio');
    } else if ($top_level_category->slug === 'video') {
      get_template_part('partials/singles/single-post-video');
    }
  }
} else {
?>
    <div class="grid-row">
      <article class="grid-item is-xxl-24"><?php _e('Sorry, no posts matched your criteria :['); ?></article>
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
