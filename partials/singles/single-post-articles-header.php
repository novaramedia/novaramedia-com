<?php
  $meta = get_post_meta($post->ID);

  if (!empty($meta['bitly_url'])) {
    $share_url = $meta['bitly_url'][0];
  } else {
    $share_url = get_the_permalink($post->ID);
  }
?>

<div id="single-articles-title-row" class="row margin-top-small margin-bottom-basic mobile-margin-bottom-small">
  <div class="col col2"></div>
  <div class="col col20">
    <h1 id="single-articles-title" class="js-fix-widows"><?php the_title(); ?></h1>
  </div>
</div>

<div class="row margin-bottom-small">
  <div class="col col3"></div>
  <div class="col col9 mobile-margin-bottom-tiny">
    <h3>by <?php
      if (!empty($meta['_cmb_author'])) {
        echo $meta['_cmb_author'][0];
      } else {
        echo 'Novara Reporters';
      }
    ?></h3>
    <?php
      $author_twitters = get_post_meta($post->ID, '_cmb_author_twitter', true);

      if (!empty($author_twitters)) {
        if (!is_array($author_twitters)) {
          $author_twitters = array($author_twitters);
        }

        foreach($author_twitters as $handle) {
          echo '<a target="_blank" href="https://twitter.com/' . $handle . '">';
          echo '<h5>@' . ltrim($handle, '@') . '</h5>';
          echo '</a>';
        }
      }
    ?>
    <h6 class="margin-top-micro"><?php the_time('j F Y'); ?></h6>
  </div>
  <div class="col col9 margin-top-micro font-smaller">
    <ul class="inline-action-list">
    <?php
      if (!empty($meta['_igv_reading_time'])) {
        echo '<li>Estimated read time: ';
        if ($meta['_igv_reading_time'][0] > 1) {
          echo $meta['_igv_reading_time'][0] . ' mins';
        } else {
          echo $meta['_igv_reading_time'][0] . ' min';
        }
        echo '</li> ';
      }
    ?>
    </ul>
    <ul class="inline-action-list margin-top-micro">
      <li><?php render_tweet_link($share_url, $post->post_title, 'Tweet Article'); ?></li>
      <li><?php render_facebook_share_link($share_url); ?></li>
      <li><div class="kindleWidget" style="display:inline-block;cursor:pointer;white-space:nowrap;">Send to Kindle</div></li>
    </ul>
  </div>
</div>

<?php
  if (!empty($meta['_cmb_sc'][0])) {
?>
<div class="row margin-top-basic margin-bottom-small">
  <div class="col col3"></div>
  <div class="col col18 mobile-margin-bottom-tiny">
    <p class="font-smaller">Listen to this article as audio:</p>
    <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="120" scrolling="no" frameborder="no"></iframe>
  </div>
</div>
<?php
  }
?>