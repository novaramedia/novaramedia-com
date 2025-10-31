<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

get_header();
?>
<main id="main-content">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );

    $mailchimp_key = ! empty( $meta['_nm_mailchimp_key'] ) ? $meta['_nm_mailchimp_key'][0] : false;
    $title_size = ! empty( $meta['_nm_title_size'] ) ? $meta['_nm_title_size'][0] : false;
    $youtube_id = ! empty( $meta['_nm_youtube_id'] ) ? $meta['_nm_youtube_id'][0] : false;
    $support_override = ! empty( $meta['_nm_support_text'] ) ? $meta['_nm_support_text'][0] : false;

    $settings_title_classes = array(
      'huge'    => 'font-size-21 font-size-s-18',
      'big'     => 'font-size-19 font-size-s-16',
      'medium'  => 'font-size-17 font-size-s-15',
      'smaller' => 'font-size-15 font-size-s-13',
    );
    ?>
  <article id="page">
    <div class="background-black font-color-white">
      <div class="container pt-4 pb-6">
        <div class="grid-row">
          <div class="grid-item is-xxl-24">
            <a href="<?php echo get_post_type_archive_link( 'newsletter' ); ?>">
              <h4 class="font-size-10 font-weight-bold text-uppercase">Newsletter</h4>
            </a>
            <h1 class="font-weight-bold text-wrap-pretty
            <?php
              echo $title_size ? $settings_title_classes[ $title_size ] : $settings_title_classes['medium'];
            ?>
            mt-2"><?php the_title(); ?></h1>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="grid-row mt-4 mb-4">
        <div class="grid-item is-m-24 is-xxl-12">
          <?php
          if ( $youtube_id ) {
            ?>
          <div class="u-video-embed-container mb-4">
            <iframe class="youtube-player" type="text/html" src="<?php echo esc_url( generate_youtube_embed_url( $youtube_id, true ) ); ?>" allow="autoplay" allowfullscreen></iframe>
          </div>
            <?php
          } else {
            the_post_thumbnail( 'col8', array( 'class' => 'ui-rounded-image' ) );
          }
          ?>
        </div>
        <div class="grid-item offset-s-0 is-s-24 offset-m-1 is-m-20 is-xxl-12">
          <div class="font-size-12 font-weight-semibold mb-4">
            <?php the_content(); ?>
          </div>
          <?php
          if ( $mailchimp_key ) {
            render_mailchimp_signup_form( $mailchimp_key, 'white', 'black' );
          }
          ?>
        </div>
      </div>
    </div>
    <?php
    if ( $support_override ) {
      get_template_part(
        'partials/support-section',
        null,
        array(
          'override_text'     => $support_override,
          'container_classes' => 'mb-4',
        )
      );
    } else {
      get_template_part(
        'partials/support-section',
        null,
        array(
          'container_classes' => 'mb-4',
        )
      );
    }
    ?>
    </article>
    <?php
  }
}
?>
</main>
<?php
get_footer();
?>
