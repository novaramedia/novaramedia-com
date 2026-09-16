/**
 * Classic editor adapter. Binds the native form submit and blocks it with
 * an alert + row highlights when validation fails.
 *
 * Gating: drafts and previews save freely; validation only runs for
 * publish-type submits (Publish / Schedule / Update / Submit for Review).
 * Options page forms (Links Bar, fundraising) have neither gate element and
 * always validate.
 */

import { validateField } from './core.js';
import {
  scanFields,
  readField,
  syncTinyMce,
  getLabel,
  addFailureHighlight,
  removeFailureHighlight,
  clearAllHighlights,
} from './dom.js';

const FORM_IDS = [ 'post', 'nm_secondary_options_page', 'nm_fundraising_options' ];

const getCategoryMap = () =>
  ( window.nmMetaValidation && window.nmMetaValidation.categoryMap ) || {};

const getActiveCategoryIds = () =>
  Array.from( document.querySelectorAll( 'input[name="post_category[]"]:checked' ) )
    .map( ( el ) => parseInt( el.value, 10 ) );

const checkValidation = ( event ) => {
  // SubmitEvent.submitter is the button that triggered this submit; in the
  // classic editor Save Draft is id="save-post" (Publish and Update submit
  // via id="publish", which falls through to validate). Clear highlights
  // left by an earlier failed Publish attempt so the gated save doesn't
  // look like it still has errors.
  const submitter = event.submitter;

  if ( submitter && submitter.id === 'save-post' ) {
    clearAllHighlights();

    return;
  }

  const preview = document.getElementById( 'wp-preview' );

  if ( preview && preview.value === 'dopreview' ) {
    clearAllHighlights();

    return;
  }

  syncTinyMce();

  const fields = scanFields();

  if ( ! fields.length ) {
    return;
  }

  const categoryMap = getCategoryMap();
  const activeCategoryIds = getActiveCategoryIds();
  const messages = [];

  let firstErrorRow = null;

  fields.forEach( ( el ) => {
    const field = readField( el );
    const { valid, failures } = validateField( field, activeCategoryIds, categoryMap );

    if ( valid ) {
      removeFailureHighlight( field.row );

      return;
    }

    addFailureHighlight( field.row );
    firstErrorRow = firstErrorRow || field.row;

    failures.forEach( ( reason ) => {
      messages.push( `\nField "${ getLabel( field.row ) }": ${ reason }` );
    } );
  } );

  if ( firstErrorRow ) {
    event.preventDefault();

    window.alert( `The following validation errors occured: ${ messages.join( '' ) }` );

    window.scrollTo( {
      top: firstErrorRow.getBoundingClientRect().top + window.scrollY - 200,
      behavior: 'smooth',
    } );
  }
};

const init = () => {
  const form = FORM_IDS.map( ( id ) => document.getElementById( id ) ).find( Boolean );

  if ( ! form || ! scanFields().length ) {
    return;
  }

  form.addEventListener( 'submit', checkValidation );
};

if ( document.readyState === 'loading' ) {
  document.addEventListener( 'DOMContentLoaded', init );
} else {
  init();
}
