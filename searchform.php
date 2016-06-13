<div class="background-black padding-top-basic padding-bottom-basic">
  <div class="container">
    <div class="row">
      <div class="col col24">
        <form role="search" method="get" id="search-form" class="padding-small u-cf" action="<?php echo site_url(); ?>" autocomplete="off">
          <input id="search-input" class="font-italic padding-tiny" type="text" value="" name="s">
          <button type="submit" id="search-submit" class="padding-tiny background-white font-color-black"><i class="icon-search"></i></button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$tags = get_terms( 'post_tag', array(
  'hide_empty' => false,
) );

if ( ! empty( $tags ) ) {
?>
<div id="tag-suggestions" class="background-light-gray font-color-white padding-top-basic padding-bottom-basic">
  <div class="container">
    <div class="row">
      <div class="col col6">
        <h4>Tag suggestions</h4>
      </div>
      <div class="col col18">
        <ul id="suggested-tags">
<?php
  foreach ( $tags as $tag ) {
    $tag_link = get_term_link($tag->term_id);
    echo '<li class="suggested-tag" data-tag="' . $tag->slug . '"><a href="' . $tag_link . '">' . $tag->name . '</a></li>';
  }
?>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php
}
?>
