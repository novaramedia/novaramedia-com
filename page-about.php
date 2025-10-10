<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header();
?>
<main id="main-content">
<?php

if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);

    $team_1 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members-1', true);
    $team_2 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members-2', true);
    $team_3 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members-3', true);
    $team_4 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members-4', true);

    $associates_1 = get_post_meta($post->ID, 'about_page_team_group_associates-roles-and-names-1', true);
    $associates_2 = get_post_meta($post->ID, 'about_page_team_group_associates-roles-and-names-2', true);

    $contact = get_post_meta($post->ID, 'about_page_contact_group', true);
    $funding = get_post_meta($post->ID, 'about_page_funding_group', true);
?>
  <article id="page" class="container margin-top-small margin-bottom-large">
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold"><?php
 the_title(); ?></h4>
      </div>
    </div>

    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-s-24 is-m-18 is-l-16 is-xxl-10 page-copy">
        <?php
 the_content(); ?>
      </div>
    </div>

    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold">Team</h4>
      </div>
    </div>
    <div class="grid-row margin-bottom-small mobile-margin-bottom-none">
      <div class="grid-item is-m-12 is-xxl-6">
        <?php
 render_about_group_field($team_1); ?>
      </div>
      <div class="grid-item is-m-12 is-xxl-6">
        <?php
 render_about_group_field($team_2); ?>
      </div>
      <div class="grid-item is-m-12 is-xxl-6">
        <?php
 render_about_group_field($team_3); ?>
      </div>
      <div class="grid-item is-m-12 is-xxl-6">
        <?php
 render_about_group_field($team_4); ?>
      </div>
    </div>

    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold">Associates</h4>
      </div>
    </div>
    <div class="grid-row margin-bottom-small mobile-margin-bottom-none">
      <div class="grid-item is-m-12 is-xxl-6">
        <?php
 render_about_group_field($associates_1); ?>
      </div>
      <div class="grid-item is-m-12 is-xxl-6">
        <?php
 render_about_group_field($associates_2); ?>
      </div>
    </div>
<?php

    if ($contact) {
?>
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold">Contact and information</h4>
      </div>
    </div>
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-s-24 is-m-18 is-l-16 is-xxl-10">
        <ul>
<?php

        foreach($contact as $contact_entry) {
         $link = get_permalink($contact_entry['link']);

         if (!empty($contact_entry['email'])) {
           $link = 'mailto:' . $contact_entry['email'];
         }
?>
          <li><a href="<?php
 echo $link; ?>"><?php
 echo $contact_entry['title']; ?></a></li>
<?php

        }
?>
        </ul>
      </div>
    </div>
<?php

    }
?>
<?php

    if (!empty($meta['_cmb_about_funding'])) {
?>
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold">Funding</h4>
      </div>
    </div>

    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-s-24 is-m-18 is-l-16 is-xxl-10 page-copy">
        <?php
 echo apply_filters('the_content', $meta['_cmb_about_funding'][0]); ?>
      </div>
    </div>
<?php

      if ($funding) {
?>
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-s-24 is-m-18 is-l-16 is-xxl-10">
<?php

        foreach($funding as $fund) {
?>
        <div class="margin-bottom-tiny">
          <p><?php
 echo $fund['text']; ?></p>
          <?php
 echo wp_get_attachment_image($fund['image_id'], 'col4'); ?>
        </div>
<?php

        }
?>
      </div>
    </div>
<?php

      }
    }

    if (!empty($meta['_cmb_about_regulation'])) {
?>
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold">Regulation</h4>
      </div>
    </div>

    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-s-24 is-m-18 is-l-16 is-xxl-10 page-copy">
        <?php
 echo apply_filters('the_content', $meta['_cmb_about_regulation'][0]); ?>
      </div>
    </div>
<?php

    }

    if (!empty($meta['_cmb_about_legal'])) {
?>
    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-xxl-24">
        <h4 class="font-size-9 text-uppercase font-weight-bold">Legal</h4>
      </div>
    </div>

    <div class="grid-row margin-bottom-small">
      <div class="grid-item is-s-24 is-m-18 is-l-16 is-xxl-10 page-copy">
        <?php
 echo apply_filters('the_content', $meta['_cmb_about_legal'][0]); ?>
      </div>
    </div>
<?php

    }
?>
  </article>
<?php

  }
  wp_reset_postdata();
} ?>
</main>
<?php

get_footer();
?>
