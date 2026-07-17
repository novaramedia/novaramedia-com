/**
 * Shared DOM helpers for both editor adapters. Everything that touches the
 * document lives here so core.js stays pure and node-testable.
 */

const FAILURE_COLOR = 'rgb(255, 170, 170)';

// Non-group wysiwyg fields render via wp_editor() which drops CMB2
// 'attributes'; they are marked with editor_class instead (nm- prefixed
// because classes share wp-admin's global namespace with core and other
// plugins). Copy the marker onto the data attributes the scanner reads.
export const bridgeWysiwygMarkers = () => {
  document.querySelectorAll( 'textarea.nm-validation-required' ).forEach( ( el ) => {
    el.setAttribute( 'data-validation', 'true' );
    el.setAttribute( 'data-validation-required', 'true' );
  } );
};

export const scanFields = ( root = document ) => {
  bridgeWysiwygMarkers();

  return Array.from( root.querySelectorAll( '[data-validation]' ) );
};

// Wysiwyg fields edited in Visual mode only sync to their underlying
// textarea on save; force the sync so .value reads current content.
export const syncTinyMce = () => {
  if ( typeof window.tinyMCE !== 'undefined' && typeof window.tinyMCE.triggerSave === 'function' ) {
    window.tinyMCE.triggerSave();
  }
};

// closest() returns the nearest ancestor .cmb-row — in field groups there
// can be several ancestors; nearest matches the old parents().first().
export const getRow = ( el ) => el.closest( '.cmb-row' );

export const getLabel = ( row ) => {
  const label = row && row.querySelector( '.cmb-th label' );

  return label ? label.textContent : '';
};

// Markup-only values (e.g. an empty <p></p> from a wysiwyg) must read as
// empty. DOMParser never executes scripts.
export const toText = ( value ) =>
  new DOMParser().parseFromString( String( value || '' ), 'text/html' ).body.textContent;

/**
 * Read one field element into the shape core.validateField() consumes.
 * File-list rows have no text value — attached items stand in for content.
 */
export const readField = ( el ) => {
  const row = getRow( el );
  const isFileList = row && row.classList.contains( 'cmb-type-file-list' );
  const value = el.value || '';

  let text = toText( value );

  if ( isFileList ) {
    text = row.querySelectorAll( 'ul.cmb-attach-list li' ).length ? 'attached' : '';
  }

  const rules = {};

  if ( el.dataset.validationRequired === 'true' ) {
    rules.required = true;
  }

  // dataset values are always strings, so numeric-looking category slugs
  // (e.g. "2024") survive intact — no jQuery .data() coercion hazard.
  if ( typeof el.dataset.validationRequiredCategory !== 'undefined' ) {
    rules.requiredCategory = el.dataset.validationRequiredCategory;
  }

  if ( typeof el.dataset.validationWordLength !== 'undefined' ) {
    rules.wordLength = parseInt( el.dataset.validationWordLength, 10 );
  }

  return { el, row, value, text, rules };
};

export const addFailureHighlight = ( row ) => {
  if ( row ) {
    row.style.backgroundColor = FAILURE_COLOR;
  }
};

export const removeFailureHighlight = ( row ) => {
  if ( row ) {
    row.style.backgroundColor = '';
  }
};

export const clearAllHighlights = () => {
  scanFields().forEach( ( el ) => removeFailureHighlight( getRow( el ) ) );
};
