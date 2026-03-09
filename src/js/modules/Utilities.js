/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import { DateTime } from 'luxon';
// possible https://www.npmjs.com/package/fromnow

import Cookies from 'js-cookie';

import selectText from '../functions/selectText.js';

/**
 * Sidewide utilities, inc utility css classes
 */
export class Utilities {
  constructor() {
    this.displayTimeSince();
    this.checkGDPRApproval();
  }

  /**
   * Function to run on jQuery document ready event
   */
  onReady() {
    const _this = this;

    if ($('#single-resources-section').length) {
      _this.bindResourcesToggle();
    }

    $('.js-select').click((event) => {
      selectText($(event.target)[0]);
    });
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
