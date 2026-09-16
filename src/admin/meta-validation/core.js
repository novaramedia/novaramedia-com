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
 * @param {boolean}  [field.rules.required]          Required regardless of category.
 * @param {string}   [field.rules.requiredCategory]  Required only in this category slug.
 * @param {number}   [field.rules.wordLength]        Maximum word count.
 * @param {number[]} activeCategoryIds         Term IDs currently on the post.
 * @param {Object}   categoryMap               slug => term IDs (self + descendants).
 * @return {{ valid: boolean, failures: string[] }}
 */
export const validateField = ( { value, text, rules }, activeCategoryIds, categoryMap ) => {
  const failures = [];

  if ( typeof rules.wordLength !== 'undefined' && countWords( value ) > rules.wordLength ) {
    failures.push( `Excess word length. Must be less than ${ rules.wordLength } words.` );
  }

  // Required either unconditionally, or conditionally when the post is in
  // the named category (or any of its descendants — the map carries both).
  let required = rules.required === true;

  if ( ! required && typeof rules.requiredCategory !== 'undefined' ) {
    const termIds = categoryMap[ rules.requiredCategory ] || [];

    required = termIds.some( ( id ) => activeCategoryIds.includes( id ) );
  }

  if ( required && isEmptyText( text ) ) {
    failures.push( 'Meta field required' );
  }

  return { valid: failures.length === 0, failures };
};
