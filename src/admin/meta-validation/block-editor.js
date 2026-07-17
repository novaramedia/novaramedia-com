/**
 * Block editor adapter. Gutenberg saves via REST — there is no cancellable
 * form submit — so gating uses the supported wp.data mechanism: while
 * required metabox fields are invalid AND the post is (or is becoming)
 * published, saving is locked and an error notice lists the failures.
 *
 * Drafts save and preview freely: the lock only engages when the saved or
 * edited status is publish-type or the publish sidebar is open. "Switch to
 * draft" changes the edited status, so it unlocks and proceeds.
 *
 * Known limitation (see spec §5): with the "Enable pre-publish checks"
 * preference turned off, a draft's first publish may race the subscriber.
 */

import { select, dispatch, subscribe } from '@wordpress/data';
// Registers the core/notices store and its wp-notices script dependency;
// dispatch( 'core/notices' ) below only works reliably once this has run.
import '@wordpress/notices';
import { validateField } from './core.js';
import {
  scanFields,
  readField,
  syncTinyMce,
  getLabel,
  addFailureHighlight,
  removeFailureHighlight,
} from './dom.js';

const LOCK_ID = 'nm-meta-validation';
const NOTICE_ID = 'nm-meta-validation';
const PUBLISH_STATUSES = [ 'publish', 'future', 'private' ];

const getCategoryMap = () =>
  ( window.nmMetaValidation && window.nmMetaValidation.categoryMap ) || {};

const debounce = ( fn, ms ) => {
  let timer;

  return () => {
    clearTimeout( timer );
    timer = setTimeout( fn, ms );
  };
};

const gatherEditorState = () => {
  const editor = select( 'core/editor' );

  return {
    savedStatus: editor.getCurrentPostAttribute( 'status' ),
    editedStatus: editor.getEditedPostAttribute( 'status' ),
    sidebarOpen: editor.isPublishSidebarOpened(),
    categories: ( editor.getEditedPostAttribute( 'categories' ) || [] ).map( Number ),
  };
};

const gateActive = ( state ) =>
  PUBLISH_STATUSES.includes( state.savedStatus ) ||
  PUBLISH_STATUSES.includes( state.editedStatus ) ||
  state.sidebarOpen;

const revalidate = () => {
  const state = gatherEditorState();
  const active = gateActive( state );
  const failures = [];

  syncTinyMce();

  scanFields().forEach( ( el ) => {
    const field = readField( el );
    const result = validateField( field, state.categories, getCategoryMap() );

    if ( result.valid || ! active ) {
      removeFailureHighlight( field.row );

      return;
    }

    addFailureHighlight( field.row );

    result.failures.forEach( ( reason ) => {
      failures.push( `${ getLabel( field.row ) }: ${ reason }` );
    } );
  } );

  if ( failures.length && active ) {
    dispatch( 'core/editor' ).lockPostSaving( LOCK_ID );
    dispatch( 'core/notices' ).createErrorNotice(
      `Required post information is missing — ${ failures.join( '; ' ) }`,
      { id: NOTICE_ID, isDismissible: false }
    );
  } else {
    dispatch( 'core/editor' ).unlockPostSaving( LOCK_ID );
    dispatch( 'core/notices' ).removeNotice( NOTICE_ID );
  }
};

const debouncedRevalidate = debounce( revalidate, 200 );

const init = () => {
  // Defence in depth: bail on block-editor screens without the post editor
  // store (the PHP side already restricts enqueueing to post screens).
  if ( ! select( 'core/editor' ) ) {
    return;
  }

  // Store churn is constant; only revalidate when the inputs the gate and
  // rules depend on actually change.
  let lastKey = '';

  subscribe( () => {
    const state = gatherEditorState();
    const key = JSON.stringify( [
      state.savedStatus,
      state.editedStatus,
      state.sidebarOpen,
      state.categories,
    ] );

    if ( key !== lastKey ) {
      lastKey = key;

      // "Switch to draft" calls editPost({status:'draft'}) + savePost()
      // synchronously; if the lock is still held when savePost() runs
      // (debounce hasn't fired yet), that save no-ops. When the gate goes
      // inactive, unlock synchronously so the immediately-following save
      // isn't swallowed. Otherwise keep the debounce to avoid thrashing.
      if ( ! gateActive( state ) ) {
        revalidate();
      } else {
        debouncedRevalidate();
      }
    }
  } );

  // Live re-validation while typing in metabox fields. Capture phase so
  // events inside the metabox area are seen regardless of jQuery handlers.
  document.addEventListener(
    'input',
    ( event ) => {
      if ( event.target.matches && event.target.matches( '[data-validation], textarea.nm-validation-required' ) ) {
        debouncedRevalidate();
      }
    },
    true
  );

  // TinyMCE (Visual mode) edits never fire DOM input events on the
  // textarea; bind editor events as editors register. If tinyMCE isn't
  // present yet, the subscribe path still covers the publish flow.
  if ( window.tinyMCE && typeof window.tinyMCE.on === 'function' ) {
    // Editors initialised before this script ran never fire AddEditor.
    ( window.tinyMCE.editors || [] ).forEach( ( editor ) => {
      editor.on( 'input change', debouncedRevalidate );
    } );

    window.tinyMCE.on( 'AddEditor', ( { editor } ) => {
      editor.on( 'input change', debouncedRevalidate );
    } );
  }

  // subscribe() only fires on future store changes — run one initial pass
  // so an already-published post that loads with an empty required field
  // locks immediately. Gutenberg mounts metaboxes after DOMContentLoaded,
  // so run again on window load once the metabox area is in the DOM.
  debouncedRevalidate();
  window.addEventListener( 'load', debouncedRevalidate );
};

if ( document.readyState === 'loading' ) {
  document.addEventListener( 'DOMContentLoaded', init );
} else {
  init();
}
