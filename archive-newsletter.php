<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

get_header();
?>
<main id="main-content" class="mb-4">
  <article id="page">
    <div class="container mt-4 mb-4">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <h1 class="font-size-9 font-weight-bold">Newsletters</h1>
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
          $background_color = $index % 2 === 0 ? 'gray-base' : false; // this is to alternate the background colors
          $text_color = $index % 2 === 0 ? 'black' : false;
          $button_color = $index % 2 === 0 ? 'black' : false;

          if ( $post->post_name === 'the-cortado' ) {
            $background_color = 'black';
            $text_color = 'white';
          }

          get_template_part(
            'partials/email-signup',
            null,
            array(
              'newsletter_page_id' => $post->ID,
              'background-color'   => $background_color,
              'text-color'         => $text_color,
              'button-color'       => $button_color,
              'hide-discover'      => true,
              'hide-border'        => true,
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
