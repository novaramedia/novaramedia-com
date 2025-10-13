<?php
get_header();
?>
<main id="main-content">
  <article id="page">
    <div class="container mt-4 mb-4">
      <div class="grid-row mt-5 mb-5 mt-s-4 mb-s-4">
        <div class="flex-grid-item is-xxl-24 page-copy">
          We have an expanding range of email newsletters that are free to subscribe to. Choose your options below.
        </div>
      </div>
    </div>
    <?php
    if ( have_posts() ) {
      $index = 1;
      while ( have_posts() ) {
        ++$index;
        the_post();
        $meta = get_post_meta( $post->ID );
        $mailchimp_key = ! empty( $meta['_nm_mailchimp_key'] ) ? $meta['_nm_mailchimp_key'][0] : false;

        if ( $mailchimp_key ) {
          $background_color = $index % 2 === 0 ? 'white' : false;
          $text_color = $index % 2 === 0 ? 'black' : false;

          if ( $post->post_name === 'the-cortado' ) {
            $background_color = 'black';
            $text_color = 'white';
          }

          get_template_part(
            'partials/email-signup',
            null,
            array(
              'newsletter_page_id' => $post->ID,
              'background-color' => $background_color,
              'text-color' => $text_color,
              'hide-discover' => true,
              'hide-border' => true,
            )
          );
        }
      }
    }
    ?>
  </article>
<?php
  get_template_part( 'partials/support-section' );
?>
</main>
<?php
get_footer();
?>
