<?php
  $show_text = isset($args['show_text']) ? $args['show_text'] : true;
  
  $heading_copy = !empty($args['heading_copy']) ? $args['heading_copy'] : 'Support Us';
  
  $support_section_text = IGV_get_option('_igv_support_section_text');
  
  $fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
  $fundraiser_form_text = IGV_get_option('_igv_fundraiser_form_text');

  $default_subscription_value = 8;
?>

<div class="support-section background-red font-color-white padding-top-mid padding-bottom-mid">
  <div class="container">      
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-xxl-12">
        <h4><?php echo $heading_copy; ?></h4>
      </div>
    </div>
    
    <?php
      if ($show_text && $support_section_text) {
    ?>
    <div class="flex-grid-row margin-bottom-small">
      <div class="flex-grid-item flex-item-m-12 flex-item-xxl-6">
         <?php echo apply_filters('the_content', $support_section_text); ?>
      </div>
    </div>
    <?php
      }
    ?>
        
    <form 
      class="support-form" 
      action="https://payment.novaramedia.com/regular" 
    >
      <input class="support-form__value-input" type="hidden" value="<?php echo $default_subscription_value; ?>" name="amount" />

      <div class="flex-grid-row margin-bottom-basic">
        <div class="flex-grid-item flex-item-xxl-2 flex-offset-xxl-2">          
          <button class="support-form__button support-form__value-option" data-action="set-value" data-value="8">£8</button>
          <button class="support-form__button support-form__value-option" data-action="set-value" data-value="20">£20</button>
          <button class="support-form__button support-form__value-option" data-action="set-value" data-value="50">£50</button>
        </div>

        <div class="flex-grid-item flex-item-xxl-2">
          <input class="support-form__custom-input" type="number" placeholder="£ Custom amount" />
        </div>
        
        <div class="flex-grid-item flex-item-xxl-2">
          <button class="support-form__button support-form__schedule-option" data-action="set-type" data-value="https://payment.novaramedia.com/oneoff">One-off</button>
            <button class="support-form__button support-form__button--active support-form__schedule-option" data-action="set-type" data-value="https://payment.novaramedia.com/regular">Monthly</button>
        </div>

        <div class="flex-grid-item flex-item-xxl-2">
          <input class="support-form__submit" type="submit" value="Go" />
        </div>
      </div>          
    </form>      
  
  </div>
</div>