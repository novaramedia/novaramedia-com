/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import { debounce } from 'lodash';
import { DateTime } from 'luxon';
// possible https://www.npmjs.com/package/fromnow

import Cookies from 'js-cookie';

import selectText from '../functions/selectText.js';

/**
 * Sidewide utilities, inc utility css classes
 */
export class Utilities {
  constructor() {
    this.fixWidows();
    this.displayTimeSince();
    this.checkGDPRApproval();
  }

  /**
   * Function to run on jQuery document ready event
   */
  onReady() {
    const _this = this;

    this.truncateLines();

    if ($('#single-resources-section').length) {
      _this.bindResourcesToggle();
    }

    $('.js-select').click((event) => {
      selectText($(event.target)[0]);
    });

    $(window).on(
      'resize',
      debounce(() => {
        _this.truncateLines();
      }, 250)
    );
  }

  /**
   * Binds the click toggle for the single post resources section
   */
  bindResourcesToggle() {
    const $resources = $('#single-resources-section');

    $('#js-resources-toggle').click(() => {
      $resources.toggle();
    });
  }

  /**
   * Utility css class mainly for use on headines to avoid widows [single words on a new line]
   * Regex matches the last space character between the last 2 words and replaces the space with non breaking space
   */
  fixWidows() {
    $('.js-fix-widows').each((index, element) => {
      let string = $(element).html();
      string = string.replace(/ ([^ ]*)$/, '&nbsp;$1');
      $(element).html(string);
    });
  }

  /**
   * Utility css class to truncate text to a specified number of lines
   * Target expected to be a block element with data-lines of the number of lines to truncate to
   * Adds a css class of is-truncated if the text is truncated
   * Uses line-height to calculate the height of the element
   * Adds a max-height to the element based on the line-height and data-lines
   * Checks if the height of the element is greater than the max-height and adds the is-truncated class
   */
  truncateLines() {
    $('.js-truncate-lines').each((index, element) => {
      const $element = $(element);
      const lines = $element.data('lines');

      if ($element.data('initiated') !== true) {
        $element.data('original-text', $element.text());
      } else {
        $element.text($element.data('original-text'));
      }

      $element.data('initiated', true);

      $element.css('max-height', 'initial'); // reset max-height
      const styles = window.getComputedStyle($element[0]); // get computed styles
      const lineHeight = styles
        .getPropertyValue('line-height')
        .replace('px', ''); // get line-height in px
      const maxHeight = lineHeight * lines; // calculate max-height in px

      while ($element.height() > maxHeight) {
        // while height is greater than max-height
        $element.text($element.text().slice(0, -1)); // remove last character

        if ($element.height() <= maxHeight) {
          // if height is less than or equal to max-height then this is the last run
          $element.text(
            $element
              .text()
              .replace(/\s+$/, '') // remove trailing whitespace
              .slice(0, -5) + '...' // remove last 5 characters and add ellipsis
          );
        }
      }

      $element.css('max-height', maxHeight); // set max-height
    });
  }

  /**
   * Utility css class to render time since post for posts under 5h old
   * Target expected to be empty element with data-timestamp of a valid timestamp
   */
  displayTimeSince() {
    $('.js-time-since').each((index, element) => {
      const $element = $(element);
      const timestamp = $element.data('timestamp');

      let published = DateTime.fromISO(timestamp);

      $element.text(published.toRelative());
    });
  }

  /**
   * Checks anon cookie for privacy approval and renders approval box if not found
   */
  checkGDPRApproval() {
    const approvalCookie =
      Cookies.get('cookie-approval') === 'true' ? true : false;

    if (!approvalCookie) {
      const $bar = $('#obligation-bar');
      $bar.show();

      $('#obligation-accept').on('click', () => {
        Cookies.set('cookie-approval', 'true', { expires: 365 });
        $bar.hide();
      });
    }
  }
}
