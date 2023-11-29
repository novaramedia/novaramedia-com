<?php
get_header();

$category = get_category(get_query_var('cat'));

// pr($category);

$podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);
$podcast_copy = !empty($podcast_copy_override) ? $podcast_copy_override : 'Subscribe to the podcast';
$podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : false;

$credits = [
  array(
    'name' => 'Chal Ravens',
    'role' => 'Executive Producer',
    'bio' => 'The boss, la jefa, the big cheese. Chal is the executive producer of Novara FM and the rest of the Novara Media audio output.'
  ),
  array(
    'name' => 'Richard Hames',
    'role' => 'Producer & Host',
    'bio' => 'Doomplanner & internet shapecel Richard Hames is the producer of Novara FM and host of the monthly show Tech Against Tyranny (says GPT3).'
  ),
  array(
    'name' => 'James Butler',
    'role' => 'Host',
    'bio' => 'James is a writer, broadcaster and founder of Novara Media. From 2011 up until 2019 he hosted the majority of Novara FM episodes and therefore you will find him all throughout the archive.'
  ),
]
?>
<main id="main-content" class="category-archive novara-fm-archive">
  <div class="background-black">
    <div class="container">
      <div class="novara-fm-archive__header pt-6 pb-6">
        <?php echo nm_get_file('/dist/img/products/novara-fm/novarafm-wordmark.svg'); ?>
      </div>
    </div>
  </div>

  <div class="background-green">
    <div class="container pt-4 pb-4 text-align-center fs-4-sans">
      <?php echo category_description(); ?>
    </div>
  </div>

  <div class="background-black">
  <?php
    $lastest_fm = get_posts(array(
      'category' => $category->term_id,
      'posts_per_page' => 7
    ));

    if ($lastest_fm) {
      $post_id = $lastest_fm[0]->ID;
      $meta = get_post_meta($post_id);
  ?>
    <section class="container pt-6 pb-7 font-color-white">
      <div class="grid-row">
        <div class="grid-item offset-xxl-3 is-xxl-18">
          <div class="grid-row grid--nested">
            <div class="grid-item is-xxl-12">
              <h4 class="fs-3-sans font-bold font-uppercase mb-4">Latest Episode</h4>
              <h2 class="fs-7 mb-4"><?php echo get_the_title($post_id); ?></h2>
              <h4><?php render_standfirst($post_id); ?></h4>
              <p class="fs-4-serif mb-4"><?php render_short_description($post_id); ?></p>

              <?php
                if (!empty($meta['_cmb_sc'][0])) {
              ?>
              <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="120" scrolling="no" frameborder="no"></iframe>
              <?php
                if (!empty($meta['_cmb_is_resonance']) && $meta['_cmb_is_resonance'][0]) {
              ?>
                <div class="fs-1 mt-1">
                  <a target=_blank rel="nofollow" href="http://resonancefm.com/">powered by: Resonance FM</a>
                </div>
              <?php
                }
              }
              ?>
              <h4 class="fs-6 mt-2 mb-3">Listen now on:</h4>
              <ul class="u-inline-list fs-3-sans font-uppercase">
              <li><a class="nm-button nm-button--black">How do we do this tho?</a></li>
              <li><a class="nm-button nm-button--black">Just link to the overall feed?</a></li>
            </ul>
            </div>
            <div class="grid-item is-xxl-12">
              <?php render_thumbnail($post_id, 'col12', array(
                'class' => 'mt-1'
              )); ?>
            </div>
          </div>

        </div>
      </div>
    </section>
    <?php
      }
    ?>

    <section class="container">
      <h4 class="fs-4-sans font-uppercase">Recent Episodes</h4>
      <div class="grid-row">
      <?php
        foreach ($lastest_fm as $post) {
          $post_id = $post->ID;
      ?>
        <div class="grid-item is-xxl-5">
          <?php render_thumbnail($post_id, 'col12', array(
            'class' => 'ui-rounded-image'
          )); ?>
          <h2 class="fs-6-sans"><?php echo get_the_title($post_id); ?></h2>
          <h4><?php render_standfirst($post_id); ?></h4>
          <p><?php render_short_description($post_id); ?></p>
        </div>
      <?php
        }
      ?>
    </section>

    <section class="container pt-6 pb-6">
      <div class="grid-row">
        <div class="grid-item offset-xxl-4 is-xxl-16 background-white">
          <div class="pt-5 pb-5 pl-5 pr-5">
            <h2 class="fs-8 mb-4">About the show</h2>
            <p class="fs-6 mb-4">Novara Media’s flagship podcast is about the ideas that shape our past, present and future. With a desire to change the world—and ourselves along the way—Novara FM interrogates the people, ideologies and movements that wield power in our lives, from politics and culture to technology and the environment.</p>
            <h4 class="fs-6 mb-3">Listen now on:</h4>
            <ul class="u-inline-list fs-3-sans font-uppercase">
              <li><a class="nm-button nm-button--black">Apple Podcasts</a></li>
              <li><a class="nm-button nm-button--black">Spotify</a></li>
              <li><a class="nm-button nm-button--black">Pocket Casts</a></li>
              <li><a class="nm-button nm-button--black" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow">Everywhere else</a></li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php
    get_template_part('partials/support-section');
  ?>

  <div class="background-black font-color-white">
    <section class="container pt-6 pb-6">
      <div class="grid-row">
        <div class="grid-item is-xxl-24">
          <h3 class="fs-3-sans font-bold font-uppercase mb-4">Credits</h3>
        </div>
        <?php foreach($credits as $credit) {
      ?>
        <div class="grid-item is-xxl-6">
          <h4 class="fs-5-sans mb-1"><?php echo $credit['name']; ?></h4>
          <h6 class="fs-2 font-bold font-uppercase mb-1"><?php echo $credit['role']; ?></h6>
          <p class="fs-3-sans"><?php echo $credit['bio']; ?></p>
        </div>
      <?php
        }
      ?>
      </div>
    </section>
  </div>
</main>
<?php
get_footer();
?>
