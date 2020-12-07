<?php
get_header();
?>

<!-- main content -->

<main id="main-content">

<?php
if( have_posts() ) {
  while( have_posts() ) {
    the_post();
    $meta = get_post_meta($post->ID);
          
    $team_1 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members1', true);    
    $team_2 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members2', true);
    $team_3 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members3', true);    
    $team_4 = get_post_meta($post->ID, 'about_page_team_group_team-roles-and-members4', true);

    $associates_1 = get_post_meta($post->ID, 'about_page_team_group_associates-roles-and-names1', true);
    $associates_2 = get_post_meta($post->ID, 'about_page_team_group_associates-roles-and-names2', true);

    $contact = get_post_meta($post->ID, 'about_page_contact_group', true);
    $funding = get_post_meta($post->ID, 'about_page_funding_group', true);
?>
  <!-- main posts loop -->
  <article id="page" class="container margin-bottom-large">
    <div class="row margin-bottom-small">
      <div class="col col24">
        <h4><?php the_title(); ?></h4>
      </div>
    </div>
    
    <div class="row margin-bottom-small">
      <div class="col col10 page-copy">
        <?php the_content(); ?>
      </div>
    </div>
    
    <div class="row margin-bottom-small">
      <div class="col col24">
        <h4>Team</h4>
      </div>
    </div>
    <div class="row margin-bottom-small mobile-margin-bottom-none">
      <div class="col col6">
        <?php render_about_group_field($team_1); ?>
      </div>
      <div class="col col6">
        <?php render_about_group_field($team_2); ?>
      </div>
      <div class="col col6">
        <?php render_about_group_field($team_3); ?>
      </div>
      <div class="col col6">
        <?php render_about_group_field($team_4); ?>
      </div>
    </div>

    <div class="row margin-bottom-small">
      <div class="col col24">
        <h4>Associates</h4>
      </div>
    </div>
    <div class="row margin-bottom-small mobile-margin-bottom-none">
      <div class="col col6">
        <?php render_about_group_field($associates_1); ?>
      </div>
      <div class="col col6">
        <?php render_about_group_field($associates_2); ?>
      </div>
    </div>
<?php
    if ($contact) {
?>
    <div class="row margin-bottom-small">
      <div class="col col24">
        <h4>Contact and information</h4>
      </div>
    </div>
    <div class="row margin-bottom-small">
      <div class="col col10">
        <ul>
<?php
        foreach($contact as $contact_entry) {         
         $link = get_permalink($contact_entry['link']);
         
         if (!empty($contact_entry['email'])) {
           $link = 'mailto:' . $contact_entry['email'];
         }         
?>
          <li><a href="<?php echo $link; ?>"><?php echo $contact_entry['title']; ?></a></li>
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
    <div class="row margin-bottom-small">
      <div class="col col24">
        <h4>Funding</h4>
      </div>
    </div>

    <div class="row margin-bottom-small">
      <div class="col col10 page-copy">
        <?php echo apply_filters('the_content', $meta['_cmb_about_funding'][0]); ?>
      </div>
    </div>
<?php
      if ($funding) {
?>
    <div class="row margin-bottom-small">
      <div class="col col10">
<?php
        foreach($funding as $fund) {
?>
        <div class="margin-bottom-tiny">
          <p><?php echo $fund['text']; ?></p>
          <?php echo wp_get_attachment_image($fund['image_id'], 'col4'); ?>
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
    <div class="row margin-bottom-small">
      <div class="col col24">
        <h4>Regulation</h4>
      </div>
    </div>

    <div class="row margin-bottom-small">
      <div class="col col10 page-copy">
        <?php echo apply_filters('the_content', $meta['_cmb_about_regulation'][0]); ?>
      </div>
    </div>
<?php
    }
    
    if (!empty($meta['_cmb_about_legal'])) {
?>
    <div class="row margin-bottom-small">
      <div class="col col24">
        <h4>Legal</h4>
      </div>
    </div>

    <div class="row margin-bottom-small">
      <div class="col col10 page-copy">
        <?php echo apply_filters('the_content', $meta['_cmb_about_legal'][0]); ?>
      </div>
    </div>
<?php
    }
?>
  </article>
<?php
  }
  wp_reset_postdata();
} else {
?>
  <div class="container">
    <div class="row">
      <article class="col col24"><?php _e('Sorry, no posts matched your criteria :{'); ?></article>
    </div>
  </div>
<?php
} ?>

<!-- end main-content -->

</main>

<?php
get_footer();
?>
