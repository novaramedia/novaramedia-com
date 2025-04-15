<?php
get_header();

$credits = array(
    'producers'      => 'Richard Hames and Chal Ravens',
    'music_sound'    => 'Matt Huxley',
    'design_digital' => 'Pietro Garrone, Filippo Marra and Kimberley Dobney',
    'social_media'   => 'Dunya Kamal and Bronte Dow',
    'thanks'         => 'All of the prisoners, their partners, friends and family members who spoke to us for this podcast. Thanks to their prison buddies: Alex, Jamie, Kate and Pia, and to Bertie Coyle from Just Stop Oil.',
);

$base_image_path = get_stylesheet_directory_uri() . '/src/img/specials/committed/';

/**
 * Renders the podcast credit section.
 *
 * This function generates the HTML output for the podcast credit section,
 * including the podcast title, description, and any other relevant information.
 *
 * @return void
 */
function nm_render_committed_credit( $credit ) {
  ?>
<div class="podcast-credit">
  <div><?php echo $credit[0]; ?></div>
  <?php echo $credit[1]; ?>
</div>
  <?php
}
?>
<main id="main-content" class="catagory-archive">
  <style type="text/css">
    .webp .committed__backgrounded {
      background-image: url(<?php echo $base_image_path . 'catagory-committed-background.webp'; ?>);
    }
    .avif .committed__backgrounded {
      background-image: url(<?php echo $base_image_path . 'catagory-committed-background.avif'; ?>);
    }
    .fallback .committed__backgrounded {
      background-image: url(<?php echo $base_image_path . 'catagory-committed-background.png'; ?>);
    }

    .committed-archive__container {
      height: 590px;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }

    .committed-archive__logo,  .committed-credits__logo {
      background-image: url(<?php echo $base_image_path . 'committed-logo-white.png'; ?>);
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }

     .committed-archive__logo {
      height: 180px;
     }

    .committed-credits__logo {
      height: 80px;
    }

    .committed-archive__subtitle {
      background-image: url(<?php echo $base_image_path . 'committed-subtitle.png'; ?>);
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
      background-image: url(<?php echo $base_image_path . 'committed-listen-now-background.png'; ?>);
      padding: 25px 30px;
      background-size: 100% 100%;
    }

    .committed-listen-now-title {
      background-image: url(<?php echo $base_image_path . 'committed-listen-now-title.png'; ?>);
      background-size: contain;
      background-repeat: no-repeat;
      height: 1.8rem;
    }

    .committed-listen-now__links a:hover {
      text-decoration: underline; /* using as alternative to styles lib */
    }

    .committed__serif-large {
      font-size: 27px;
      line-height: 1.25;
    }

    .committed__serif-medium {
      font-size: 21px;
      line-height: 1.25;
    }

    .committed-credits__background {
      background-image: url(<?php echo $base_image_path . 'committed-credits-background__desktop.png'; ?>);
      background-size: 100% 100%;
      background-repeat: no-repeat;
      background-position: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .committed-credits__logo-row {
      justify-content: center;
    }

    .committed-credits__reporters img {
      height: 18vw;
    }

    .committed-credits__info-row {
      justify-content: center;
    }

    @media screen and (max-width: 1336px) {
      .committed__serif-large {
        font-size: 23px;
      }
    }

    @media screen and (max-width: 1104px) {
      .committed-archive__container {
        height: 400px;
        }
      .committed__serif-medium {
        font-size: 19px;
        }
    }

    @media screen and (max-width: 910px) {
      .committed-archive__logo {
        height: 150px;
      }
      .committed__serif-large {
        font-size: 19px;
      }
      .committed__serif-medium {
        font-size: 16px;
      }
      .committed-credits__background {
      background-image: url(<?php echo $base_image_path . 'committed-credits-background-m.png'; ?>);
      }
    }

    @media screen and (max-width: 759px) {
      .committed-archive__logo {
          height: 70px;
      }
      .committed-archive__subtitle {
          height: 50px;
      }
      .committed__serif-large {
          font-size: 18px;
      }
      .committed-credits__background {
          background-image: url(<?php echo $base_image_path . 'committed-credits-background-s.png'; ?>);
      }
      .committed-credits__logo {
        height: 45px;
      }
      .committed-credits__reporters img{
        height: 34vw;
      }
    }
     @media screen and (max-width: 480px) {
        .committed-listen-now-title{
        height: 1.3rem;
      }
     }
  </style>
<div class="committed-archive__container committed__backgrounded">
  <section class="container">
    <div class="flex-grid-row mb-4">
      <div class="flex-grid-item flex-item-xxl-12 flex-item-m-10 mb-4 mt-4">
        <a href="<?php echo home_url( 'category/audio/' ); ?>" class="ui-tag-block ui-tag-block--no-border"><span class="ui-tag">Podcast</span></a>
      </div>
      <div class="flex-grid-item flex-item-xxl-12 text-align-center pt-10 pt-l-7 pt-s-12">
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
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-1 flex-item-l-10 flex-offset-xxl-2 flex-item-xxl-8 font-serif committed__serif-large text-paragraph-breaks">
        <p>In the 1970s, the Provisional IRA was in the early days of its armed campaign to end British rule on the island of Ireland. In the United States, a small group of activists began organising on their behalf. They called themselves the Irish Northern Aid Committee, or Noraid—and they were looking for a fight.</p>
        <p>Hosted by documentary filmmaker Nate Lavey, Foreign Agent is a podcast series about the connection between ordinary Irish Americans and a revolutionary socialist guerrilla group. In six episodes, travelling back and forth across the Atlantic over three decades of conflict, Foreign Agent explores how regular Americans became militant advocates for the cause of Irish freedom.</p>
        <p>It’s a story of guns smuggled in furniture and wild plots to build homemade missiles. But it’s also about the political and material power that the Irish American community wielded, and how the intoxicating spell of nationalism created a movement out of seemingly irreconcilable social and political positions. And at every step of the way, the US government tried to shut them down. This is the story of the Troubles—as seen through American eyes.</p>
      </div>
    </div>
    <div class="flex-grid-row mb-6 mb-s-4">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-12 font-color-white committed-archive-listen__box">
        <div class="committed-archive__box">
          <div class="committed-listen-now-title mb-1"></div>
          <div class="font-weight-bold committed-listen-now__links font-size-13 font-size-s-12">
            <a href="#">Apple Podcasts</a>,
            <a href="#">Spotify</a>,<br/><a href="#">Google Podcasts</a>,
            <a href="#">RSS</a>
          </div>
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
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-2 flex-item-l-8 flex-offset-xl-3 flex-item-xl-7 flex-offset-xxl-3 flex-item-xxl-6 font-serif mb-4 text-paragraph-breaks committed__serif-medium">
          <?php the_content(); ?>
        </div>
      </article>
    <?php
  }
}
?>
    </div>
  </section>
  <div>
    <div class="committed-credits__background pb-6 pt-6 font-color-white">
      <div class="flex-grid-row mb-6 mb-s-5 mt-m-0 mt-4 committed-credits__logo-row">
        <img src="<?php echo $base_image_path . 'committed-logo-white.png'; ?>" alt="committed credits logo" class="committed-credits__logo"/>
      </div>
      <div class="flex-grid-row committed-credits__info-row font-size-10">

        <!-- Reporters -->
        <div class="flex-grid-item flex-item-m-4 flex-item-xxl-4 flex-item-s-8 mb-s-5">
          <div class="flex-grid-row font-weight-bold mb-1">Reporters</div>
          <div class="committed-credits__reporters flex-grid-row">
            <div class="mr-2">
              <a href="<?php echo home_url( 'contributor/rivkah-brown/' ); ?>" class="ui-hover">
              <img src="<?php echo $base_image_path . 'committed-rivkah.jpg'; ?>" alt="Portrait of Rivkah Brown" class="committed-credits__logo"/>
              </a>
              <p>Rivkah Brown</p>
            </div>
            <div class="">
              <a href=<?php echo home_url( 'contributor/clare-hymer/' ); ?> class="ui-hover">
              <img src="<?php echo $base_image_path . 'committed-clare.jpg'; ?>" alt="Portrait of Clare Hymer" class="committed-credits__logo"/>
              </a>
              <p>Clare Hymer</p>
            </div>
          </div>
        </div>

        <!-- Producers -->
        <div class="flex-grid-item flex-item-m-4 flex-item-l-3 flex-item-xxl-3 flex-item-s-8 mb-s-5">
          <div class="font-weight-bold mb-1">Producers</div>
          <div class="mb-2">
            <?php echo wp_kses_post( $credits['producers'] ); ?>
          </div>
          <div class="font-weight-bold mb-1">Music and sound editing</div>
          <div class="mb-2">
            <?php echo wp_kses_post( $credits['music_sound'] ); ?>
          </div>
          <div class="font-weight-bold mb-1">Design and digital</div>
          <div class="mb-2">
            <?php echo wp_kses_post( $credits['design_digital'] ); ?>
          </div>
          <div class="font-weight-bold mb-1">Social media production</div>
          <div>
            <?php echo wp_kses_post( $credits['social_media'] ); ?>
          </div>
        </div>

        <div class="flex-grid-item flex-item-m-8 flex-item-l-4 flex-item-xxl-3 mt-m-3 mt-s-0 flex-item-s-8">
          <div class="font-weight-bold mb-1">Thanks to</div>
          <?php echo apply_filters( 'the_content', $credits['thanks'] ); ?>
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
