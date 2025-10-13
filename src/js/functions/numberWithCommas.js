/**
 * Adds commas as thousands separators to a number
 *
 * @param {number|string} number - The number to format
 * @returns {string} - The number formatted with comma separators
 *
 * @see https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript#2901298
 */
export default function numberWithCommas(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
