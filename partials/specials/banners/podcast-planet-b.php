<?php
  $term = get_term_by('slug', 'planet-b', 'category');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="pt-6 pb-6 font-color-white" style="overflow: hidden; position: relative">
  <div class="planet-b__background-container background-green-neon" style="position: absolute; top: 0; width: 100%; height: 100%; overflow: hidden; z-index: -10; pointer-events: none; ">
    <img class="planet-b__background-image planet-b__background-image-1" alt="" role="presentation" src="<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/planet-b-banner-background.png'; ?>" />
    <img class="planet-b__background-image planet-b__background-image-2" alt="" role="presentation" src="<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/planet-b-banner-background.png'; ?>" />
  </div>
  <style type="text/css">
    .planet-b__background-image {
        width: 380px;
      height: auto;
      position: absolute;
    }
    .planet-b__background-image-1 {
      top: 0;
      left: 0;
    }
    .planet-b__background-image-2 {
      transform: rotate(180deg);
      bottom: 0;
      right: 0;
    }
    .planet-b__banner-logo {
      height: auto;
      max-width: 100%;
      width: 250px
    }
    @media screen and (max-width: 1336px) {
      .planet-b__background-image {
        width: 360px;
      }
    }
    @media screen and (max-width: 1104px) {
      .planet-b__background-image {
        width: 320px;
      }
      .planet-b__background-image-1 {
        left: -20px;
      }
      .planet-b__background-image-2 {
        right: -20px;
      }
    }
    @media screen and (max-width: 910px) {
      .planet-b__background-image {
        width: 280px;
      }
    }
    @media screen and (max-width: 759px) {
      .planet-b__background-image {
        width: 220px;
      }
      .planet-b__banner-logo {
        width: 180px
      }
    }
  </style>
  <div class="container">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <a class="fs-3-sans font-uppercase font-bold font-color-green-racing" href="<?php echo $url; ?>"><h4>Podcast</h4></a>
      </div>
    </div>
    <div class="grid-row">
      <div class="grid-item is-s-24 is-xxl-12 mb-s-3">
        <a href="<?php echo $url; ?>">
          <svg class="planet-b__banner-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 275.9 168.9">
            <path fill="#fcffff" d="M16.7 35.7V13h13c1.9 0 3.8.1 5.5.4 1.8.3 3.3.9 4.7 1.7 1.3.8 2.4 1.9 3.2 3.4.9 1.5 1.3 3.4 1.3 5.8s-.4 4.4-1.3 5.8c-.8 1.5-1.8 2.7-3.2 3.5-1.3.8-2.9 1.3-4.7 1.6-1.8.3-3.6.4-5.5.4h-13zM0 0v75.9h16.7V48.7h17.5c4.7 0 8.8-.7 12.1-2 3.3-1.4 6-3.3 8.1-5.5 2.1-2.3 3.7-4.9 4.6-7.8 1-3 1.5-6 1.5-9 0-3.1-.5-6.1-1.5-9-.9-2.9-2.4-5.5-4.6-7.8-2.1-2.3-4.7-4.1-8.1-5.4C43 .7 39 0 34.2 0H0zm62.3 0v75.9h15.1V0H62.3zm18.9 37.9c.2-3.5 1.1-6.5 2.7-8.8 1.6-2.3 3.5-4.2 6-5.6 2.4-1.4 5.1-2.4 8.1-3 3-.6 6.1-1 9.1-1 2.8 0 5.6.2 8.4.6 2.8.4 5.4 1.1 7.8 2.2 2.3 1.1 4.3 2.7 5.7 4.8 1.5 2 2.2 4.6 2.2 8v28.6c0 2.5.1 4.9.4 7.1.3 2.3.8 4 1.5 5.1h-15.3c-.3-.9-.5-1.7-.7-2.6-.1-.9-.2-1.8-.3-2.8-2.4 2.5-5.2 4.2-8.5 5.2s-6.6 1.5-10 1.5c-2.6 0-5.1-.3-7.3-1-2.3-.6-4.3-1.6-6-3s-3-3-4-5.1c-.9-2.1-1.4-4.5-1.4-7.3 0-3.1.5-5.7 1.6-7.7 1.1-2.1 2.6-3.7 4.3-4.9 1.8-1.2 3.8-2.1 6-2.7 2.3-.6 4.5-1.1 6.8-1.5 2.3-.4 4.5-.6 6.7-.9 2.2-.2 4.1-.5 5.8-1 1.7-.4 3-1 4-1.8 1-.9 1.5-2.1 1.4-3.6 0-1.6-.3-2.9-.8-3.8-.5-1-1.2-1.7-2.1-2.2-.9-.6-1.9-.9-3.1-1.1-1.1-.2-2.4-.3-3.7-.3-3 0-5.3.6-7 1.9-1.7 1.3-2.7 3.4-3 6.4H81.2zM116.1 49c-.6.6-1.5 1-2.4 1.4-.9.3-1.9.5-3.1.7-1.1.2-2.2.4-3.4.5-1.2.1-2.4.3-3.6.5-1.1.2-2.3.5-3.4.9s-2 .9-2.9 1.5c-.8.6-1.4 1.3-1.9 2.2-.5.9-.7 2.1-.7 3.5 0 1.3.2 2.5.7 3.4.5.9 1.2 1.7 2 2.2.9.5 1.8.9 3 1.1 1.1.2 2.3.3 3.5.3 3 0 5.3-.5 6.9-1.5 1.6-1 2.8-2.2 3.6-3.5.8-1.4 1.2-2.8 1.4-4.3.2-1.4.3-2.6.3-3.4V49zm19.4-28.1v55h15.1V47.1c0-5.6.9-9.6 2.8-12 1.8-2.5 4.8-3.7 8.9-3.7 3.6 0 6.1 1.1 7.6 3.4 1.4 2.2 2.1 5.6 2.1 10.1V76h15V42.1c0-3.4-.3-6.5-1-9.3-.6-2.8-1.6-5.2-3.1-7.1-1.5-2-3.5-3.5-6.2-4.6-2.6-1.1-5.8-1.7-9.9-1.7-3.2 0-6.3.7-9.4 2.2-3 1.4-5.5 3.7-7.4 6.9h-.3v-7.7h-14.2zm93.6 21.6h-24.6c.1-1.1.3-2.3.6-3.6.4-1.3 1.1-2.6 2-3.8 1-1.2 2.3-2.2 3.8-3 1.6-.9 3.7-1.3 6.1-1.3 3.7 0 6.4 1 8.2 3 1.9 2 3.2 4.9 3.9 8.7zm-24.5 9.6h39.7c.3-4.3-.1-8.3-1.1-12.2-1-3.9-2.6-7.4-4.9-10.4-2.2-3-5-5.5-8.5-7.2-3.5-1.8-7.6-2.8-12.2-2.8-4.2 0-8 .7-11.5 2.2-3.4 1.5-6.3 3.5-8.8 6.2-2.5 2.6-4.4 5.6-5.7 9.1-1.3 3.5-2 7.4-2 11.5 0 4.3.6 8.2 1.9 11.7 1.3 3.5 3.2 6.6 5.6 9.1 2.4 2.6 5.4 4.5 8.8 6 3.5 1.3 7.4 2 11.7 2 6.2 0 11.6-1.4 15.9-4.3 4.4-2.8 7.7-7.5 9.8-14.1H230c-.5 1.7-1.8 3.3-4 4.9-2.2 1.5-4.8 2.2-7.9 2.2-4.3 0-7.5-1.1-9.8-3.3-2.3-2.2-3.5-5.7-3.7-10.6zm60.3-31.2V4.5h-15.1V21h-9.1v10.1h9.1v32.4c0 2.8.5 5 1.4 6.7.9 1.7 2.2 3 3.7 3.9 1.6.9 3.5 1.5 5.5 1.8 2.1.4 4.4.5 6.7.5 1.5 0 3 0 4.6-.1 1.6-.1 3-.2 4.3-.4V64.2c-.7.1-1.5.2-2.2.3-.8.1-1.6.1-2.4.1-2.6 0-4.3-.4-5.1-1.3-.9-.9-1.3-2.6-1.3-5.1V31.1h11.1V20.9h-11.2zM16.7 118.7V101h15.6c1.5 0 2.9.1 4.3.4 1.4.2 2.7.6 3.7 1.3s1.9 1.5 2.6 2.7c.6 1.1 1 2.6 1 4.4 0 3.2-1 5.5-2.9 6.9-1.9 1.4-4.4 2.1-7.3 2.1h-17zM0 88v75.9h36.8c3.4 0 6.7-.4 9.9-1.3 3.3-.9 6.1-2.2 8.6-3.9 2.6-1.8 4.6-4 6.1-6.8s2.2-6.2 2.2-10c0-4.7-1.2-8.8-3.5-12.1-2.3-3.4-5.7-5.8-10.4-7.1 3.4-1.6 6-3.7 7.7-6.3 1.8-2.6 2.7-5.7 2.7-9.6 0-3.5-.6-6.5-1.8-8.9-1.1-2.4-2.8-4.3-4.9-5.7-2.1-1.5-4.7-2.6-7.7-3.2-3-.6-6.3-1-9.9-1H0zm16.7 63v-20.8h18.2c3.6 0 6.5.9 8.7 2.6 2.2 1.6 3.3 4.4 3.3 8.3 0 2-.4 3.6-1.1 4.9-.6 1.3-1.5 2.3-2.7 3.1-1.1.7-2.4 1.2-3.9 1.6-1.5.3-3 .4-4.7.4H16.7z"/>
            <path fill="#55846b" d="M78 89.5v21.4h16.2V107H82.7v-5.2H93V98H82.7v-4.6H94v-3.9H78zm26 21.3l5.2-15.5H105l-3.3 10.6h-.1l-3.3-10.6H94l5.3 15.5h4.7zm16.2-9.4h-6.9c0-.3.1-.6.2-1s.3-.7.6-1.1c.3-.3.6-.6 1.1-.8.5-.2 1-.4 1.7-.4 1 0 1.8.3 2.3.8.4.6.8 1.4 1 2.5zm-6.9 2.7h11.2c.1-1.2 0-2.3-.3-3.4s-.7-2.1-1.4-2.9c-.6-.9-1.4-1.5-2.4-2s-2.1-.8-3.4-.8c-1.2 0-2.3.2-3.2.6-1 .4-1.8 1-2.5 1.7s-1.2 1.6-1.6 2.6c-.4 1-.6 2.1-.6 3.2 0 1.2.2 2.3.5 3.3.4 1 .9 1.9 1.6 2.6.7.7 1.5 1.3 2.5 1.7 1 .4 2.1.6 3.3.6 1.8 0 3.2-.4 4.5-1.2 1.2-.8 2.2-2.1 2.8-4h-3.7c-.1.5-.5.9-1.1 1.4-.6.4-1.4.6-2.2.6-1.2 0-2.1-.3-2.8-.9-.8-.7-1.2-1.7-1.2-3.1zm12.5-8.8v15.5h4.2v-7c0-.7.1-1.3.2-1.9.1-.6.4-1.1.7-1.6.3-.5.8-.8 1.3-1.1.5-.3 1.2-.4 2-.4.3 0 .5 0 .8.1.3 0 .5 0 .7.1v-3.9c-.3-.1-.6-.1-.9-.1-.5 0-1.1.1-1.6.2-.5.2-1 .4-1.4.7-.4.3-.8.6-1.2 1-.3.4-.6.8-.8 1.3h-.1v-2.9h-3.9zm19.5 17.4l6.5-17.4h-4.4l-3.4 10.6h-.1l-3.5-10.6h-4.5l5.4 14.5c.1.3.2.6.2 1 0 .5-.1.9-.4 1.3-.3.4-.7.6-1.2.7h-1.3c-.4 0-.8-.1-1.2-.1v3.5c.4 0 .9.1 1.3.1s.9.1 1.3.1c1.5 0 2.6-.3 3.4-.8.8-.7 1.4-1.6 1.9-2.9zm13.6-17.4v-4.6h-4.2v4.6H152v2.8h2.6v9.1c0 .8.1 1.4.4 1.9.3.5.6.8 1 1.1.5.3 1 .4 1.6.5.6.1 1.2.1 1.9.1h1.3c.4 0 .8-.1 1.2-.1v-3.3c-.2 0-.4.1-.6.1h-.7c-.7 0-1.2-.1-1.4-.4-.2-.2-.4-.7-.4-1.4V98h3.1v-2.8h-3.1zm3.9-5.8v21.4h4.2v-8.1c0-1.6.3-2.7.8-3.4.5-.7 1.4-1 2.5-1 1 0 1.7.3 2.1 1 .4.6.6 1.6.6 2.8v8.7h4.2v-9.5c0-1-.1-1.8-.3-2.6-.2-.8-.4-1.5-.9-2-.4-.6-1-1-1.7-1.3-.7-.3-1.6-.5-2.8-.5-.8 0-1.6.2-2.5.6-.8.4-1.5 1-2.1 1.9h.1v-8h-4.2zm20.6 3.5v-3.5h-4.2V93h4.2zm-4.3 2.3v15.5h4.2V95.3h-4.2zm6.1 0v15.5h4.2v-8.1c0-1.6.3-2.7.8-3.4.5-.7 1.4-1 2.5-1 1 0 1.7.3 2.1 1 .4.6.6 1.6.6 2.8v8.7h4.2v-9.5c0-1-.1-1.8-.3-2.6-.2-.8-.4-1.5-.9-2-.4-.6-1-1-1.7-1.3-.7-.3-1.6-.5-2.8-.5-.9 0-1.8.2-2.6.6-.9.4-1.6 1-2.1 1.9h-.1v-2.2h-3.9zm23.5 11.9c-.7 0-1.2-.1-1.6-.4-.4-.3-.8-.6-1.1-1.1-.3-.4-.4-.9-.6-1.5-.1-.6-.2-1.1-.2-1.7 0-.6.1-1.1.2-1.6s.3-1 .6-1.4c.3-.4.7-.7 1.1-1 .4-.2 1-.4 1.6-.4.7 0 1.3.1 1.7.4.5.3.8.6 1.1 1 .3.4.5.9.6 1.5.1.6.2 1.2.2 1.8 0 .6-.1 1.1-.2 1.6s-.4.9-.7 1.3c-.3.4-.7.7-1.1 1-.5.3-1 .5-1.6.5zm7.6 2.6V95.3h-4v2.1h-.1c-.5-.9-1.2-1.5-1.9-1.9-.8-.4-1.6-.6-2.7-.6s-2 .2-2.9.6c-.8.4-1.5 1-2.1 1.7-.6.7-1 1.5-1.3 2.5-.3.9-.4 1.9-.4 2.9 0 1.1.1 2.1.4 3 .3.9.7 1.8 1.2 2.5s1.2 1.3 2.1 1.7c.8.4 1.8.6 3 .6.9 0 1.8-.2 2.7-.6.9-.4 1.5-1 2-1.8h.1v2c0 1.1-.2 2-.8 2.7-.5.7-1.4 1.1-2.6 1.1-.8 0-1.4-.2-2-.5-.6-.3-1-.8-1.2-1.6h-4.2c.1.9.3 1.6.7 2.2.4.6 1 1.2 1.6 1.6.7.4 1.4.7 2.2.9.8.2 1.6.3 2.3.3 1.8 0 3.2-.2 4.2-.7 1.1-.5 1.9-1.1 2.4-1.8.6-.7.9-1.4 1.1-2.2.1-.9.2-1.6.2-2.2zM78 115.5v21.4h4.4v-15h.1l5.2 15h3.6l5.2-15.1h.1v15.1h4.4v-21.4h-6.6l-4.7 14.7h-.1l-5-14.7H78zm39.4 21.3v-15.5h-4.2v8.1c0 1.6-.3 2.7-.8 3.4-.5.7-1.4 1-2.5 1-1 0-1.7-.3-2.1-.9-.4-.6-.6-1.6-.6-2.9v-8.7H103v9.5c0 1 .1 1.8.2 2.6.2.8.5 1.4.9 2 .4.5 1 1 1.7 1.3.7.3 1.7.4 2.8.4.9 0 1.8-.2 2.6-.6.9-.4 1.6-1 2.1-1.9h.1v2.2h4zm5.3-5h-4c0 1 .3 1.9.7 2.6.4.7 1 1.2 1.6 1.6.7.4 1.4.7 2.3.9.9.2 1.7.3 2.6.3.9 0 1.7-.1 2.6-.3.9-.2 1.6-.4 2.3-.9.7-.4 1.2-1 1.6-1.6.4-.7.6-1.6.6-2.6 0-.7-.1-1.3-.4-1.8-.3-.5-.6-.9-1.1-1.2-.5-.3-1-.6-1.6-.8-.6-.2-1.2-.4-1.8-.5-.6-.1-1.2-.3-1.8-.4-.6-.1-1.1-.2-1.6-.4-.4-.2-.8-.4-1.1-.6-.3-.2-.4-.5-.4-.9 0-.3.1-.6.2-.7.2-.2.3-.3.6-.4.2-.1.5-.2.8-.2.3 0 .5-.1.8-.1.8 0 1.4.1 2 .4.6.3.9.8.9 1.6h4c-.1-1-.3-1.7-.7-2.4-.4-.6-.9-1.1-1.5-1.5-.6-.4-1.3-.6-2.1-.8-.8-.2-1.6-.2-2.4-.2-.8 0-1.7.1-2.5.2s-1.5.4-2.2.8c-.6.4-1.2.9-1.6 1.5-.4.6-.6 1.5-.6 2.5 0 .7.1 1.3.4 1.7.3.5.6.8 1.1 1.2.5.3 1 .5 1.6.7.6.2 1.2.3 1.8.5 1.5.3 2.7.6 3.5 1 .9.3 1.3.8 1.3 1.4 0 .4-.1.7-.3 1-.2.2-.4.4-.7.6l-.9.3c-.3.1-.6.1-.9.1-.4 0-.8-.1-1.2-.1-.4-.1-.7-.2-1-.4-.3-.2-.5-.5-.7-.8-.1-.5-.2-.9-.2-1.3zm17-10.5v-4.6h-4.2v4.6h-2.6v2.8h2.6v9.1c0 .8.1 1.4.4 1.9.3.5.6.8 1 1.1.5.3 1 .4 1.6.5.6.1 1.2.1 1.9.1h1.3c.4 0 .8-.1 1.2-.1v-3.3c-.2 0-.4.1-.6.1h-.7c-.7 0-1.2-.1-1.4-.4-.2-.2-.4-.7-.4-1.4V124h3.1v-2.8h-3.2zm-47.5 27.3h4.5c-.1-1.2-.5-2.3-1-3.3s-1.2-1.8-2.1-2.4c-.8-.7-1.8-1.2-2.8-1.5-1-.3-2.1-.5-3.3-.5-1.6 0-3.1.3-4.4.9-1.3.6-2.4 1.4-3.3 2.4-.9 1-1.6 2.2-2.1 3.6s-.7 2.8-.7 4.4c0 1.6.2 3 .7 4.4.5 1.3 1.2 2.5 2.1 3.5.9 1 2 1.8 3.3 2.4 1.3.6 2.8.8 4.4.8 1.3 0 2.5-.2 3.6-.6 1.1-.4 2.1-1 2.9-1.7.8-.8 1.5-1.7 2-2.8.5-1.1.8-2.3.9-3.6h-4.5c-.2 1.4-.7 2.6-1.5 3.5-.8.9-1.9 1.3-3.3 1.3-1.1 0-2-.2-2.7-.6-.7-.4-1.3-1-1.8-1.6-.5-.7-.8-1.4-1-2.3-.2-.9-.3-1.7-.3-2.6 0-.9.1-1.8.3-2.7.2-.9.6-1.7 1-2.3.5-.7 1.1-1.2 1.8-1.6.7-.4 1.6-.6 2.7-.6.6 0 1.1.1 1.6.3.5.2 1 .4 1.4.8.4.3.8.7 1 1.2.4.2.6.7.6 1.2zm6.3-7.1v21.4h4.2v-8.1c0-1.6.3-2.7.8-3.4.5-.7 1.4-1 2.5-1 1 0 1.7.3 2.1 1 .4.6.6 1.6.6 2.8v8.7h4.2v-9.5c0-1-.1-1.8-.3-2.6-.2-.8-.4-1.5-.9-2-.4-.6-1-1-1.7-1.3-.7-.3-1.6-.5-2.8-.5-.8 0-1.6.2-2.5.6-.8.4-1.5 1-2.1 1.9h-.1v-8h-4zm16.4 10.6c.1-1 .3-1.8.7-2.5.4-.7 1-1.2 1.7-1.6.7-.4 1.4-.7 2.3-.8.9-.2 1.7-.3 2.6-.3.8 0 1.6.1 2.4.2.8.1 1.5.3 2.2.6.7.3 1.2.8 1.6 1.3.4.6.6 1.3.6 2.2v8c0 .7 0 1.4.1 2 .1.6.2 1.1.4 1.4h-4.3c-.1-.2-.1-.5-.2-.7 0-.3-.1-.5-.1-.8-.7.7-1.5 1.2-2.4 1.5-.9.3-1.9.4-2.8.4-.7 0-1.4-.1-2.1-.3-.6-.2-1.2-.5-1.7-.8-.5-.4-.9-.9-1.1-1.4-.3-.6-.4-1.3-.4-2.1 0-.9.2-1.6.4-2.2.3-.6.7-1 1.2-1.4.5-.3 1.1-.6 1.7-.7.6-.2 1.3-.3 1.9-.4s1.3-.2 1.9-.2c.6-.1 1.2-.1 1.6-.3.5-.1.9-.3 1.1-.5.3-.2.4-.6.4-1 0-.5-.1-.8-.2-1.1-.1-.3-.3-.5-.6-.6-.2-.2-.5-.3-.9-.3-.3-.1-.7-.1-1-.1-.8 0-1.5.2-2 .5-.5.4-.8 1-.8 1.8h-4.2zm9.8 3.1c-.2.2-.4.3-.7.4-.3.1-.5.2-.9.2-.3.1-.6.1-1 .1-.3 0-.7.1-1 .1-.3.1-.6.1-1 .2-.3.1-.6.2-.8.4-.2.2-.4.4-.5.6-.1.3-.2.6-.2 1s.1.7.2 1c.1.3.3.5.6.6.2.1.5.2.8.3s.6.1 1 .1c.8 0 1.5-.1 1.9-.4.5-.3.8-.6 1-1 .2-.4.3-.8.4-1.2.1-.4.1-.7.1-1v-1.4zm6.3-7.9v15.5h4.2v-8.1c0-1.6.3-2.7.8-3.4.5-.7 1.4-1 2.5-1 1 0 1.7.3 2.1 1 .4.6.6 1.6.6 2.8v8.7h4.2v-9.5c0-1-.1-1.8-.3-2.6-.2-.8-.4-1.5-.9-2-.4-.6-1-1-1.7-1.3-.7-.3-1.6-.5-2.8-.5-.9 0-1.8.2-2.6.6-.9.4-1.6 1-2.1 1.9h-.1v-2.2H131zm23.8 11.9c-.7 0-1.2-.1-1.6-.4-.4-.3-.8-.6-1.1-1.1-.3-.4-.4-.9-.6-1.5-.1-.6-.1-1.1-.1-1.7 0-.6.1-1.1.2-1.6s.3-1 .6-1.4c.3-.4.7-.7 1.1-1 .4-.2 1-.4 1.6-.4.7 0 1.3.1 1.7.4.5.3.8.6 1.1 1 .3.4.5.9.6 1.5.1.6.2 1.2.2 1.8 0 .6-.1 1.1-.2 1.6s-.4.9-.7 1.3-.7.7-1.1 1c-.6.3-1.1.5-1.7.5zm7.6 2.6v-14.5h-4v2.1h-.1c-.5-.9-1.2-1.5-1.9-1.9-.8-.4-1.6-.6-2.7-.6s-2 .2-2.9.6c-.8.4-1.5 1-2.1 1.7-.6.7-1 1.5-1.3 2.5-.3.9-.4 1.9-.4 2.9 0 1.1.1 2.1.4 3 .3.9.7 1.8 1.2 2.5s1.2 1.3 2.1 1.7c.8.4 1.8.6 3 .6.9 0 1.8-.2 2.7-.6.9-.4 1.5-1 2-1.8h.1v2c0 1.1-.2 2-.8 2.7-.5.7-1.4 1.1-2.6 1.1-.8 0-1.4-.2-2-.5-.6-.3-1-.8-1.2-1.6h-4.2c.1.9.3 1.6.7 2.2.4.6 1 1.2 1.6 1.6.7.4 1.4.7 2.2.9.8.2 1.6.3 2.3.3 1.8 0 3.2-.2 4.2-.7 1.1-.5 1.9-1.1 2.4-1.8.6-.7.9-1.4 1.1-2.2.1-.9.2-1.6.2-2.2zm12.6-8.4h-6.9c0-.3.1-.6.2-1s.3-.7.6-1.1c.3-.3.6-.6 1.1-.8.5-.2 1-.4 1.7-.4 1 0 1.8.3 2.3.8.5.6.8 1.4 1 2.5zm-6.9 2.7h11.2c.1-1.2 0-2.3-.3-3.4s-.7-2.1-1.4-2.9c-.6-.9-1.4-1.5-2.4-2s-2.1-.8-3.4-.8c-1.2 0-2.3.2-3.2.6-1 .4-1.8 1-2.5 1.7s-1.2 1.6-1.6 2.6c-.4 1-.6 2.1-.6 3.2 0 1.2.2 2.3.5 3.3.4 1 .9 1.9 1.6 2.6.7.7 1.5 1.3 2.5 1.7 1 .4 2.1.6 3.3.6 1.8 0 3.2-.4 4.5-1.2 1.2-.8 2.2-2.1 2.8-4h-3.7c-.1.5-.5.9-1.1 1.4-.6.4-1.4.6-2.2.6-1.2 0-2.1-.3-2.8-.9s-1.1-1.7-1.2-3.1z"/>
          </svg>
        </a>
      </div>
      <div class="grid-item is-s-24 is-xxl-12">
        <a href="<?php echo $url; ?>"><p class="fs-5-sans font-bold font-color-green-racing mb-4">Planet B is a new podcast series from Novara Media which imagines a world that isn’t just saved from climate breakdown, but is renewed and transformed by the fight against it.</p></a>
        <a href="<?php echo $url; ?>" class="nm-button nm-button--white nm-button--inline font-color-black">Listen Now</a>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
