/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import { DateTime } from 'luxon';

export class LiveChecker {
  constructor() {
    this.isLive = false;
    this.$messageContainer = $('.submenu__message');
    this.$liveMessage = $('.submenu__message__live');
    this.$offlineMessage = $('.submenu__message__offline');
    this.basicSchedule = {
      days: [1, 2, 3, 4, 5],
      startHour: 18,
      endHour: 19
    }
    this.offlineMessages = [
      {
        quote: 'Utopia Now',
        link: 'https://shop.novaramedia.com/products/no-future-utopia-now-scarf',
      },
      {
        quote: 'calling Radio Alice',
        link: 'https://en.wikipedia.org/wiki/Radio_Alice',
      },
      {
        quote: 'va in paradiso',
        link: 'https://www.imdb.com/title/tt0066919/',
      },
    ];
  }

  onReady() {
    const _this = this;

    if (_this.$messageContainer.length === 0) {
      return;
    }

    _this.randomOfflineMessage = _this.offlineMessages[Math.floor(Math.random() * _this.offlineMessages.length)];
    _this.$offlineMessage.text(`"${_this.randomOfflineMessage.quote}"`).attr('href', _this.randomOfflineMessage.link);

    _this.checkIfLive();
    _this.updateMessage();

    this.liveCheckerInterval = setInterval(() => {
      _this.checkIfLive();
    }, 1000 * 60);
  }

  checkIfLive() {
    const _this = this;

    let currentTime = DateTime.now().setZone('Europe/London');
    let currentDay = currentTime.weekday;
    let currentHour = currentTime.hour;

    if (_this.basicSchedule.days.includes(currentDay)) {
      if (currentHour >= _this.basicSchedule.startHour && currentHour < _this.basicSchedule.endHour) {
        _this.setLiveStatus(true);
        return;
      }
    }

    _this.setLiveStatus(false);
  }

  setLiveStatus(newLiveStatus) {
    if (this.isLive !== newLiveStatus) {
      this.isLive = newLiveStatus;
      this.updateMessage();
    }
  }

  updateMessage() {
    const _this = this;

    if (_this.isLive) {
      _this.$liveMessage.show();
      _this.$offlineMessage.hide();
    } else {
      _this.$liveMessage.hide();
      _this.$offlineMessage.show();
    }
  }
}
