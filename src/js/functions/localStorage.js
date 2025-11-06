/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

/**
 * General-purpose localStorage utility functions with expiration support
 */

const MILLISECONDS_PER_DAY = 24 * 60 * 60 * 1000;

/**
 * Get an item from localStorage with automatic expiration handling
 * @param {string} key - The localStorage key
 * @returns {*} The stored value or null if not found/expired
 */
export function getItem(key) {
  try {
    const stored = localStorage.getItem(key);
    if (!stored) {
      return null;
    }

    const data = JSON.parse(stored);

    // If data has an expiresAt property, check if it's expired
    if (data.expiresAt !== undefined) {
      const now = Date.now();
      if (now > data.expiresAt) {
        // Expired - remove and return null
        localStorage.removeItem(key);
        return null;
      }
      // Return the value without the expiration metadata
      return data.value;
    }

    // No expiration, return as-is
    return data;
  } catch (error) {
    console.warn(`Error reading from localStorage (key: ${key}):`, error);
    return null;
  }
}

/**
 * Set an item in localStorage with optional expiration
 * @param {string} key - The localStorage key
 * @param {*} value - The value to store
 * @param {number} [expirationDays] - Optional number of days until expiration
 */
export function setItem(key, value, expirationDays) {
  try {
    let dataToStore;

    if (expirationDays !== undefined) {
      const now = Date.now();
      const expiresAt = now + expirationDays * MILLISECONDS_PER_DAY;
      dataToStore = { value, expiresAt };
    } else {
      dataToStore = value;
    }

    localStorage.setItem(key, JSON.stringify(dataToStore));
  } catch (error) {
    console.warn(`Error saving to localStorage (key: ${key}):`, error);
  }
}

/**
 * Remove an item from localStorage
 * @param {string} key - The localStorage key
 */
export function removeItem(key) {
  try {
    localStorage.removeItem(key);
  } catch (error) {
    console.warn(`Error removing from localStorage (key: ${key}):`, error);
  }
}

/**
 * Check if an item exists in localStorage (and is not expired)
 * @param {string} key - The localStorage key
 * @returns {boolean} True if the item exists and is not expired
 */
export function hasItem(key) {
  return getItem(key) !== null;
}

/**
 * Clear all items from localStorage
 */
export function clear() {
  try {
    localStorage.clear();
  } catch (error) {
    console.warn('Error clearing localStorage:', error);
  }
}
