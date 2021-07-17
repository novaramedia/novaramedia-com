<?php
  $show_text = isset($args['show_text']) ? $args['show_text'] : true;
  $override_text = isset($args['override_text']) ? $args['override_text'] : false;
    
  $heading_copy = !empty($args['heading_copy']) ? $args['heading_copy'] : 'Support Us';
  
  $support_section_text = IGV_get_option('_igv_support_section_text');
  
  $fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
  $fundraiser_form_text = IGV_get_option('_igv_fundraiser_form_text');

  $default_subscription_value = 8;
?>

<div class="support-section background-red font-color-white padding-top-large padding-bottom-large">
  <div class="container">    
    
    <form 
      class="support-form" 
      action="https://payment.novaramedia.com/regular" 
    >
      <input class="support-form__value-input" type="hidden" value="<?php echo $default_subscription_value; ?>" name="amount" />
            
      <div class="flex-grid-row margin-bottom-small">
        <div class="flex-grid-item flex-item-m-12">
          <a href="<?php echo home_url('support/'); ?>">
            <h4><?php echo $heading_copy; ?></h4>
          </a>
        </div>
      </div>

      <div class="flex-grid-row font-size-3">
        <div class="flex-grid-item flex-item-m-12 flex-item-l-6 flex-item-xxl-5">      
          <?php
            if ($support_section_text || $override_text) {
            ?>
          <div class="margin-top-micro font-bold">
            <?php 
              if ($override_text) {
                echo apply_filters('the_content', $override_text);
              } else {
                echo apply_filters('the_content', $support_section_text);
              } 
            ?>
          </div>
            <?php
              }
            ?>
        </div>

        <div class="flex-grid-item flex-item-s-6 flex-offset-s-0 flex-item-m-4 flex-offset-m-2 flex-item-xxl-3">   
          <div class="margin-bottom-small">       
            <button class="support-form__button support-form__value-option" data-action="set-value" data-value="8">£8</button>
            <button class="support-form__button support-form__value-option" data-action="set-value" data-value="20">£20</button>
            <button class="support-form__button support-form__value-option" data-action="set-value" data-value="50">£50</button>
          </div>
          
          <button class="support-form__button support-form__schedule-option" data-action="set-type" data-value="https://payment.novaramedia.com/oneoff">One-off</button>
          <button class="support-form__button support-form__button--active support-form__schedule-option" data-action="set-type" data-value="https://payment.novaramedia.com/regular">Monthly</button>
        </div>

        <div class="flex-grid-item flex-item-s-6 flex-item-m-4 flex-item-xxl-3">
          <div class="margin-bottom-small">       
            <input class="support-form__custom-input" type="number" placeholder="£ Custom amount" />
          </div>
          
          <input class="support-form__submit nm-button--red-dark" type="submit" value="Go" />
        </div>

      </div>
    </form>      
  </div>
</div>