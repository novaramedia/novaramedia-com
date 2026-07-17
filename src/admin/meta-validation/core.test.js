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
