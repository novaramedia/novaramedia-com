/**
 * Pure validation rules — no DOM, no wp.* globals, runs under bare node.
 * Markup stripping happens in dom.js; `text` arrives already stripped.
 */

// Split regex matches the historic classic implementation exactly.
export const countWords = ( stringInput ) =>
  ( stringInput.length && stringInput.split( /\s+\b/ ).length ) || 0;

export const isEmptyText = ( text ) => ! text || ! text.trim();

/**
 * Validate one field against its rules.
 *
 * @param {Object}   field                     Shape produced by dom.js readField().
 * @param {string}   field.value               Raw field value (may contain HTML).
 * @param {string}   field.text                Markup-stripped text of the value.
 * @param {Object}   field.rules
 * @param {boolean}  [field.rules.required]             Required regardless of category.
 * @param {string}   [field.rules.requiredCategory]     Required only in this category slug.
 * @param {string}   [field.rules.notRequiredCategory]  Never required in this category slug;
 *                                                      overrides every other required rule.
 * @param {string[]} [field.rules.requiredWith]         Required when any of these sibling
 *                                                      fields (by element id) has a value.
 * @param {number}   [field.rules.wordLength]           Maximum word count.
 * @param {number[]} activeCategoryIds         Term IDs currently on the post.
 * @param {Object}   categoryMap               slug => term IDs (self + descendants).
 * @param {Object}   [siblingTexts]            id => markup-stripped text of the fields a
 *                                             requiredWith rule names (dom.js readSiblingTexts()).
 * @return {{ valid: boolean, failures: string[] }}
 */
export const validateField = ( { value, text, rules }, activeCategoryIds, categoryMap, siblingTexts = {} ) => {
  const failures = [];

  if ( typeof rules.wordLength !== 'undefined' && countWords( value ) > rules.wordLength ) {
    failures.push( `Excess word length. Must be less than ${ rules.wordLength } words.` );
  }

  // Whether the post is in the named category or any of its descendants —
  // the map carries both. A slug missing from the map is never active.
  const inCategory = ( slug ) =>
    ( categoryMap[ slug ] || [] ).some( ( id ) => activeCategoryIds.includes( id ) );

  // Required either unconditionally, or conditionally when the post is in
  // the named category.
  let required = rules.required === true;

  if ( ! required && typeof rules.requiredCategory !== 'undefined' ) {
    required = inCategory( rules.requiredCategory );
  }

  // Required when any named sibling field has a value, e.g. season is
  // required once an episode number or label is filled in.
  if ( ! required && Array.isArray( rules.requiredWith ) ) {
    required = rules.requiredWith.some( ( id ) => ! isEmptyText( siblingTexts[ id ] ) );
  }

  // An exempt category overrides every rule above: a post in that category
  // (or a descendant) never has this field required.
  if ( required && typeof rules.notRequiredCategory !== 'undefined' && inCategory( rules.notRequiredCategory ) ) {
    required = false;
  }

  if ( required && isEmptyText( text ) ) {
    failures.push( 'Meta field required' );
  }

  return { valid: failures.length === 0, failures };
};
