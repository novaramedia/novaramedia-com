/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

/**
 * LocalStorage utility functions for support banner state management
 */

const STORAGE_KEY = 'support-bar-state';

/**
 * Get the support bar state from localStorage
 * @returns {Object|null} State object with {closed: boolean, expiresAt: timestamp} or null if not found/expired
 */
export function getSupportBarState() {
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (!stored) {
      return null;
    }

    const state = JSON.parse(stored);
    const now = Date.now();

    // Check if the state has expired
    if (state.expiresAt && now > state.expiresAt) {
      // Expired - remove and return null
      localStorage.removeItem(STORAGE_KEY);
      return null;
    }

    return state;
  } catch (error) {
    console.warn('Error reading support bar state from localStorage:', error);
    return null;
  }
}

/**
 * Set the support bar state in localStorage
 * @param {boolean} closed - Whether the bar is closed
 * @param {number} expirationDays - Number of days until the state expires
 */
export function setSupportBarState(closed, expirationDays) {
  try {
    const now = Date.now();
    const expiresAt = now + expirationDays * 24 * 60 * 60 * 1000;

    const state = {
      closed: closed,
      expiresAt: expiresAt,
    };

    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
  } catch (error) {
    console.warn('Error saving support bar state to localStorage:', error);
  }
}

/**
 * Clear the support bar state from localStorage
 */
export function clearSupportBarState() {
  try {
    localStorage.removeItem(STORAGE_KEY);
  } catch (error) {
    console.warn('Error clearing support bar state from localStorage:', error);
  }
}
