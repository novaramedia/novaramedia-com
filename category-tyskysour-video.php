<?php
get_header();
?>
<main id="main-content" class="category-archive tyskysour-archive">
<?php
  $is_front_page = get_query_var( 'paged', 0 ) === 0 ? true : false;

  $video = get_category_by_slug('video');
  $category = get_category(get_query_var('cat'));

  $embed_id = !empty(get_term_meta($category->term_id, '_nm_ts_latest_youtube_id', true)) ? get_term_meta($category->term_id, '_nm_ts_latest_youtube_id', true) : false;

  $team_image_id = !empty(get_term_meta($category->term_id, '_nm_ts_team_image_id', true)) ? get_term_meta($category->term_id, '_nm_ts_team_image_id', true) : false;

  $podcast_copy_override = get_term_meta($category->term_id, '_nm_podcast_text', true);
  $youtube_copy_override = get_term_meta($category->term_id, '_nm_youtube_text', true);

  $podcast_url = !empty(get_term_meta($category->term_id, '_nm_podcast_url', true)) ? get_term_meta($category->term_id, '_nm_podcast_url', true) : 'https://podfollow.com/novaramedia/view';

  if ($is_front_page) {
?>
<div class="background-black font-color-white">
  <section class="container padding-top-small">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-7 flex-item-l-4 flex-item-xxl-3 mobile-margin-bottom-tiny">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 346 65">
          <path fill="#FCFFFF" d="M15.1683 10.5611v41.2522h11.1282V10.5611h15.1683V1.20496H0v9.35614h15.1683Z"/>
          <path fill="#FCFFFF" d="m56.4205 56.3496 15.3101-41.1813H61.3112l-8.0094 25.0915H53.16l-8.293-25.0915H34.1642l12.8292 34.3768c.2836.7088.4253 1.4649.4253 2.2682 0 1.0868-.3308 2.0791-.9923 2.9769-.6143.8979-1.583 1.4176-2.9061 1.5594-.9923.0473-1.9846.0236-2.9769-.0709-.9924-.0945-1.9611-.189-2.9061-.2835v8.293c1.0396.0945 2.0555.1653 3.0478.2126 1.0396.0945 2.0792.1418 3.1187.1418 3.4495 0 6.143-.638 8.0804-1.9138 1.9373-1.2758 3.4495-3.4022 4.5363-6.3792ZM82.898 39.9054h-9.5688c.0945 2.4572.638 4.5127 1.6303 6.1666 1.0395 1.6066 2.339 2.9061 3.8984 3.8984 1.6066.9923 3.4258 1.7011 5.4577 2.1264 2.0319.4253 4.1111.6379 6.2375.6379 2.0791 0 4.111-.2126 6.0957-.6379 2.0318-.378 3.8272-1.0632 5.3872-2.0555 1.559-.9923 2.811-2.2918 3.756-3.8984.993-1.6539 1.489-3.6858 1.489-6.0957 0-1.7011-.331-3.1187-.993-4.2528-.661-1.1813-1.535-2.15-2.622-2.9061-1.087-.8033-2.339-1.4412-3.7568-1.9137-1.3703-.4726-2.7879-.8742-4.2528-1.205-1.4176-.3308-2.8115-.6379-4.1819-.9214-1.3703-.2836-2.5989-.5907-3.6858-.9215-1.0395-.378-1.8901-.8505-2.5516-1.4176-.6616-.567-.9923-1.2994-.9923-2.1973 0-.756.189-1.3467.567-1.772.378-.4725.8269-.8269 1.3467-1.0632.5671-.2362 1.1813-.378 1.8429-.4252.6615-.0945 1.2758-.1418 1.8429-.1418 1.7956 0 3.355.3544 4.6781 1.0632 1.323.6615 2.0555 1.961 2.1972 3.8984h9.5684c-.189-2.2682-.779-4.1347-1.772-5.5995-.945-1.5121-2.15-2.7171-3.614-3.6149-1.4653-.8978-3.1428-1.5357-5.0329-1.9138-1.8429-.378-3.7567-.567-5.7413-.567s-3.922.189-5.8122.567c-1.8901.3308-3.5912.9451-5.1033 1.8429-1.5121.8506-2.7407 2.0319-3.6858 3.544-.8978 1.5121-1.3467 3.4495-1.3467 5.8122 0 1.6066.3308 2.9769.9923 4.111.6616 1.0868 1.5357 2.0083 2.6226 2.7643 1.0868.7088 2.3154 1.2995 3.6857 1.772 1.4176.4253 2.8589.8033 4.3237 1.1341 3.5913.7561 6.3792 1.5121 8.3639 2.2682 2.0318.756 3.0478 1.8901 3.0478 3.4022 0 .8978-.2126 1.6539-.6379 2.2682-.4253.567-.9687 1.0396-1.6303 1.4176-.6143.3308-1.3231.5906-2.1264.7797-.756.1417-1.4884.2126-2.1972.2126-.9924 0-1.9611-.1181-2.9061-.3544-.8978-.2363-1.7011-.5907-2.4099-1.0632-.7088-.5198-1.2995-1.1577-1.7721-1.9138-.4252-.8033-.6379-1.7483-.6379-2.8352ZM114.093 1.20496V51.8133h10.065V39.1966l3.898-3.7566 10.065 16.3733h12.192l-15.381-23.1778 13.821-13.4672h-11.908L124.158 28.352V1.20496h-10.065ZM171.947 56.3496l15.31-41.1813h-10.42l-8.009 25.0915h-.142l-8.293-25.0915H149.69l12.83 34.3768c.283.7088.425 1.4649.425 2.2682 0 1.0868-.331 2.0791-.993 2.9769-.614.8979-1.582 1.4176-2.906 1.5594-.992.0473-1.984.0236-2.977-.0709-.992-.0945-1.961-.189-2.906-.2835v8.293c1.04.0945 2.056.1653 3.048.2126 1.04.0945 2.079.1418 3.119.1418 3.449 0 6.143-.638 8.08-1.9138 1.938-1.2758 3.45-3.4022 4.537-6.3792ZM199.275 35.0147h-10.774c-.047 3.1187.52 5.8122 1.701 8.0803 1.181 2.2682 2.764 4.1347 4.749 5.5996 2.032 1.4648 4.347 2.528 6.946 3.1896 2.646.7088 5.363 1.0632 8.151 1.0632 3.45 0 6.474-.4017 9.073-1.205 2.646-.8033 4.844-1.9138 6.592-3.3314 1.796-1.4648 3.142-3.1896 4.04-5.1742.898-1.9846 1.347-4.1347 1.347-6.4501 0-2.8352-.615-5.1506-1.843-6.9462-1.181-1.8429-2.599-3.3078-4.253-4.3946-1.654-1.0868-3.331-1.8665-5.032-2.339-1.654-.5198-2.954-.8742-3.899-1.0632-3.166-.8033-5.741-1.4649-7.726-1.9847-1.937-.5197-3.473-1.0395-4.607-1.5593-1.087-.5198-1.819-1.0868-2.197-1.7011-.378-.6143-.567-1.4176-.567-2.41 0-1.0868.236-1.9846.709-2.6934.472-.7088 1.063-1.2995 1.772-1.772.756-.47253 1.583-.80331 2.48-.99232.898-.18902 1.796-.28352 2.694-.28352 1.37 0 2.622.11813 3.756.3544 1.182.23626 2.221.63792 3.119 1.20494.898.5671 1.607 1.3467 2.127 2.3391.567.9923.897 2.2445.992 3.7566h10.774c0-2.9297-.567-5.4105-1.701-7.4424-1.087-2.07915-2.576-3.78027-4.466-5.10336-1.89-1.3231-4.064-2.26816-6.521-2.835203C214.301.307146 211.773 0 209.127 0c-2.268 0-4.536.307146-6.804.921437-2.269.614293-4.301 1.559363-6.096 2.835203-1.796 1.27584-3.261 2.88245-4.395 4.81984-1.087 1.89012-1.63 4.13462-1.63 6.73362 0 2.3154.425 4.3 1.276 5.9539.898 1.6066 2.055 2.9533 3.473 4.0402 1.418 1.0868 3.024 1.9846 4.82 2.6934 1.795.6615 3.638 1.2286 5.528 1.7011 1.843.5198 3.663.9923 5.458 1.4176 1.796.4253 3.402.9215 4.82 1.4885 1.418.567 2.552 1.2758 3.402 2.1264.898.8506 1.347 1.961 1.347 3.3314 0 1.2758-.331 2.339-.992 3.1896-.662.8033-1.489 1.4412-2.481 1.9137-.992.4725-2.056.8033-3.19.9923-1.134.1418-2.197.2127-3.189.2127-1.465 0-2.883-.1654-4.253-.4962-1.37-.378-2.575-.9214-3.615-1.6302-.992-.7561-1.796-1.7248-2.41-2.9061-.614-1.1813-.921-2.6226-.921-4.3237ZM245.589 33.5262c0-1.4648.141-2.906.425-4.3236.283-1.4176.756-2.6699 1.418-3.7567.708-1.0868 1.63-1.961 2.764-2.6225 1.134-.7088 2.552-1.0632 4.253-1.0632s3.118.3544 4.252 1.0632c1.182.6615 2.103 1.5357 2.765 2.6225.709 1.0868 1.205 2.3391 1.488 3.7567.284 1.4176.426 2.8588.426 4.3236 0 1.4649-.142 2.9061-.426 4.3237-.283 1.3704-.779 2.6226-1.488 3.7567-.662 1.0868-1.583 1.961-2.765 2.6225-1.134.6616-2.551.9923-4.252.9923-1.701 0-3.119-.3307-4.253-.9923-1.134-.6615-2.056-1.5357-2.764-2.6225-.662-1.1341-1.135-2.3863-1.418-3.7567-.284-1.4176-.425-2.8588-.425-4.3237Zm-10.065 0c0 2.9297.449 5.5759 1.346 7.9386.898 2.3627 2.174 4.3946 3.828 6.0957 1.654 1.6538 3.638 2.9297 5.954 3.8275 2.315.8978 4.914 1.3467 7.797 1.3467 2.882 0 5.481-.4489 7.796-1.3467 2.363-.8978 4.371-2.1737 6.025-3.8275 1.654-1.7011 2.93-3.733 3.828-6.0957.898-2.3627 1.347-5.0089 1.347-7.9386 0-2.9297-.449-5.5759-1.347-7.9385-.898-2.4099-2.174-4.4418-3.828-6.0957-1.654-1.7011-3.662-3.0006-6.025-3.8984-2.315-.9451-4.914-1.4176-7.796-1.4176-2.883 0-5.482.4725-7.797 1.4176-2.316.8978-4.3 2.1973-5.954 3.8984-1.654 1.6539-2.93 3.6858-3.828 6.0957-.897 2.3626-1.346 5.0088-1.346 7.9385ZM314.366 51.8133v-36.645h-10.065v19.2085c0 3.733-.615 6.4265-1.843 8.0803-1.229 1.6066-3.214 2.4099-5.954 2.4099-2.41 0-4.088-.7324-5.033-2.1972-.945-1.5121-1.417-3.7803-1.417-6.8045v-20.697h-10.065v22.5399c0 2.2681.189 4.3473.567 6.2374.425 1.8429 1.134 3.4259 2.126 4.749.992 1.2758 2.339 2.2681 4.04 2.9769 1.749.7088 3.97 1.0632 6.663 1.0632 2.126 0 4.206-.4725 6.237-1.4176 2.032-.945 3.686-2.4808 4.962-4.6072h.213v5.1034h9.569ZM322.004 15.1683v36.645h10.065V35.2982c0-1.6538.166-3.1896.497-4.6072.33-1.4176.874-2.6461 1.63-3.6857.803-1.0868 1.843-1.9374 3.119-2.5517 1.275-.6143 2.835-.9214 4.678-.9214.614 0 1.252.0472 1.913.1417.662.0473 1.229.1182 1.702.2127v-9.3562c-.804-.2363-1.536-.3544-2.198-.3544-1.276 0-2.504.189-3.686.567-1.181.3781-2.291.9215-3.331 1.6303-1.039.6615-1.961 1.4885-2.764 2.4808-.803.945-1.441 1.9846-1.914 3.1187h-.142v-6.8045h-9.569Z"/>
</svg>
      </div>
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-6 flex-offset-l-2 flex-item-l-3 flex-offset-xl-3 flex-item-xl-3 flex-offset-xxl-5 flex-item-xxl-2">
        <a class="nm-button nm-button--black font-size-s-0" href="<?php echo $podcast_url; ?>" target="_blank" rel="nofollow">Subscribe to<br/>the podcast</a>
      </div>
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-6 flex-item-l-3 flex-item-xl-3 flex-item-xxl-2">
        <a class="nm-button nm-button--red font-size-s-0" href="https://www.youtube.com/subscription_center?add_user=novaramedia" target="_blank" rel="nofollow">Subscribe to our<br/>YouTube channel</a>
      </div>
    </div>

    <div class="tyskysour-archive__liveplayer flex-grid-row">
      <div class="flex-grid-item flex-item-xxl-12">
        <div class="u-video-embed-container">
          <iframe class="youtube-player" type="text/html" src="<?php echo generate_youtube_embed_url($embed_id); ?>"></iframe>
        </div>
      </div>
    </div>

    <div class="flex-grid-row margin-top-basic mobile-margin-top-small">
      <div class="flex-grid-item flex-item-s-12 flex-item-m-9 flex-item-l-6 flex-item-xxl-5 font-size-2 font-size-s-1 padding-top-small mobile-padding-top-none">
        <?php echo category_description(); ?>
      </div>
      <div class="flex-grid-item flex-offset-s-1 flex-item-s-10 flex-offset-m-3 flex-item-m-7 flex-offset-l-0 flex-item-l-6 flex-offset-xxl-2 flex-item-xxl-5">
        <?php echo wp_get_attachment_image($team_image_id, 'gallery', false, array('class' => 'tyskysour-archive__about-team-image u-display-block')); ?>
      </div>
    </div>
  </section>
</div>

<div class="background-yellow">
  <section class="container padding-top-basic padding-bottom-small mobile-padding-top-small">
    <div class="flex-grid-row margin-bottom-basic mobile-margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <h2 class="font-size-3 font-size-s-2">Missed the show? Catch up here:</h2>
      </div>
    </div>
    <div class="flex-grid-row">
      <?php
        $i = 0;
        if (have_posts()) {
          while(have_posts() && $i < 4) {
            the_post();
        ?>
        <div class="flex-grid-item flex-item-s-6 flex-item-xxl-3 margin-bottom-small">
          <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('col6-16to9'); ?>
            <h6 class="js-fix-widows font-size-2 font-size-s-1 font-semibold margin-top-micro"><?php the_title(); ?></h6>
          <a href="<?php the_permalink(); ?>">
        </div>
        <?php
          $i++;
          }
        }
      ?>
    </div>
  </section>
</div>

<?php get_template_part('partials/support-section'); ?>

<?php
  } // end if front page
?>

  <section id="posts" class="container <?php echo $is_front_page ? 'margin-top-basic mobile-margin-top-small' : 'margin-top-small'; ?>">
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-s-12">
        <h4><?php echo $is_front_page ? 'More TyskySour' : 'TyskySour'; ?></h4>
      </div>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();

    get_template_part('partials/post-layouts/flex-post', null, array(
      'grid-item-classes' => 'flex-grid-item flex-item-s-12 flex-item-l-6 flex-item-xxl-4 margin-bottom-basic mobile-margin-bottom-tiny',
      'image-size' => 'col12-16to9',
    ));
  }
} else {
?>
    <article class="u-alert"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
<?php
} ?>
    </div>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-s-12">
        <?php get_template_part('partials/pagination'); ?>
      </div>
    </div>
  </section>
</main>
<?php
get_footer();
?>
