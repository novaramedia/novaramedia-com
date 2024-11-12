<?php
$override_text = isset($args['override_text']) ? $args['override_text'] : false;
$heading_copy = !empty($args['heading_copy']) ? $args['heading_copy'] : NM_get_option('nm_fundraising_settings_support_section_title', 'nm_fundraising_options');
$support_section_text = NM_get_option('nm_fundraising_settings_support_section_text', 'nm_fundraising_options');
?>

<div id="<?php echo $instance; ?>" class="support-section background-red font-color-white pt-6 pb-6 pt-s-5 pb-s-5">
  <div class="container">


    <div class="grid-row font-size-11 font-size-s-10">
      <div class="grid-item is-m-24 is-l-16 is-xl-12 is-xxl-10">
        <a href="<?php echo home_url('support/'); ?>">
          <h4 class="font-size-12 font-weight-bold mb-3"><?php echo $heading_copy; ?></h4>
        </a>
        <?php
        if ($support_section_text || $override_text) {
        ?>
          <div class="margin-top-micro margin-bottom-small">
            <a href="<?php echo home_url('support/'); ?>" class="js-fix-widows"><?php
                                                                                if ($override_text) {
                                                                                  echo $override_text;
                                                                                } else {
                                                                                  echo $support_section_text;
                                                                                }
                                                                                ?></a>
          </div>
        <?php
        }
        ?>
      </div>

      <!-- Reneders the support form -->
      <?php render_support_form(); ?>
    </div>
  </div>
</div>