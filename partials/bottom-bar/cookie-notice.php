<?php
$copy = IGV_get_option( '_igv_privacy_notice' );

if ( empty( $copy ) ) {
  $copy = 'We are always working to improve this website for our users. To do this we use data provided by the cookies and external scripts.';
}
?>
<div id="obligation-bar">
  <div class="container pt-2 pb-2">
    <div class="grid-row">
      <div class="grid-item is-xxl-18 font-color-white font-size-8 text-links-underlined">
        <p class="text-wrap-balance">
          <?php
          echo wp_kses(
            $copy,
            array(
              'a' => array(
                'href'   => array(),
                'title'  => array(),
                'target' => array(),
                'rel'    => array(),
              ),
              'strong' => array(),
              'em'     => array(),
            )
          );
          ?>
        </p>
      </div>
      <div class="grid-item is-xxl-6 text-align-right">
        <a id="obligation-accept" class="obligation-button ui-button ui-button--white ui-button--small">Accept</a>
      </div>
    </div>
  </div>
</div>
