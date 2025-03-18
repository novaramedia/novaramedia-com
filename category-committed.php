<?php
get_header();

$credits_1 = array(
    array( 'Executive Producer', 'Chal Ravens' ),
    array( 'Commissioning Editors', 'Chal Ravens & Craig Gent' ),
    array( 'Factchecking', 'Steven Methven' ),
    array( 'Legal Advisor', 'Philip Wheeler' ),
);

$credits_2 = array(
    array( 'Music', 'Matt Huxley' ),
    array( 'Design & Digital', 'Pietro Garrone, Patrick Best & Max Ryan' ),
    array( 'Social Media Production', 'Luisa Le Voguer Couyet & Jonah Sealey Braverman' ),
);

$credits_thanks = array(
    array( 'Thanks', 'Arielle Angel, Colin Archdeacon, Michael Casper, Chelsea Converse, Claire Devoogd, Corey Eastwood, Donal Foreman, Dov Weinryb Grosghal, Sam McBride, Jess MilNeil, Josh Nathan-Kazis, Wilson Sherwin, Peter Smith and Zach Vary' ),
);

/**
 * Renders the podcast credit section.
 *
 * This function generates the HTML output for the podcast credit section,
 * including the podcast title, description, and any other relevant information.
 *
 * @return void
 */
function nm_render_podcast_credit( $credit ) {
  ?>
<div class="podcast-credit mb-3">
  <div class="font-size-8"><?php echo $credit[0]; ?></div>
  <?php echo $credit[1]; ?>
</div>
  <?php
}
?>
<main id="main-content" class="category-archive">
  <style type="text/css">
    .webp .committed__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/catagory-committed-background.webp'; ?>);
    }
    .avif .committed__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/catagory-committed-background.avif'; ?>);
    }
    .fallback .committed__backgrounded {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/catagory-committed-background.png'; ?>);
    }

    .committed-archive__container {
      height: 590px;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }

    .committed-archive__logo {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-logo-white.png'; ?>);
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      height: 200px;
    }

    .committed-archive__subtitle {
      background-image: url(<?php echo get_bloginfo( 'stylesheet_directory' ) . '/dist/img/specials/committed/committed-subtitle.png'; ?>);
      height: 100px;
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }

    .committed-archive-listen__box {
    display: flex;
     justify-content: center;

    }

    .committed-archive__box {
      border-radius: 0px;
      padding: .8rem 1rem;
      background-color:rgb(254, 105, 25);
    }

    @media screen and (max-width: 1336px) {
    }

    @media screen and (max-width: 1104px) {
              .committed-archive__container {
      height: 400px;
      background-position: center;
      }
    }

    @media screen and (max-width: 910px) {

    }

    @media screen and (max-width: 759px) {
      .committed-archive__container {
      height: 400px;
      background-position: center;
      }
      .committed-archive__logo {
        height: 70px;
      }
       .committed-archive__subtitle {
        height: 50px;
       }
    }
  </style>
<div class="committed-archive__container committed__backgrounded">
  <section class="container">
    <div class="flex-grid-row mb-4">
      <div class="flex-grid-item flex-item-xxl-12 mb-4">
        <a href="<?php echo home_url( 'category/audio/' ); ?>" class="ui-tag-block"><span class="ui-tag">Podcast</span></a>
      </div>
      <div class="flex-grid-item flex-item-xxl-12 text-align-center pt-10 pt-s-12">
        <h1 class="u-visuallyhidden">Committed</h1>
        <h3 class="u-visuallyhidden">Would you go to prison for your politics?</h3>
        <div class="committed-archive__logo"></div>
        <div class="committed-archive__subtitle "></div>
      </div>
    </div>
  </section>
</div>
  <section class="container">
    <div class="flex-grid-row mt-4 mb-4">
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-1 flex-item-l-10 flex-offset-xxl-2 flex-item-xxl-8">
        <h3 class="mb-4 font-size-12 font-weight-bold">Listen to the trailer:</h3>
        <iframe width="100%" height="166" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/1270698601&color=%23ffab70&auto_play=false&hide_related=true&show_comments=false&show_user=false&show_reposts=false&show_teaser=false"></iframe>
      </div>
    </div>
    <div class="flex-grid-row mt-4 mb-5">
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-1 flex-item-l-10 flex-offset-xxl-2 flex-item-xxl-8 font-serif text-paragraph-breaks">
        <p>INSERT COMMITTED TEXT</p>
        <p>INSERT COMMITTED TEXT</p>
        <p>INSERT COMMITTED TEXT</p>
      </div>
    </div>
    <div class="flex-grid-row mb-6">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-12 font-size-11 font-color-white committed-archive-listen__box text-links-underlined">
        <div class="committed-archive__box" style="display: inline-block;">
          <p class="font-weight-bold mb-0">Listen now on:</p><a href="https://podcasts.apple.com/us/podcast/foreign-agent-the-iras-american-connection/id1624937065?uo=4">Apple Podcasts</a>, <a href="https://open.spotify.com/show/4bc1ix28XO6XdJhqWpBBeZ">Spotify</a>,<br/><a href="https://podcasts.google.com/feed/aHR0cHM6Ly9mZWVkcy5wb2RjYXN0bWlycm9yLmNvbS9mb3JlaWduLWFnZW50">Google Podcasts</a>, <a href="https://feeds.podcastmirror.com/foreign-agent">RSS</a>
        </div>
      </div>
    </div>
  </section>
  <section id="posts" class="container">
    <div class="mb-6">
<?php
if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    $meta = get_post_meta( $post->ID );
    ?>
      <article class="flex-grid-row pt-6 pt-s-4" id="<?php echo $post->post_name; ?>">
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-xxl-1 flex-item-xxl-4 mobile-mb-4">
          <h4 class="font-size-9 text-uppercase font-weight-bold mb-2 mb-s-0"><?php echo $meta['_cmb_standfirst'][0]; ?></h4>
          <h3 class="font-size-13 font-weight-semibold js-fix-widows"><?php the_title(); ?></h3>
        </div>
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-item-xxl-6">
          <?php the_post_thumbnail( 'col12-16to9', array( 'class' => 'index-post-thumbnail' ) ); ?>
        </div>
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-2 flex-item-l-8 flex-offset-xl-3 flex-item-xl-7 flex-offset-xxl-3 flex-item-xxl-6 mt-4 mb-4 mobile-mt-4 mobile-mb-4">
          <iframe width="100%" height="115" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=<?php echo urlencode( $meta['_cmb_sc'][0] ); ?>&color=%23ffab70&inverse=true&auto_play=false&show_user=false&show_artwork=false"></iframe>
        </div>
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-2 flex-item-l-8 flex-offset-xl-3 flex-item-xl-7 flex-offset-xxl-3 flex-item-xxl-6 font-serif mb-4 text-paragraph-breaks">
          <?php the_content(); ?>
        </div>
      </article>
    <?php
  }
}
?>
    </div>
  </section>
  <div style="background-color: #FE6919;">
    <div class="container font-size-10 pt-10 pb-6 font-color-white">
      <div class="flex-grid-row mb-5">
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-xxl-1 flex-item-xxl-10">
          <h4 class="font-size-9 text-uppercase font-weight-bold">The producers</h4>
        </div>
      </div>
      <div class="flex-grid-row mb-5">
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-6 flex-offset-xxl-1 flex-item-xxl-5">
          <div>
            <span class="font-weight-semibold">Nate Lavey</span> is a documentary filmmaker and video journalist based in New York. He has covered social struggle in the aftermath of the Tunisian revolution, student uprisings in Quebec, and depleted nuclear production facilities in New York City. His first feature film, <em>Those Who Heard and Those Who Saw</em>, is about a network of internment camps that were built in Canada in the 1940s to imprison Jewish refugees.
          </div>
        </div>
        <div class="flex-grid-item flex-item-s-6 flex-item-xxl-5">
          <div>
            <span class="font-weight-semibold">Michael McCanne</span> is a writer based in New York. His work has been published by Art in America, Jacobin, The New Inquiry, Boston Review, Jewish Currents, and Dissent. His first film <em>A Minor Figure</em>, a collaboration with Jamie Weiss, was selected to premiere as part of the 2021 edition of Documenta Madrid.
          </div>
        </div>
      </div>
      <div class="flex-grid-row mb-5">
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-xxl-1 flex-item-xxl-10">
          <h4 class="font-size-9 text-uppercase font-weight-bold">Credits</h4>
        </div>
      </div>
      <div class="flex-grid-row">
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-6 flex-offset-xxl-1 flex-item-xxl-3">
          <div style="display: inline-block;">
            <?php
            for ( $i = 0, $size = count( $credits_1 ); $i < $size; $i++ ) {
                nm_render_podcast_credit( $credits_1[ $i ] );
            }
            ?>
          </div>
        </div>
        <div class="flex-grid-item flex-item-s-6 flex-item-l-3 flex-item-xxl-4">
          <div style="display: inline-block;">
            <?php
            for ( $i = 0, $size = count( $credits_2 ); $i < $size; $i++ ) {
                nm_render_podcast_credit( $credits_2[ $i ] );
            }
            ?>
          </div>
        </div>
        <div class="flex-grid-item flex-offset-s-2 flex-item-s-7 flex-item-l-4 flex-item-xxl-3 mt-s-4">
          <div style="display: inline-block;">
            <?php
            for ( $i = 0, $size = count( $credits_thanks ); $i < $size; $i++ ) {
                nm_render_podcast_credit( $credits_thanks[ $i ] );
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php
get_template_part(
    'partials/support-section',
    null,
    array(
        'override_text' => 'With your help, we’re making our podcasts bigger and better. Support independent journalism and set up a regular donation from just £1 a month.',
    )
);

get_footer();
?>
