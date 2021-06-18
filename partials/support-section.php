<?php
  $show_text = isset($args['show_text']) ? $args['show_text'] : true;
  
  $heading_copy = !empty($args['heading_copy']) ? $args['heading_copy'] : 'Support Us';
  
  $support_section_text = IGV_get_option('_igv_support_section_text');
  
  $fundraiser_expiration = IGV_get_option('_igv_fundraiser_end_time');
  $fundraiser_form_text = IGV_get_option('_igv_fundraiser_form_text');

  $default_subscription_value = 8;
?>

<div class="support-section background-lilac padding-top-mid padding-bottom-mid margin-bottom-mid">
  <div class="container">      
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-xxl-12">
        <h4><?php echo $heading_copy; ?></h4>
      </div>
    </div>
    
    <?php
      if ($show_text && $support_section_text) {
    ?>
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-xxl-12">
         <?php echo apply_filters('the_content', $support_section_text); ?>
      </div>
    </div>
    <?php
      }
    ?>
        
    <div class="flex-grid-row margin-bottom-basic">
      <div class="flex-grid-item flex-item-xxl-12">
        <form 
          class="support-form" 
          action="https://payment.novaramedia.com/regular" 
        >
          <input class="support-form__value-input" type="hidden" value="<?php echo $default_subscription_value; ?>" name="amount" />
          
          <div class="margin-bottom-small">
            <button class="support-form__button" data-action="set-type" data-value="https://payment.novaramedia.com/oneoff">One-off</button>
            <button class="support-form__button support-form__button--active" data-action="set-type" data-value="https://payment.novaramedia.com/regular">Monthly</button>
          </div>

          <div class="margin-bottom-small">
            <button class="support-form__button" data-action="set-value" data-value="8">£8</button>
            <button class="support-form__button" data-action="set-value" data-value="20">£20</button>
            <button class="support-form__button" data-action="set-value" data-value="50">£50</button>
            <input class="support-form__custom-input" type="number" placeholder="£ Custom amount" />
          </div>
          
          <input class="support-form__submit" type="submit" value="Go" />
        </form>
      </div>
    </div>
  </div>
</div>