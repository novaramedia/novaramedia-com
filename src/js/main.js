/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import '../styl/site.styl'; // import styl for webpack

import $ from 'jquery';
import lazySizes from 'lazysizes';

import { Analytics } from './modules/Analytics.js';
import { Header } from './modules/Header.js';
import { LiveChecker } from './modules/LiveChecker.js';
import { Search } from './modules/Search.js';
import { Support } from './modules/Support.js';
import { Carousels } from './modules/Carousels.js';
import { MailchimpSignup } from './modules/MailchimpSignup.js';
import { Scrollers } from './modules/Scrollers.js';
import { Utilities } from './modules/Utilities.js';
import { Highlighters } from './modules/HighlightScroll.js';

class Site {
  constructor() {
    this.analytics = new Analytics();
    this.header = new Header();
    this.liveChecker = new LiveChecker();
    this.search = new Search();
    this.support = new Support();
    this.carousels = new Carousels();
    this.mailchimpSignup = new MailchimpSignup();
    this.scrollers = new Scrollers();
    this.utilties = new Utilities();
    this.highlighters = new Highlighters();

    $(document).ready(this.onReady.bind(this));
  }

  onReady() {
    lazySizes.init();

    this.header.onReady();
    this.liveChecker.onReady();
    this.search.onReady();
    this.support.onReady();
    this.carousels.onReady();
    this.scrollers.onReady();
    this.utilties.onReady();
    this.highlighters.onReady();
  }
}

new Site();
