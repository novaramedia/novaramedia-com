/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import '../styl/site.styl'; // import styl for webpack

import $ from 'jquery';
import lazySizes from 'lazysizes';

import { Analytics } from './modules/Analytics.js';
import { Header } from './modules/Header.js';
import { Search } from './modules/Search.js';
import { Support } from './modules/Support.js';
import { Utilities } from './modules/Utilities.js';

class Site {
	constructor() {
		this.analytics = new Analytics();
		this.header = new Header();
		this.search = new Search();
		this.support = new Support();
		this.utilties = new Utilities();

		$(document).ready(this.onReady.bind(this));
	}

	onReady() {
    lazySizes.init();
    
    this.header.onReady();
    this.search.onReady();
    this.support.onReady();
    
		this.utilties.bind();
	}
}

new Site();