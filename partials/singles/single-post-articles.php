<?php
  $meta = get_post_meta( $post->ID );

  $layout = ! empty( $meta['_cmb_article_layout'][0] ) ? $meta['_cmb_article_layout'][0] : 'basic';

  $articles_support_box_text = NM_get_option( 'nm_articles_support_box_text', 'nm_fundraising_options' );
  $support_box_override_text = ! empty( $meta['_cmb_support_box_override'][0] ) ? $meta['_cmb_support_box_override'][0] : false;

if ( ! empty( $meta['bitly_url'] ) ) {
  $share_url = $meta['bitly_url'][0];
} else {
  $share_url = get_the_permalink( $post->ID );
}

  get_template_part( 'partials/singles/articles/articles-header-' . $layout );
?>
<div class="grid-row mt-5 mt-s-4 mb-6">
  <div class="grid-item only-desktop is-m-24 is-l-24 is-xxl-4 mb-4">
    <?php
    if ( $articles_support_box_text || $support_box_override_text ) {
      ?>
    <a href="<?php echo home_url( 'support' ); ?>">
      <div id="single-article-support-box">
      <?php
      if ( $support_box_override_text ) {
        echo $support_box_override_text;
      } else {
        echo $articles_support_box_text;
      }
      ?>
      </div>
    </a>
      <?php
    }
    ?>
  </div>
  <div class="grid-item is-s-24 offset-s-0 is-m-20 offset-m-2 is-l-18 offset-l-3 is-xl-16 offset-xl-0 is-xxl-12 offset-xxl-2">
<?php
if ( ! empty( $meta['_cmb_sc'][0] ) ) {
  ?>
    <div class="text-copy mb-4">
      <p class="font-size-8">Listen to this article as audio:</p>
      <?php
        render_soundcloud_embed_iframe(
          $meta['_cmb_sc'][0],
          'small',
          true,
          array(
            'show_artwork' => 'false',
          )
        );
      ?>
    </div>
  <?php
}
?>
    <div id="single-articles-copy" class="mb-4" data-testid="post-content">
      <?php
      $content = get_the_content();
      $blocks  = parse_blocks( $content );

      // Check if content has any real Gutenberg blocks
      $has_blocks = false;
      foreach ( $blocks as $b ) {
        if ( ! empty( $b['blockName'] ) ) {
          $has_blocks = true;
          break;
        }
      }

      if ( ! $has_blocks ) {
        // Classic editor content - apply the_content filter once
        echo '<div class="text-copy">' . apply_filters( 'the_content', $content ) . '</div>';
      } else {
        // Block editor content - render each block properly.
        // We apply the_content filter to ensure embeds, shortcodes, etc. work correctly.
        // Note: WordPress core removes wpautop when processing blocks, but since most
        // block output is block-level HTML elements, wpautop typically won't affect it.
        // See docs/BLOCK-RENDERING.md for details and potential optimisation.
        foreach ( $blocks as $block ) {
          $block_name = $block['blockName'] ?? null;
          if ( $block_name === 'nm-wp/newsletter-signup' ) {
            // Newsletter block - render without text-copy wrapper
            echo render_block( $block );
          } else {
            // Other blocks - wrap in text-copy only if not empty
            $rendered_content = render_block( $block );
            if ( trim( $rendered_content ) !== '' ) {
              echo '<div class="text-copy">' . apply_filters( 'the_content', $rendered_content ) . '</div>';
            }
          }
        }
      }
      ?>
    </div>
<?php
/*
    Get contributors metadata and if set display in italics in a div below the main content.
    If there are multiple contributors set then each will have their own paragraph
*/
    $contributors_posts_array = get_contributors_array( $post->ID );

if ( $contributors_posts_array ) {
  ?>
    <div class="text-copy font-italic mb-4">
  <?php
  foreach ( $contributors_posts_array as $contributor ) {
    $bio = get_post_meta( $contributor->ID, '_nm_contributor_short_bio', true );

    if ( $bio ) {
      echo apply_filters( 'the_content', $bio );
    }
  }
  ?>
    </div>
  <?php
}
?>
    <div>
      <ul class="inline-action-list font-size-10">
        <li><?php render_tweet_link( $share_url, $post->post_title, 'Tweet article' ); ?></li>
        <li><?php render_facebook_share_link( $share_url, 'Share article on Facebook' ); ?></li>
        <li><?php render_email_share_link( $share_url, $post->post_title, 'Email this article' ); ?></li>
        <li><?php render_reddit_share_link( $share_url, $post->post_title, 'Post to Reddit' ); ?></li>
      </ul>
    </div>
  </div>
</div>
