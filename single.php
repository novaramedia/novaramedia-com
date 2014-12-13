<?php
$meta = get_post_meta($post->ID);
$cats = get_the_category();
$cat = array_shift($cats);
$type = $cat->slug;
if($type === 'txt') {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: ".$meta['_cmb_redirect'][0]);
}
get_header();
?>

	<!-- main content -->

  <section id="main-content" class="container">

    <!-- main posts loop -->
    <section id="single-post">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
  $meta = get_post_meta($post->ID);
?>

      <article <?php post_class('row'); ?> id="post-<?php the_ID(); ?>">

        <div class="col col18">

          <h1 class="single-post-title underline"><?php the_title(); ?></h1>

<?php
if (!empty($meta['_cmb_utube'][0])) {
?>
          <div class="video-holder">
            <iframe class="youtube-player" type="text/html" width="100%" height="490" src="http://www.youtube.com/embed/<?php echo $meta['_cmb_utube'][0]; ?>?autohide=2&amp;modestbranding=1&amp;origin=http://novaramedia.com&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
          </div>
<?php
}

if (!empty($meta['_cmb_sc'][0])) {
?>
            <iframe src="https://w.soundcloud.com/player/?url=<?php echo urlencode($meta['_cmb_sc'][0]); ?>" width="100%" height="200" scrolling="no" frameborder="no"></iframe>

<?php
  if (strstr($meta['_cmb_sc'][0], 'resonance-fm')) {
?>
    				<div class="font-mono">
    					<a target=_blank href="http://resonancefm.com/">powered by: Resonance FM</a>
    				</div>
<?php
  }
}
?>
        </div>

        <div class="col col6">

          <div class="single-post-sidebar-item underline">
            <ul class="u-inline-list">
      				<li><fb:like href="<?php the_permalink() ?>" send="true" layout="button_count" width="450" show_faces="false"></fb:like></li>
      				<li class="twitter-button-fix"><a href="https://twitter.com/share" data-url="<?php the_permalink() ?>" data-via="novaramedia" data-related="novaramedia" class="twitter-share-button" data-dnt="true" data-count="none">Tweet</a></li>
      			</ul>
          </div>

          <?php if (!empty($meta['_cmb_dl'][0]) || !empty($meta['_cmb_dl_mp3'][0]) || !empty($meta['_cmb_mobi'][0]) || !empty($meta['_cmb_epub'][0])) { ?>

          <div class="single-post-sidebar-item underline row">
              <h4>Downloads:</h4>
              <ul>
                <?php if (!empty($meta['_cmb_dl_mp3'][0])) { ?>
        					<li><a target="_blank" href="<?php echo $meta['_cmb_dl_mp3'][0]; ?>" download="<?php echo the_title(null, null, FALSE); ?>.mp3">&#8605; mp3</a></li><?php } ?>
        					<?php if (!empty($meta['_cmb_dl'][0])) { ?>
        					<li><a target="_blank" href="<?php echo $meta['_cmb_dl'][0]; ?>"><li>&#8605; Download</li></li><?php } ?>
        					<?php if (!empty($meta['_cmb_mobi'][0])) { ?>
        					<li><a target="_blank" href="<?php echo $meta['_cmb_mobi'][0]; ?>"><li>&#8605; mobi</li></li><?php } ?>
        					<?php if (!empty($meta['_cmb_epub'][0])) { ?>
        					<li><a target="_blank" href="<?php echo $meta['_cmb_epub'][0]; ?>"><li>&#8605; epub</li></li><?php } ?>
              </ul>
          </div>
          <?php } ?>

<?php
/* This is to support the old limited resources */
if (!empty($meta['_cmb_link1'][0])) {
?>
    			<div class="single-post-sidebar-item underline">
    				<h4>Resources</h4>
    				<ul>
    					<a target="_blank" href="<?php echo $meta['_cmb_link1url'][0]; ?>"><li>&#8605; <?php echo $meta['_cmb_link1'][0]; ?></li></a>
<?php
if (!empty($meta['_cmb_link2'][0])) {
?>
              <a target="_blank" href="<?php echo $meta['_cmb_link2url'][0]; ?>"><li>&#8605; <?php echo $meta['_cmb_link2'][0]; ?></li></a>
<?php } ?>
<?php
if (!empty($meta['_cmb_link3'][0])) {
?>
              <a target="_blank" href="<?php echo $meta['_cmb_link3url'][0]; ?>"><li>&#8605; <?php echo $meta['_cmb_link3'][0]; ?></li></a>
<?php } ?>
<?php if (!empty($meta['_cmb_link4'][0])) {
?>
              <a target="_blank" href="<?php echo $meta['_cmb_link4url'][0]; ?>"><li>&#8605; <?php echo $meta['_cmb_link4'][0]; ?></li></a>
              <?php } ?>
<?php if (!empty($meta['_cmb_link5'][0])) {
?>
              <a target="_blank" href="<?php echo $meta['_cmb_link5url'][0]; ?>"><li>&#8605; <?php echo $meta['_cmb_link5'][0]; ?></li></a>
              <?php } ?>
    				</ul>
    			</div>
<?php
}

/* This is resources from a repeating meta field */
$resources = get_post_meta($post->ID, '_cmb_resources');
if(!empty($resources)) {
?>
    			<div class="single-post-sidebar-item underline">
    				<h4>Resources</h4>
    				<ul>
<?php
  foreach($resources[0] as $resource) {
    echo '  <a target="_blank" href="' . $resource['link'] . '"><li>&#8605; ' . $resource['title'] . '</li></a>';
  }
?>
    				</ul>
    			</div>
<?php
}
?>

          <div class="single-post-sidebar-item underline row">
            <h4>Tags:</h4>
            <?php the_tags('&#8605;', ' &#8605;'); ?>
          </div>

<?php
if (!empty($meta['_cmb_utube_2'][0])) {
?>
          <div class="single-post-sidebar-item underline row">
            <div class="video-holder">
  						<iframe id="ytplayer2" type="text/html" width="850" height="600" src="http://www.youtube.com/embed/<?php echo $meta['_cmb_utube_2'][0]; ?>?autohide=2&amp;modestbranding=1&amp;origin=http://novaramedia.com&amp;showinfo=0&amp;theme=light&amp;rel=0"></iframe>
  					</div>
          </div>
<?php
}
?>

          <div class="single-post-sidebar-item">
            <?php the_content(); ?>
          </div>

        </div>

      </article>

      <!-- end post -->

      <div class="row">
        <div class="col col24 underline divider"></div>
      </div>

      <div class="row">

        <div class="col col18 divider">
          <h2>Related Posts:</h2>
        </div>

      </div>

      <div id="single-post-related-posts" class="row masonry">

<?php
$orig_post = $post;
global $post;
$tags = wp_get_post_tags($post->ID);
if ($tags) {
	$tag_ids = array();
	foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
	$args=array(
		'tag__in' => $tag_ids,
		'post__not_in' => array($post->ID),
		'posts_per_page'=>6
	);
	$my_query = new wp_query( $args );
	if( $my_query->have_posts() ) {
		while( $my_query->have_posts() ) {
			$my_query->the_post();
			$meta = get_post_meta($post->ID);
			?>
  			<article class="related-post col col8">
    			<a href="<?php the_permalink() ?>">
    				<?php the_post_thumbnail('col8-related'); ?>
    				<h3><?php the_title(); ?></h3>
    				<div class="post-short-desc"><?php echo wpautop($meta['_cmb_short-desc'][0]); ?></div>
    			</a>
  			</article>
<?php
	  }
	}
}
$post = $orig_post;
wp_reset_query();
?>

      </div>

    <?php endwhile; else: ?>
      <p><?php _e('Sorry, no posts matched your criteria :{'); ?></p>
    <?php endif; ?>

    </section>

  <!-- end main-content -->

  </section>

<?php get_footer(); ?>