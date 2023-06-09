<div id="bottom-bar" class="background-black">
  <?php
    if (!is_page('support')) {
      get_template_part('partials/bottom-bar/support-bar');
    }
    get_template_part('partials/bottom-bar/gdpr');
  ?>
</div>
