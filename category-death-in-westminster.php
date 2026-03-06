<?php
get_header();

$base_image_path = get_stylesheet_directory_uri() . '/dist/img/specials/death-in-westminster/';
?>
<main id="main-content" class="category-archive diw-archive">
  <style type="text/css">
    .diw-archive {
      --diw-red: #FF3817;
      --diw-black-muted: #272727;
      --diw-white-muted: rgb(217, 215, 205);
    }

    .avif .diw-archive__hero {
      background-image: url(<?php echo $base_image_path . 'diw-header-background.avif'; ?>);
    }

    .webp .diw-archive__hero {
      background-image: url(<?php echo $base_image_path . 'diw-header-background.webp'; ?>);
    }

    .fallback .diw-archive__hero {
      background-image: url(<?php echo $base_image_path . 'diw-header-background.jpg'; ?>);
    }

    /* Hero */
    .diw-archive__hero {
      height: 535px;
      position: relative;
      overflow: hidden;
    }

    .diw-archive__artwork {
      position: absolute;
      left: 50%;
      top: 0;
      transform: translateX(-50%);
      height: 70%;
      width: auto;
      z-index: 1;
    }

    .diw-archive__hero-content {
      position: relative;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      align-items: center;
      padding-bottom: 2rem;
    }

    /* Typography */
    .diw-archive__title {
      font-size: 8.5rem;
      letter-spacing: -6%;
      margin-bottom: .75rem;
    }

    .diw-archive__subtitle {
      color: var(--diw-red);
      letter-spacing: -4%;
    }

    .diw__serif-large {
      font-size: 27px;
      line-height: 1.25;
    }

    .diw__serif-medium {
      font-size: 21px;
      line-height: 1.25;
    }

    /* Credits */
    .diw-archive__credits {
      background-color: var(--diw-white-muted);
    }

    .diw-archive__credits-title {
      color: var(--diw-red);
    }

    .diw-archive__credit-label {
      color: var(--diw-red);
    }

    .diw-archive__credit-value {
      color: var(--diw-black-muted);
    }

    .diw-archive__credit-col {
      max-width: 280px;
      margin-left: auto;
      margin-right: auto;
      text-wrap: balance;
    }

    .diw-archive__portrait {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 1px solid var(--diw-white-muted);
      object-fit: cover;
      position: relative;
    }

    .diw-archive__portrait + .diw-archive__portrait {
      margin-left: -30px;
    }

    /* Breakpoints */
    @media screen and (max-width: 1408px) {
      .diw-archive__hero {
        height: 420px;
      }

      .diw-archive__artwork {
        height: 67%;
      }

      .diw-archive__title {
        font-size: 6.5rem;
      }

      .diw__serif-large {
        font-size: 23px;
      }
    }

    @media screen and (max-width: 1104px) {
      .diw-archive__hero {
        height: 365px;
      }

      .diw-archive__title {
        font-size: 6rem;
      }

      .diw__serif-medium {
        font-size: 19px;
      }
    }

    @media screen and (max-width: 910px) {
      .diw-archive__hero {
        height: 330px;
      }

      .diw-archive__title {
        font-size: 5.5rem;
      }

      .diw__serif-large {
        font-size: 19px;
      }

      .diw__serif-medium {
        font-size: 16px;
      }

      .diw-archive__portrait {
        width: 90px;
        height: 90px;
      }
    }

    @media screen and (max-width: 759px) {
      .diw-archive__hero {
        height: 450px;
      }

      .diw-archive__artwork {
        height: 65%;
      }

      .diw-archive__title {
        font-size: 4.3rem;
        margin-bottom: .5rem;
      }

      .diw__serif-large {
        font-size: 18px;
      }

      .diw-archive__portrait {
        width: 80px;
        height: 80px;
      }
    }
  </style>
<div class="container mt-4 mb-5 mb-s-4">
  <div class="grid-row">
    <div class="grid-item is-xxl-24">
      <div class="diw-archive__hero background-cover-image ui-rounded-box">
        <picture>
          <source srcset="<?php echo esc_url( $base_image_path . 'diw-artwork.avif' ); ?>" type="image/avif">
          <source srcset="<?php echo esc_url( $base_image_path . 'diw-artwork.webp' ); ?>" type="image/webp">
          <img
            src="<?php echo esc_url( $base_image_path . 'diw-artwork.png' ); ?>"
            alt="Death in Westminster artwork"
            class="diw-archive__artwork"
          />
        </picture>
        <div class="diw-archive__hero-content text-align-center">
          <h1 class="diw-archive__title font-weight-bold font-size-20">Death in Westminster</h1>
          <h3 class="diw-archive__subtitle font-weight-bold font-size-14 font-size-xl-13 font-size-s-12 text-wrap-balance">How Britain became butler for the world's illicit money</h3>
        </div>
      </div>
    </div>
  </div>
</div>
  <section class="container">
    <?php
    $diw_term = get_queried_object();
    $diw_trailer_url = get_term_meta( $diw_term->term_id, '_nm_soundcloud_trailer', true );
    if ( ! empty( $diw_trailer_url ) ) {
    ?>
    <div class="grid-row mb-4">
      <div class="grid-item offset-s-0 is-s-24 offset-l-2 is-l-20 offset-xxl-4 is-xxl-16">
        <h3 class="mb-4 font-size-12 font-weight-bold">Listen to the trailer:</h3>
        <?php
          render_soundcloud_embed_iframe(
            $diw_trailer_url,
            'full',
            true,
            array(
              'color' => '#FF3817',
            )
          );
          ?>
      </div>
    </div>
    <?php } ?>
    <div class="grid-row mt-4 mb-5">
      <div class="grid-item offset-s-0 is-s-24 offset-l-2 is-l-20 offset-xxl-4 is-xxl-16 font-serif diw__serif-large text-paragraph-breaks">
        <?php
        $diw_description = category_description();
        if ( empty( $diw_description ) ) {
          $diw_description = '<p>Death in Westminster is a new podcast series from Novara Media, hosted by Kojo Koram and Dalia Gebrial, investigating how Britain became butler for the world\'s illicit money.</p>';
        }
        echo $diw_description;
        ?>
      </div>
    </div>
    <div class="grid-row mb-6">
      <div class="grid-item is-s-24 is-xxl-24 text-align-center font-size-13 text-links-underlined">
        Listen now on:<br/>
        <a href="https://podcasts.apple.com/us/channel/novara-media/id6742787656">Apple Podcasts</a>,
        <a href="https://open.spotify.com/show/3KtmyPhvQ2FvhwdRNo5I1N?si=a95692ce1d75432d">Spotify</a>,
        <a href="https://podfollow.com/novaramedia/view">RSS</a>
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
      <article class="diw-archive__episode grid-row pt-6 pt-s-4" id="<?php echo esc_attr( $post->post_name ); ?>">
        <div class="grid-item offset-s-0 is-s-24 offset-xxl-2 is-xxl-8 mobile-mb-4">
          <h4 class="font-size-9 text-uppercase font-weight-bold mb-2 mb-s-0"><?php echo esc_html( $meta['_cmb_standfirst'][0] ); ?></h4>
          <h3 class="font-size-13 font-weight-semibold text-wrap-pretty"><?php the_title(); ?></h3>
        </div>
        <div class="grid-item offset-s-0 is-s-24 is-xxl-12">
          <?php the_post_thumbnail( 'col12-16to9', array( 'class' => 'index-post-thumbnail ui-rounded-box' ) ); ?>
        </div>
        <div class="grid-item offset-s-0 is-s-24 offset-l-4 is-l-16 offset-xl-6 is-xl-14 offset-xxl-6 is-xxl-12 mt-4 mb-4">
          <?php
          if ( isset( $meta['_cmb_sc'][0] ) && ! empty( $meta['_cmb_sc'][0] ) ) {
            render_soundcloud_embed_iframe(
              $meta['_cmb_sc'][0],
              'small',
              true,
              array(
                'color'        => '#FF3817',
                'show_artwork' => 'false',
              )
            );
          }
          ?>
        </div>
        <div class="grid-item offset-s-0 is-s-24 offset-l-4 is-l-16 offset-xl-6 is-xl-14 offset-xxl-6 is-xxl-12 font-serif diw__serif-medium mb-4 text-paragraph-breaks">
          <?php the_content(); ?>
        </div>
      </article>
    <?php
  }
}
?>
    </div>
  </section>
  <div class="container mb-4">
    <div class="grid-row">
      <div class="grid-item is-xxl-24">
        <div class="diw-archive__credits ui-rounded-box ui-backgrounded-box-padding font-size-11 font-size-s-10 text-align-center">
          <div class="grid-row grid-row--nested pt-5 pb-6">
            <div class="grid-item is-xxl-24 mb-5">
              <h3 class="diw-archive__credits-title diw-archive__title font-weight-bold font-size-20 mb-4">Death in Westminster</h3>
            </div>
            <div class="grid-item is-xxl-8 is-m-12 is-s-24">
              <div class="diw-archive__credit-col">
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Produced by</h6>
                <div class="diw-archive__credit-value mb-3">Planet B Productions</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Distributed by</h6>
                <div class="diw-archive__credit-value mb-3">Novara Media</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Hosted by</h6>
                <div class="diw-archive__credit-value mb-3">Kojo Koram and Dalia Gebrial</div>
                <div class="mb-2">
                  <img
                    src="<?php echo esc_url( $base_image_path . 'diw-kojo.png' ); ?>"
                    alt="Portrait of Kojo Koram"
                    class="diw-archive__portrait"
                  />
                  <img
                    src="<?php echo esc_url( $base_image_path . 'diw-dalia.png' ); ?>"
                    alt="Portrait of Dalia Gebrial"
                    class="diw-archive__portrait"
                  />
                </div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Written by</h6>
                <div class="diw-archive__credit-value mb-3">Kojo Koram, Daniel Trilling, Eleanor Penny and Max Packman-Walder</div>
              </div>
            </div>
            <div class="grid-item is-xxl-8 is-m-12 is-s-24">
              <div class="diw-archive__credit-col">
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Producers</h6>
                <div class="diw-archive__credit-value mb-3">Max Packman-Walder, Daniel Norman, Ben Heyderman and Aaron White</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Editor</h6>
                <div class="diw-archive__credit-value mb-3">James Fox</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Executive Producer</h6>
                <div class="diw-archive__credit-value mb-3">Freddie Stuart</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Sound design</h6>
                <div class="diw-archive__credit-value mb-3">Josh Farmer</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Original music</h6>
                <div class="diw-archive__credit-value mb-3">Aron Kyne</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Voice acting</h6>
                <div class="diw-archive__credit-value mb-3">Max Packman-Walder</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Design</h6>
                <div class="diw-archive__credit-value mb-3">Pietro Garrone and Filippo Marra</div>
              </div>
            </div>
            <div class="grid-item is-xxl-8 is-m-12 offset-m-12 is-s-24 offset-s-0">
              <div class="diw-archive__credit-col">
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">Based on</h6>
                <div class="diw-archive__credit-value mb-3">Uncommon Wealth: Britain and the Aftermath of Empire by Kojo Koram</div>
                <h6 class="diw-archive__credit-label font-weight-bold mb-1">With support from</h6>
                <div class="diw-archive__credit-value">The Joffe Trust and Friends Provident Foundation</div>
              </div>
            </div>
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
    'container_classes' => 'mt-4 mb-4',
    'override_text'     => 'With your help, we\'re making our podcasts bigger and better. Support independent journalism and set up a regular donation from just £1 a month.',
  )
);

get_footer();
