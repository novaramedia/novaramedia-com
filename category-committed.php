<?php
get_header();

$credits = array(
    'producers'       => 'Richard Hames and Chal Ravens',
    'music_sound'     => 'Matt Huxley',
    'design_digital'  => 'Pietro Garrone, Filippo Marra and Kimberley Dobney',
    'social_media'    => 'Dunya Kamal and Bronte Dow',
    'thanks'          => 'All of the prisoners, their partners, friends and family members who spoke to us for this podcast. Thanks to their prison buddies: Alex, Jamie, Kate and Pia, and to Bertie Coyle from Just Stop Oil.',
    'image_covers'    => 'Covers photography courtesy of Just Stop Oil',
    'image_ep_three'  => 'Episode 3 photograph by Crispin Hughes',
    'image_reporters' => 'Reporters’ portraits by Magdalena Siwicka',
    'image_misc'      => 'Miscellaneous imagery by Reuters',
);

$base_image_path = get_stylesheet_directory_uri() . '/dist/img/specials/committed/';

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
      background-image: url(<?php echo $base_image_path . 'catagory-committed-background.jpg'; ?>);
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
    .committed-archive-listen__container {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
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
        <?php render_ui_tag( 'Podcast', home_url( 'category/audio/' ), 'no-border' ); ?>
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
        <h3 class="mb-2 font-size-12 font-weight-bold">Listen to the trailer:</h3>
        <?php
          render_soundcloud_embed_iframe(
            'https://api.soundcloud.com/tracks/2081003709',
            'full',
            true,
            array(
              'color' => '#ffab70',
            )
          );
          ?>
    </div>
    <div class="flex-grid-row mt-4 mb-5">
      <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-1 flex-item-l-10 flex-offset-xxl-2 flex-item-xxl-8 font-serif committed__serif-large text-paragraph-breaks">
        <p>Britain is locking up climate activists. You’ve probably heard of some of them – they’re the road-blockers, soup-throwers and spray-painters who made headlines around the world as Just Stop Oil. Some of them are now facing years in prison, caught under the wheels of a crackdown on protest. But what type of person risks their freedom for a cause they believe in?</P>
        <p>Committed is a new podcast series from Novara Media about Britain's political prisoners. Over four episodes, reporters Rivkah Brown and Clare Hymer follow five young people into jail and out again, asking how a concerned citizen could become a convicted criminal. We speak directly to activists in prison to find out how Just Stop Oil persuaded so many of them to take direct action against the climate crisis.</p>
        <p>Now that the orange paint has dried, how do Just Stop Oil prisoners make sense of what they’ve done? What’s it really like in prison, and is life on the inside any different for political prisoners? How does jail-time strain the relationships with the people they love most? And with Just Stop Oil’s actions slowing to a halt, was it all worth it?</p>
      </div>
    </div>
    <div class="flex-grid-row mb-6 mb-s-4 committed-archive-listen__container">
      <div class="flex-grid-item flex-item-s-12 flex-item-xxl-12 font-color-white committed-archive-listen__box">
        <div class="committed-archive__box">
          <div class="committed-listen-now-title mb-1"></div>
          <div class="font-weight-bold committed-listen-now__links font-size-13 font-size-s-12">
            <a href="https://podcasts.apple.com/us/channel/novara-media/id6742787656">Apple Podcasts</a>,<br/>
            <a href="https://open.spotify.com/show/3KtmyPhvQ2FvhwdRNo5I1N?si=a95692ce1d75432d">Spotify</a>,
            <a href="https://feeds.podcastmirror.com/novara-media">RSS</a>
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
          <h3 class="font-size-13 font-weight-semibold mb-2"><?php the_title(); ?></h3>
        </div>
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-item-xxl-6">
          <?php the_post_thumbnail( 'col12-16to9', array( 'class' => 'index-post-thumbnail' ) ); ?>
        </div>
        <div class="flex-grid-item flex-offset-s-0 flex-item-s-12 flex-offset-l-2 flex-item-l-8 flex-offset-xl-3 flex-item-xl-7 flex-offset-xxl-3 flex-item-xxl-6 mt-4 mb-4 mobile-mt-4 mobile-mb-4">
          <?php
          if ( isset( $meta['_cmb_sc'][0] ) && ! empty( $meta['_cmb_sc'][0] ) ) {
            render_soundcloud_embed_iframe(
              esc_url( $meta['_cmb_sc'][0] ),
              'small',
              true,
              array(
                'color'        => '#ffab70',
                'show_artwork' => 'false',
              )
            );
          }
          ?>
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
          <h6 class="font-weight-bold mb-1">Producers</h6>
          <div class="mb-2">
            <?php echo wp_kses_post( $credits['producers'] ); ?>
          </div>
          <h6 class="font-weight-bold mb-1">Music and sound editing</h6>
          <div class="mb-2">
            <?php echo wp_kses_post( $credits['music_sound'] ); ?>
          </div>
          <h6 class="font-weight-bold mb-1">Design and digital</h6>
          <div class="mb-2">
            <?php echo wp_kses_post( $credits['design_digital'] ); ?>
          </div>
          <h6 class="font-weight-bold mb-1">Social media production</h6>
          <div>
            <?php echo wp_kses_post( $credits['social_media'] ); ?>
          </div>
        </div>

        <div class="flex-grid-item flex-item-m-8 flex-item-l-4 flex-item-xxl-3 mt-m-5 mt-s-0 flex-item-s-8">
          <h6 class="font-weight-bold mb-1">Thanks to</h6>
          <?php echo wp_kses_post( $credits['thanks'] ); ?>

          <h6 class="font-weight-bold mt-5 mb-1">Image credits</h6>
          <?php echo wp_kses_post( $credits['image_covers'] ); ?><br/>
          <?php echo wp_kses_post( $credits['image_ep_three'] ); ?><br/>
          <?php echo wp_kses_post( $credits['image_reporters'] ); ?><br/>
          <?php echo wp_kses_post( $credits['image_misc'] ); ?>
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
