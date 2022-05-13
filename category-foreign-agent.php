<?php
get_header();

function renderPodcastCredit($credit) {
?>
<div class="podcast-credit margin-bottom-tiny">
  <div class="font-small-caps"><?php echo $credit[0]; ?></div>
  <?php echo $credit[1]; ?>
</div>
<?php
}
?>
<main id="main-content" class="category-archive foreign-agents-archive">
<?php
  $category = get_category(get_query_var('cat'));
  $podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : false;
?>
<div class="background-black font-color-white">
  <section class="container padding-top-basic padding-bottom-basic">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-xxl-12 text-align-center">
        <h3 class="font-size-2 margin-bottom-tiny"> The IRA’s American connection</h3>
        <h1 class="font-size-6">Foreign<br/>Agent</h1>
        <img src="" width="800" height="250" alt="Illustrative show cover" />
      </div>
    </div>
  </section>
</div>
  <section class="container">
    <div class="flex-grid-row margin-top-basic margin-bottom-large">
      <div class="flex-grid-item flex-item-s-12 flex-item-l-10 flex-offset-xxl-2 flex-item-xxl-8 font-serif font-size-3">
        <?php echo category_description(); ?>
      </div>
    </div>
  </section>
  <section id="posts" class="container">
    <div class="flex-grid-row margin-bottom-large">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-12 text-align-center font-size-2">
        Listen now on: <a href="">Apple Podcasts</a>, <a href="">Spotify</a>, <a href="">Google Podcasts</a>, <a href="">RSS</a>
      </div>
    </div>
    <div class="margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
    $description = !empty($meta['_cmb_short_desc'][0]) ? $meta['_cmb_short_desc'][0] : false;
?>
      <article class="foreign-agents-archive__episode flex-grid-row margin-bottom-large">
        <div class="flex-grid-item flex-item-s-12 flex-offset-xxl-1 flex-item-xxl-4">
          <h3 class="font-size-2 font-semibold"><?php the_title(); ?></h3>
        </div>
        <div class="flex-grid-item flex-item-s-12 flex-item-xxl-6 font-size-2 font-serif">
          <?php echo $description ? $description : the_excerpt(); ?>
        </div>

        <div class="flex-grid-item flex-item-s-12 flex-offset-xxl-1 flex-item-xxl-10 margin-top-small">
          <iframe width="100%" height="128" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1262818999&color=%23171717&auto_play=false&hide_related=true&show_comments=false&show_user=true&show_reposts=false&show_teaser=false"></iframe>
        </div>
      </article>
<?php
  }
} ?>
      <div class="flex-grid-row margin-bottom-large">
        <div class="foreign-agents-archive__episode flex-grid-item flex-item-s-12 flex-offset-xxl-3 flex-item-xxl-7">
          <h3 class="font-size-2 font-semibold">To be continued... Subscribe to the podcast feeds—or keep an eye on our social media—to listen to the next episodes.</h3>
        </div>
      </div>
    </div>
  </section>
  <div class="foreign-agents-archive__credits container">
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-xxl-12">
        <h4>The hosts</h4>
      </div>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-6">
        Host 1
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-6">
        Host 2
      </div>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-xxl-12">
        <h4>Full credits</h4>
      </div>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4">
        <?php
          $credits = array(
            array('Executive Producer', 'Chal Ravens'),
            array('Music', 'Matt Huxley'),
            array('Design', 'Pietro Garrone & Max Ryan'),
          );

          for ($i = 0, $size = count($credits); $i < $size; $i++) {
          renderPodcastCredit($credits[$i]);
          }
        ?>
      </div>
      <div class="flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-6">

      </div>
    </div>

  </div>
</main>
<?php
get_footer();
?>
