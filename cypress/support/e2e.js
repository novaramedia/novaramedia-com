// ***********************************************************
// This file is processed and loaded automatically before your test files.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands';

// Alternatively you can use CommonJS syntax:
// require('./commands')

// Bypass Kinsta full-page cache by appending a unique query string to every
// cy.visit(). Kinsta skips the page cache when a query string is present, so
// this guarantees the test fetches freshly-deployed HTML even if the API-based
// cache clear fails or the IDs drift.
Cypress.Commands.overwrite('visit', (originalVisit, url, options) => {
  if (typeof url === 'string') {
    const hashIndex = url.indexOf('#');
    const base = hashIndex === -1 ? url : url.slice(0, hashIndex);
    const fragment = hashIndex === -1 ? '' : url.slice(hashIndex);
    const separator = base.includes('?') ? '&' : '?';
    const cacheBuster = `cypress_cache_bust=${Date.now()}`;
    return originalVisit(`${base}${separator}${cacheBuster}${fragment}`, options);
  }
  return originalVisit(url, options);
});

// Set up console error spy before every test so verifyNoConsoleErrors() works.
// Using cy.on() ensures the listener is scoped to the current test and cleaned
// up automatically, preventing listener stacking across tests.
beforeEach(() => {
  cy.on('window:before:load', (win) => {
    cy.spy(win.console, 'error').as('consoleError');
  });
});

// Handle uncaught exceptions to prevent test failures from third-party scripts
Cypress.on('uncaught:exception', (err, runnable) => {
  // Returning false here prevents Cypress from failing the test
  // We log the error but don't fail on third-party script errors
  console.log('Uncaught exception:', err.message);

  // Don't fail tests on common third-party script errors
  if (
    err.message.includes('ResizeObserver') ||
    err.message.includes('google-analytics') ||
    err.message.includes('gtag') ||
    err.message.includes('fbq') ||
    err.message.includes('twitter')
  ) {
    return false;
  }

  // All other errors fail the test (Cypress default behaviour)
});
