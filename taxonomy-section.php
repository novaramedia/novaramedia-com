<?php
get_header();

$show_support_banner = NM_get_option('nm_fundraising_settings_header_cta_is_displayed', 'nm_fundraising_options');

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
<?php if ($show_support_banner !== false) {
    get_template_part('partials/front-page/front-page-support-banner');
  }
?>
  <section id="posts" class="container margin-top-small">
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6">
        <h1 class="font-size-3">
          <a href="<?php echo get_term_link($top_level_section->term_id); ?>"><?php echo $top_level_section->name; ?></a>
        </h1>
        <?php
          $second_level_sections = get_terms('section', array('parent' => $top_level_section->term_id, 'hide_empty' => true));

          if (count($second_level_sections) > 0) { // render section level sections as submenu. highlight active section (or ancestor) in menu
            ?><ul class="section-archive__submenu margin-top-micro"><?php
            foreach($second_level_sections as $section) {
              ?><li <?php if (in_array($section->term_id, $active_state_checklist)) {echo 'class="font-bold"';} ?>><a href="<?php echo get_term_link($section->term_id); ?>"><?php echo $section->name; ?></a></li><?php
            }
            ?></ul><?php
          }
        ?>
      </div>
    </div>
  </section>
  <div class="container">
    <div class="flex-grid-row margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4 margin-bottom-basic',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="flex-grid-item flex-item-s-12"><?php _e('Sorry, nothing matched your criteria :/'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </div>
</main>
<?php
get_footer();
?>
