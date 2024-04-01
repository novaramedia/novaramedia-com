<?php
get_header();
?>
<main id="main-content">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
?>
  <article id="page">
    <div class="container mt-4 mb-4">
      <div class="grid-row mb-4">
        <div class="flex-grid-item is-xxl-24">
          <h4 class="fs-3-sans font-uppercase font-bold"><?php the_title(); ?></h4>
        </div>
      </div>
      <div class="grid-row mb-4">
        <div class="flex-grid-item is-xxl-24 page-copy">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <?php
      $child_pages_wp_query = new WP_Query();
      $all_wp_pages = $child_pages_wp_query->query(array('post_type' => 'page'));

      $newsletter_pages = get_page_children($post->ID, $all_wp_pages);

      if ($newsletter_pages) {
        $index = 1;

        foreach($newsletter_pages as $newsletter) {
          $meta = get_post_meta($newsletter->ID);
          $mailchimp_key = !empty($meta['_nm_mailchimp_key']) ? $meta['_nm_mailchimp_key'][0] : false;

          if ($mailchimp_key) {
            get_template_part('partials/email-signup', null, array(
              'newsletter_page_id' => $newsletter->ID,
              'background-color' => $index % 2 === 0 ? 'light-purple' : false,
              'text-color' => $index % 2 === 0 ? 'white' : false,
            ));

            $index++;
          }
        }
      }
    ?>
  </article>
<?php
  }
} ?>
<?php
  get_template_part('partials/support-section');
?>
</main>
<?php
get_footer();
?>
