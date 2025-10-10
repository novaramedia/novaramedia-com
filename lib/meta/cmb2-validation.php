<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/*
 * Plugin Name: NM Fork: CMB2 js validation for "required" fields
 * Description: Uses js to validate CMB2 fields that have the 'data-validation' attribute set to 'required'
 * Version: 0.2.0
 * 
 * Updated to also hook to our secondary options page form (Links Bar) 
 * Changed to take variable for validation via data attribute
 * Updated to also validate max words in field
 * 
 * To enable on a CMB2 meta field set the attributes parameters
 * [note that booleans must be strings]
 * 
 * 'attributes' => array(
 *   'data-validation' => 'true',
 *   'data-validation-word-length' => 14
 *   'data-validation-required' => 'true'
 * )
 */

/**
 * Reference documentation in the wiki:
 * @link https://github.com/WebDevStudios/CMB2/wiki/Plugin-code-to-add-JS-validation-of-%22required%22-fields
 */

function cmb2_after_form_do_js_validation( $post_id, $cmb ) {
  static $added = false;

  // Only add this to the page once (not for every metabox)
  if ( $added ) {
    return;
  }

  $added = true;
?>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    let $form = false;

    if (document.getElementById('post')) {
      $form = $( document.getElementById( 'post' ) );
    }

    if ($form === false && document.getElementById('nm_secondary_options_page')) {
      $form = $(document.getElementById('nm_secondary_options_page'));
    }

    if ($form === false && document.getElementById('nm_fundraising_options')) {
      $form = $(document.getElementById('nm_fundraising_options'));
    }

    if ($form === false) {
      return; // No form to hook to give up here
    }

    const $htmlbody = $( 'html, body' );
    let $toValidate = $( '[data-validation]' );

    if ( ! $toValidate.length ) {
      return; // Nothing to validate so give up
    }

    const countWords = (stringInput) => {
      return stringInput.length && stringInput.split(/\s+\b/).length || 0;
    };

    const remove_failure = ( $row ) => {
      $row.css({ background: '' });
    }

    const generate_error_messages = (labels) => {
      let returnString = '';

      labels.forEach((item) => {
        returnString += `\nField "${item.label}": ${item.message}`
      });

      return returnString;
    }

    function checkValidation( event ) {
      var labels = [];
      var $first_error_row = null;
      var $row = null;

      $toValidate = $( '[data-validation]' );

      if ( ! $toValidate.length ) {
        return; // Nothing to validate so give up
      }

      const add_failure = ( $row, reason ) => {        
        $row.css({ 'background-color': 'rgb(255, 170, 170)' });
        
        $first_error_row = $first_error_row ? $first_error_row : $row;

        labels.push({
          label: $row.find( '.cmb-th label' ).text(),
          message: reason
        });
      }

      $toValidate.each( function() {
        var $this = $(this);
        var val = $this.val();

        $row = $this.parents( '.cmb-row' );

        if ($row.length > 1) { // In field groups there can be more than one parent .cmb-row. We want the first one.
          $row = $row.first();
        }

        if (typeof $this.data('validation-word-length') !== 'undefined') { // Validate word length if variable set
          const wordCount = countWords(val);

          if (wordCount > $this.data('validation-word-length')) {
            add_failure( $row, `Excess word length. Must be less than ${$this.data('validation-word-length')} words.` );
          } else {
            remove_failure( $row );
          }
        }

        if ( $this.data( 'validation-required' ) === true ) { // Validate required if variable set
          if ( $row.is( '.cmb-type-file-list' ) ) {

            var has_LIs = $row.find( 'ul.cmb-attach-list li' ).length > 0;

            if ( ! has_LIs ) {
              add_failure( $row, 'Meta field required' );
            } else {
              remove_failure( $row );
            }

          } else {
            if ( ! val ) {
              add_failure( $row, 'Meta field required' );
            } else {
              remove_failure( $row );
            }
          }
        }

      });

      if ( $first_error_row ) {
        event.preventDefault();

        const errorMessages = generate_error_messages(labels);

        alert( `The following validation errors occured: ${errorMessages}` );
        $htmlbody.animate({
          scrollTop: ( $first_error_row.offset().top - 200 )
        }, 600);
      }
    }

    $form.on( 'submit', checkValidation );
  });
  </script>
  <?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
}

add_action( 'cmb2_after_form', 'cmb2_after_form_do_js_validation', 10, 2 );