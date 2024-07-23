<?php
  $term = get_term_by('slug', 'disability-its-political', 'focus');

  if ($term) {
    $url = get_term_link($term);
    if ($url) {
?>
<div class="disability-its-political__container pt-6 pb-6">
  <style type="text/css">
    .disability-its-political__container {
      overflow: hidden;
      position: relative;
      background-color: #B1B1FF;
      background-image: url(<?php echo get_bloginfo('stylesheet_directory') . '/dist/img/specials/focus-disability-its-political-banner-background.svg'; ?>);
      background-size: cover;
      background-position: bottom;
      outline: 4px black solid;
    }
    .disability-its-political__text-background {
      position: absolute;
      left: -2rem;
      top: -50vh;
      height: 100vh;
      width: calc(100% + 4rem);
      background-color: #B1B1FF;
      border: solid black 4px;
    }
    .disability-its-political__text-copy {
      position: relative;
      z-index: 10;
    }
    @media screen and (max-width: 759px) {
      .disability-its-political__container {
        background-image: none;
        outline: 2px black solid;
      }
      .disability-its-political__text-background {
        display: none;
      }
    }
  }
  </style>
  <div class="container">
    <div class="grid-row mb-4">
      <div class="grid-item is-xxl-24">
        <div class="disability-its-political__text-copy">
          <a href="<?php echo $url; ?>"><h4 class="font-size-9 text-uppercase font-weight-bold">Focus</h4></a>
        </div>
      </div>
    </div>
    <div class="grid-row">
      <div class="grid-item is-s-24 is-l-8 is-xl-10 is-xxl-8 mb-s-3">
        <div class="disability-its-political__text-background"></div>
        <div class="disability-its-political__text-copy">
          <a href="<?php echo $url; ?>"><h3 class="fs-8 mb-s-3" style="margin-left: -.05em;">Disability:<br/>It’s Political</h3></a>
        </div>
      </div>
      <div class="grid-item only-desktop offset-l-4 offset-xl-2 offset-xxl-4 is-xxl-12">
        <div class="disability-its-political__text-background"></div>
        <div class="disability-its-political__text-copy">
          <a href="<?php echo $url; ?>"><p class="fs-5-sans font-weight-bold mb-4">Disability isn’t a personal tragedy, it’s a political issue. From autism to assisted dying, sex work to social care, this focus explores how capitalism marginalises those whose minds and bodies don’t "work".</p></a>
          <a href="<?php echo $url; ?>" class="ui-button ui-button--white">Explore the Focus</a>
        </div>
      </div>
    </div>
  </div>
  <div class="only-mobile">
    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 460 76" fill="none" style="width: 100%; height: auto;">
      <g clip-path="url(#a)">
        <path fill="#fff" d="M0 0h460v72H0z"/>
        <mask id="b" width="464" height="76" x="-2" y="-2" maskUnits="userSpaceOnUse" style="mask-type:alpha">
          <path fill="#B1B1FF" stroke="#000" stroke-width="3" d="M460 0H0v72h460z"/>
        </mask>
        <g mask="url(#b)">
          <path fill="#B1B1FF" stroke="#000" stroke-width="3" d="M460 0H0v72h460z"/>
          <path fill="#B1B1FF" stroke="#0E0E0E" stroke-width="3.64383" d="M-5-96h106V84H-5z"/>
          <path fill="#B1B1FF" stroke="#0E0E0E" stroke-width="3.64383" d="M100.819-95.6875h73.698v124.014h-73.698zM287.004-208.082h193.942V28.32H287.004z"/>
          <rect width="108.608" height="108.512" x="117.305" y="-87.9375" fill="#0E0E0E" rx="54.2561"/>
          <path fill="#9A8800" stroke="#0E0E0E" stroke-width="3.64383" d="M174.518-95.6875h112.486v124.014H174.518z"/>
          <path fill="#E63D00" stroke="#0E0E0E" stroke-width="3.64383" d="M100.819 28.3242h380.127v55.5479H100.819z"/>
          <path fill="#0E0E0E" fill-rule="evenodd" d="M465.431 46.8934c0-1.695-1.374-3.0692-3.069-3.0692h-81.136c-1.695 0-3.069 1.3742-3.069 3.0692v3.8727c0 1.695-1.374 3.0692-3.069 3.0692h-80.812c-1.695 0-3.069 1.3741-3.069 3.0692v3.2274c0 1.695-1.374 3.0692-3.069 3.0692h-81.135c-1.695 0-3.069 1.3741-3.069 3.0692v4.1995c0 1.6951-1.374 3.0693-3.069 3.0693h-80.46c-1.695 0-3.069 1.3741-3.069 3.0692v3.8731c0 1.6951 1.374 3.0692 3.069 3.0692h84.526c.001 0 .002-.0008.002-.0017 0-.001.001-.0018.002-.0018h86.273v-.0001h86.949v.0004h84.205c1.695 0 3.069-1.3741 3.069-3.0692V46.8934Z" clip-rule="evenodd"/>
          <path fill="#0E0E0E" d="M84.0507 72.9599c.0043 1.0343-.8336 1.875-1.8688 1.875h-9.9671c-1.0291 0-1.8645-.8312-1.8688-1.8594l-.0722-17.2771c-.0038-.892-.4813-1.7149-1.254-2.1606L15.6692 22.7629c-.3725-.2149-.6594-.5516-.8123-.9532l-2.805-7.3675c-.4209-1.1056.28-2.3186 1.4488-2.5072l7.7885-1.2567c.4246-.0685.8599.0115 1.2323.2263L68.313 37.3191c.8375.4831 1.8834-.1239 1.8794-1.0907l-.4588-109.7265c-.0018-.4324.1467-.852.42-1.1872l4.9808-6.1074c.7478-.917 2.1498-.917 2.8976 0l4.9863 6.1142c.27.331.4183.7446.42 1.1716l.6124 146.4668Z"/>
        </g>
        <path stroke="#000" stroke-width="3" d="M472.5 1.5h-492v69h492z"/>
      </g>
      <defs>
        <clipPath id="a">
          <path fill="#fff" d="M0 0h460v72H0z"/>
        </clipPath>
      </defs>
    </svg>

    <div class="container">
      <div class="grid-row">
        <div class="grid-item is-xxl-24 mt-4">
          <a href="<?php echo $url; ?>"><p class="fs-5-sans font-weight-bold mb-4">Disability isn’t a personal tragedy, it’s a political issue. From autism to assisted dying, sex work to social care, this focus explores how capitalism marginalises those whose minds and bodies don’t "work".</p></a>
          <a href="<?php echo $url; ?>" class="ui-button ui-button--white ui-button--inline font-color-black">Explore the Focus</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
    }
  }
?>
