<?php
/**
 * Plugin Name: NM Fork: CMB2 js validation for "required" fields
 * Description: Uses js to validate CMB2 fields that have the 'data-validation' attribute set, with rules chosen by data-validation-required / data-validation-word-length
 * Version: 0.4.0
 *
 * Updated to also hook to our secondary options page form (Links Bar)
 * Changed to take variable for validation via data attribute
 * Updated to also validate max words in field
 * Added tinyMCE.triggerSave() so wysiwyg fields validate current Visual-mode content
 * Required check now treats whitespace-only and markup-only (e.g. <p></p>) values as empty
 * Added editor_class bridge so non-group wysiwyg fields can be marked required
 * Validation now skipped on Save Draft and Preview submits; only publish-type submits validate
 * Added data-validation-required-category="<slug>" for fields required only in a category (or its descendants)
 *
 * To enable on a CMB2 meta field set the attributes parameters
 * [note that booleans must be strings]
 *
 * 'attributes' => array(
 *   'data-validation' => 'true',
 *   'data-validation-word-length' => 14,
 *   'data-validation-required' => 'true',
 *   'data-validation-required-category' => 'video',
 * )
 *
 * Non-group wysiwyg fields render via wp_editor(), which does not output the
 * CMB2 'attributes' array — mark those with an editor class instead:
 *
 * 'options' => array(
 *   'editor_class' => 'nm-validation-required'
 * )
 *
 * Reference documentation in the wiki:
 *
 * @link https://github.com/WebDevStudios/CMB2/wiki/Plugin-code-to-add-JS-validation-of-%22required%22-fields
 */
function cmb2_after_form_do_js_validation( $post_id, $cmb ) {
  static $added = false;

  // Only add this to the page once (not for every metabox)
  if ( $added ) {
    return;
  }

  $added = true;

  // Slug-keyed map of category term IDs (self + descendants) so field
  // markup can name categories by slug — term IDs drift across installs.
  $category_map = array();

  foreach ( get_categories( array( 'hide_empty' => false ) ) as $category ) {
    $ids = array_map( 'intval', get_term_children( $category->term_id, 'category' ) );
    array_unshift( $ids, (int) $category->term_id );

    $category_map[ $category->slug ] = $ids;
  }
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

    const categoryMap = <?php echo wp_json_encode( $category_map ); ?>;

    // Non-group wysiwyg fields render via wp_editor() which drops CMB2
    // 'attributes'; they are marked with editor_class instead. Copy the
    // marker onto the data attributes the validator scans for.
    const bridge_wysiwyg_markers = () => {
      $( 'textarea.nm-validation-required' ).attr({
        'data-validation': 'true',
        'data-validation-required': 'true'
      });
    };

    bridge_wysiwyg_markers();

    let $toValidate = $( '[data-validation]' );

    if ( ! $toValidate.length ) {
      return; // Nothing to validate so give up
    }

    const countWords = (stringInput) => {
      return stringInput.length && stringInput.split(/\s+\b/).length || 0;
    };

    // Wysiwyg values arrive as HTML, so whitespace-only ("  ") or markup-only
    // ("<p></p>") input must count as empty. DOMParser never executes scripts.
    const is_empty_value = ( value ) => {
      if ( ! value ) {
        return true;
      }

      const text = new DOMParser().parseFromString( String( value ), 'text/html' ).body.textContent;

      return ! text.trim();
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

      // Drafts and previews save freely; validation only gates
      // Publish / Schedule / Update / Submit for Review.
      const submitter = event.originalEvent && event.originalEvent.submitter;

      if ( submitter && submitter.id === 'save-post' ) {
        return;
      }

      if ( $( '#wp-preview' ).val() === 'dopreview' ) {
        return;
      }

      // Wysiwyg fields edited in Visual mode only sync to their underlying
      // textarea on save; force the sync so val() reads current content
      if ( typeof tinyMCE !== 'undefined' && typeof tinyMCE.triggerSave === 'function' ) {
        tinyMCE.triggerSave();
      }

      bridge_wysiwyg_markers();

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

        // Required either unconditionally, or conditionally when the post is
        // in the named category (or any of its descendants).
        // .attr() not .data(): jQuery data() would coerce numeric-looking
        // slugs (e.g. "2024") to numbers and break the map lookup.
        const requiredCategorySlug = $this.attr( 'data-validation-required-category' );

        let isRequired = $this.data( 'validation-required' ) === true;

        if ( ! isRequired && typeof requiredCategorySlug !== 'undefined' ) {
          const termIds = categoryMap[ requiredCategorySlug ] || [];

          isRequired = termIds.some( function( id ) {
            return $( '#in-category-' + id ).is( ':checked' );
          });
        }

        if ( isRequired ) {
          if ( $row.is( '.cmb-type-file-list' ) ) {

            var has_LIs = $row.find( 'ul.cmb-attach-list li' ).length > 0;

            if ( ! has_LIs ) {
              add_failure( $row, 'Meta field required' );
            } else {
              remove_failure( $row );
            }

          } else {
            if ( is_empty_value( val ) ) {
              add_failure( $row, 'Meta field required' );
            } else {
              remove_failure( $row );
            }
          }
        } else if ( typeof requiredCategorySlug !== 'undefined' ) {
          // Conditionally-required field whose category isn't ticked:
          // clear any stale highlight from a previous failed attempt.
          remove_failure( $row );
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
}

add_action( 'cmb2_after_form', 'cmb2_after_form_do_js_validation', 10, 2 );
