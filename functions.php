<?php
function my_scripts_method() {

    $templateuri = get_template_directory_uri() . '/js/';

    $jslib = $templateuri."lib.js";
    wp_enqueue_script( 'jslib', $jslib,'','',true);
    $myscripts = $templateuri."my.min.js";
    wp_enqueue_script( 'myscripts', $myscripts,'','',true);

    wp_enqueue_style( 'site', get_stylesheet_directory_uri() . '/css/site.css' );

}
add_action('wp_enqueue_scripts', 'my_scripts_method');

if ( function_exists( 'add_theme_support' ) ) {
    add_theme_support( 'post-thumbnails' );
}
if ( function_exists( 'add_image_size' ) ) {
  add_image_size( 'admin-thumb', 150, 150, false );
  add_image_size( 'opengraph', 400, 300, true );

  add_image_size( 'col12-tv', 630, 355, true );
  add_image_size( 'col12-fm', 630, 226, true );

  add_image_size( 'col8-related', 414, 150, true );

  add_image_size( 'api-large', 630, 355, true );
  add_image_size( 'api-medium', 355, 200, true );
}


/*
override wp default gallery
get_template_part( 'lib/gallery' );
*/

get_template_part( 'lib/post-types' );
get_template_part( 'lib/meta-boxes' );

add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
function cmb_initialize_cmb_meta_boxes() {

  if ( ! class_exists( 'cmb_Meta_Box' ) )
    require_once 'lib/metabox/init.php';

}

/* disable that freaking admin bar */
add_filter('show_admin_bar', '__return_false');
/* turn off version in meta */
function no_generator() { return ''; }
add_filter( 'the_generator', 'no_generator' );
/* show thumbnails in admin lists */
add_filter('manage_posts_columns', 'new_add_post_thumbnail_column');
function new_add_post_thumbnail_column($cols){
  $cols['new_post_thumb'] = __('Thumbnail');
  return $cols;
}
add_action('manage_posts_custom_column', 'new_display_post_thumbnail_column', 5, 2);
function new_display_post_thumbnail_column($col, $id){
  switch($col){
    case 'new_post_thumb':
    if( function_exists('the_post_thumbnail') ) {
      echo the_post_thumbnail( 'admin-thumb' );
      }
    else
    echo 'Not supported in theme';
    break;
  }
}

function echoHomeWire($post1, $post2, $post3) { ?>
  <section class="home-wire font-white background-black">
    <div class="container">
      <div class="row">
        <div class="col col12">
          <a href="<?php echo $post1->permalink; ?>">
            <img src="<?php echo $post1->thumb_large; ?>" />
          </a>
        </div>
        <div class="col col12">
          <h3 class="padded-header home-wire-header">latest on <a href="http://wire.novaramedia.com"><svg class="logo-wire" xmlns="http://www.w3.org/2000/svg" width="2858.697" height="1209.221" viewBox="0 0 2858.697 1209.221"><path fill="#fff" d="M514.796.057h-176.109v285.642l-171.388-285.642h-167.299v582.533h176.117v-252.48l172.148 252.48h156.953l172.149-252.48v252.48h176.116v-582.533h-167.292l-171.395 285.641v-285.641m-379.321 198.297v343.593h-94.832v-501.248h103.649l235.037 391.732v-391.732h94.825v501.248h-104.404l-234.275-343.593zM677.145 626.221v418.09299999999996l-78.918-132.043-171.082-285.64v.018-.018l-171.583 285.64-79.417 132.043v-418.09299999999996h-176v583h245.623l172.377-252.741v-.259h17v.259l172.571 252.741h245.429v-583zM1029.598 1209.164v-1209.107h176.115v1209.107h-176.115zM2859.145 176.221v-176h-605v1209h605v-176h-429v-303h340v-176h-340v-378z"/><g fill="#fff"><path d="M1672.145 730.221"/><path d="M1672.145 730.221h-114v479h-176v-1209h470.348c204.777 0 255.683 192.302 255.683 326.653 0 297.412-80.829 379.188-230.173 398.001l.128.201 288.015 484.145h-209.219l-284.901-479m-113.881-176h236.369c80.868 0 138.253-51.65 138.253-192.637 0-134.351-56.085-185.363-138.253-185.363h-236.369v378z"/></g></svg></a></h3>

          <a href="<?php echo $post1->permalink; ?>">
            <h2 class="underline underline-white padded-header">
              <?php echo $post1->title; ?>
            </h2>
            <h4>by
              <?php echo $post1->author; ?>
            </h4>
          </a>
          <div class="home-wire-post-excerpt">
            <?php echo wp_trim_words($post1->short_desc, 55, '...'); ?>
          </div>
        </div>
    </div>
    <div class="row">
      <div class="col col6 home-wire-post-grid">
        <a href="<?php echo $post2->permalink; ?>">
          <img src="<?php echo $post2->thumb_medium; ?>" />
        </a>
      </div>
      <div class="col col6 home-wire-post-grid">
        <a href="<?php echo $post2->permalink; ?>">
          <h3 class="underline underline-white padded-header">
            <?php echo $post2->title; ?>
          </h3>
          <h4>
            <?php echo $post2->author; ?>
          </h4>
        </a>
      </div>
      <div class="col col6 home-wire-post-grid">
        <a href="<?php echo $post3->permalink; ?>">
          <img src="<?php echo $post3->thumb_medium; ?>" />
        </a>
      </div>
      <div class="col col6 home-wire-post-grid">
        <a href="<?php echo $post3->permalink; ?>">
          <h3 class="underline underline-white padded-header">
            <?php echo $post3->title; ?>
          </h3>
          <h4>
            <?php echo $post3->author; ?>
          </h4>
        </a>
      </div>
    </div>
  </section>
<?php
}
?>