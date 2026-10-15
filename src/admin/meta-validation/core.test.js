import test from 'node:test';
import assert from 'node:assert/strict';
import { countWords, isEmptyText, validateField } from './core.js';

test( 'countWords matches historic behaviour', () => {
  assert.equal( countWords( '' ), 0 );
  assert.equal( countWords( 'one two three' ), 3 );
} );

test( 'isEmptyText treats whitespace as empty', () => {
  assert.equal( isEmptyText( '' ), true );
  assert.equal( isEmptyText( '   ' ), true );
  assert.equal( isEmptyText( 'x' ), false );
} );

test( 'required field with empty text fails', () => {
  const result = validateField(
    { value: '<p></p>', text: '', rules: { required: true } }, [], {}
  );
  assert.equal( result.valid, false );
  assert.deepEqual( result.failures, [ 'Meta field required' ] );
} );

test( 'required field with content passes', () => {
  const result = validateField(
    { value: 'hello', text: 'hello', rules: { required: true } }, [], {}
  );
  assert.equal( result.valid, true );
} );

test( 'category-conditional: required when a mapped term id is active', () => {
  const map = { video: [ 12, 34 ] };
  const rules = { requiredCategory: 'video' };

  const inCategory = validateField( { value: '', text: '', rules }, [ 34 ], map );
  assert.equal( inCategory.valid, false );

  const notInCategory = validateField( { value: '', text: '', rules }, [ 99 ], map );
  assert.equal( notInCategory.valid, true );
} );

test( 'category slug missing from map means not required (safe fail)', () => {
  const result = validateField(
    { value: '', text: '', rules: { requiredCategory: 'gone' } }, [ 1 ], {}
  );
  assert.equal( result.valid, true );
} );

test( 'word length over limit fails with the historic message', () => {
  const result = validateField(
    { value: 'a b c d', text: 'a b c d', rules: { wordLength: 3 } }, [], {}
  );
  assert.equal( result.valid, false );
  assert.deepEqual( result.failures, [ 'Excess word length. Must be less than 3 words.' ] );
} );

test( 'word length and required can both fail', () => {
  const result = validateField(
    { value: '<p> </p> a b', text: '', rules: { wordLength: 1, required: true } }, [], {}
  );
  assert.equal( result.failures.length, 2 );
} );

// Rules added on development after this branch was cut (4.8.1 Novara Live
// exemption, 4.10.0 required-with) and ported into the shared core here.

test( 'exempt category: a required field is not required when an exempt term is active', () => {
  const map = { 'novara-live': [ 7, 8 ] };
  const rules = { required: true, notRequiredCategory: 'novara-live' };

  const exempt = validateField( { value: '', text: '', rules }, [ 8 ], map );
  assert.equal( exempt.valid, true );

  const notExempt = validateField( { value: '', text: '', rules }, [ 1 ], map );
  assert.equal( notExempt.valid, false );
} );

test( 'exempt category also overrides a category-conditional requirement', () => {
  const map = { video: [ 12 ], 'novara-live': [ 8 ] };
  const rules = { requiredCategory: 'video', notRequiredCategory: 'novara-live' };

  const result = validateField( { value: '', text: '', rules }, [ 12, 8 ], map );
  assert.equal( result.valid, true );
} );

test( 'required-with: required only when a named sibling field has a value', () => {
  const rules = { requiredWith: [ 'nm_episode_number', 'nm_episode_label' ] };

  const siblingFilled = validateField(
    { value: '', text: '', rules }, [], {}, { nm_episode_number: '3', nm_episode_label: '' }
  );
  assert.equal( siblingFilled.valid, false );
  assert.deepEqual( siblingFilled.failures, [ 'Meta field required' ] );

  const siblingsEmpty = validateField(
    { value: '', text: '', rules }, [], {}, { nm_episode_number: '   ', nm_episode_label: '' }
  );
  assert.equal( siblingsEmpty.valid, true );

  const siblingsUnknown = validateField( { value: '', text: '', rules }, [], {} );
  assert.equal( siblingsUnknown.valid, true );

  const hasOwnValue = validateField(
    { value: 'S2', text: 'S2', rules }, [], {}, { nm_episode_number: '3' }
  );
  assert.equal( hasOwnValue.valid, true );
} );

test( 'exempt category overrides required-with too', () => {
  const map = { 'novara-live': [ 8 ] };
  const rules = { requiredWith: [ 'nm_episode_number' ], notRequiredCategory: 'novara-live' };

  const result = validateField( { value: '', text: '', rules }, [ 8 ], map, { nm_episode_number: '3' } );
  assert.equal( result.valid, true );
} );
