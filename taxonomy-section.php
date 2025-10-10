<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_header();

$section_term = get_queried_object();

$active_state_checklist = [$section_term->term_id]; // haystack to search in for active state

if ($section_term->parent === 0) { // if queried section is already a top level section
  $top_level_section_id = $section_term->term_id;
} else { // otherwise get the ancestors and find the top level section
  $ancestors = get_ancestors($section_term->term_id, 'section');
  $top_level_section_id = array_reverse($ancestors)[0];
  $active_state_checklist = array_merge($active_state_checklist, $ancestors); // and update the active state haystack
}

$top_level_section = get_term($top_level_section_id, 'section');
?>
<main id="main-content" class="category-archive">
  <section id="posts" class="container mt-4">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <h1 class="font-size-10 font-weight-bold">
          <a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo get_term_link($top_level_section->term_id); ?>"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $top_level_section->name; ?></a>
        </h1>
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          $second_level_sections = get_terms('section', array('parent' => $top_level_section->term_id, 'hide_empty' => true));

          if (count($second_level_sections) > 0) { // render section level sections as submenu. highlight active section (or ancestor) in menu
            ?><ul class="section-archive__submenu font-size-9 mt-2"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            foreach($second_level_sections as $section) {
              ?><li <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} if (in_array($section->term_id, $active_state_checklist)) {echo 'class="font-weight-bold"';} ?>><a href="<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo get_term_link($section->term_id); ?>"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} echo $section->name; ?></a></li><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
            }
            ?></ul><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
          }
        ?>
      </div>
    </div>
  </section>
  <div class="container">
    <div class="grid-row mb-5">
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/archive-post', null, array(
      'grid-item-classes' => 'grid-item is-s-12 is-m-12 is-xl-8 is-xxl-6 mb-5',
      'image-size' => 'col12-16to9',
      'show-tags' => true,
    ));
  }
} else {
?>
    <article class="grid-item is-s-24"><?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
} ?>
    </div>
    <div class="grid-row mb-5">
      <div class="grid-item is-s-24">
        <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </div>
</main>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
get_footer();
?>
