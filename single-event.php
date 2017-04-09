<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
?>
  <article id="event">
    <div class="container">
      <div class="row margin-bottom-basic">
        <div class="col col24">
          <h4>IRL</h4>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col24 text-align-center">
          <h1>{Event Date}: <?php the_title(); ?></h1>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col6"></div>
        <div class="col col12">
          <?php the_post_thumbnail('col12'); ?>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col2"></div>
        <div class="col col8">
          <h5>Time:</h5>

          <h5>Venue:</h5>

          <h5>Speakers:</h5>

          <h5>Host:</h5>
        </div>
        <div class="col col12">
          <?php the_content(); ?>
        </div>
      </div>
      <div class="row margin-bottom-basic">
        <div class="col col2"></div>
        <div class="col col20">
          Tickets
        </div>
      </div>
    </div>

    <div class="background-black font-color-white padding-top-mid padding-bottom-mid">
      <div class="container">
        <div class="row margin-bottom-basic">
          <div class="col col24">
            <h4>Gallery: {x/x}</h4>
          </div>
        </div>
        <div class="row margin-bottom-basic">
          <div class="col col24">
            {gallery}
          </div>
        </div>
      </div>
    </div>
  </article>
<?php
  }
} else {
?>
  <article id="event" class="container margin-bottom-basic">
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </article>
<?php
} ?>

  <?php
    get_template_part('partials/announcement');

    get_template_part('partials/support-section');
  ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>