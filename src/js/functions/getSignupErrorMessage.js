/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

const MESSAGE_NO_RESPONSE = "Couldn't send. If you've tried a few times, wait a few minutes and try again";
const MESSAGE_GENERAL = 'General error';

/**
 * Picks the message shown in a failed email signup form, after "Sign up error: ".
 *
 * The signup service rate-limits each IP (5 requests per 3 minutes). Netlify answers over-limit
 * requests with a 429 that carries no CORS header, so the browser hides it and jQuery reports
 * status 0, exactly as it does for a network failure. Status 0 therefore gets a message that fits
 * both. A readable 429 gets the same message.
 *
 * @param {jqXHR} jqXHR The failed request.
 * @return {string} The message to show.
 */
export default function getSignupErrorMessage(jqXHR) {
  if (jqXHR.status === 0 || jqXHR.status === 429) {
    return MESSAGE_NO_RESPONSE;
  }

  try {
    const response = JSON.parse(jqXHR.responseText);

    if (typeof response.message === 'string' && response.message !== '') {
      return response.message;
    }
  } catch (error) {
    console.error('Error parsing response:', error);
  }

  return MESSAGE_GENERAL;
}
