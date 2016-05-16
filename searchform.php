<form role="search" method="get" id="searchform" class="searchform" action="http://local.novaramedia.com/">
  <div>
    <input id="search-input" type="text" value="" name="s" id="s">
    <input type="submit" id="searchsubmit" value="Search">
  </div>
</form>

<?php
$tags = get_terms( 'post_tag', array(
  'hide_empty' => false,
) );

if ( ! empty( $tags ) ) {
?>
<div id="tag-suggestions">
  <h3>Tag suggestions</h3>
  <ul id="suggested-tags">
<?php
  foreach ( $tags as $tag ) {
    $tag_link = get_term_link($tag->term_id);
    echo '<li class="suggested-tag" data-tag="' . $tag->slug . '"><a href="' . $tag_link . '">' . $tag->name . '</a></li>';
  }
?>
    
  </ul>
</div>
<?php
}
?>
