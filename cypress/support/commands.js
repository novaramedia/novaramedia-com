// ***********************************************
// Custom Cypress Commands for Novara Media Theme
//
// These commands provide reusable testing utilities
// for WordPress theme testing.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************

/**
 * Check if page loaded without console errors
 * This command checks for JavaScript errors in the console
 */
Cypress.Commands.add('checkPageLoad', () => {
  cy.window().then((win) => {
    cy.spy(win.console, 'error').as('consoleError');
  });
});

/**
 * Verify no console errors occurred during page load
 */
Cypress.Commands.add('verifyNoConsoleErrors', () => {
  cy.get('@consoleError').should((spy) => {
    // Filter out known third-party errors that we can't control
    const relevantErrors = spy.getCalls().filter((call) => {
      const errorMsg = call.args[0]?.toString() || '';
      return !(
        errorMsg.includes('ResizeObserver') ||
        errorMsg.includes('google-analytics') ||
        errorMsg.includes('gtag') ||
        errorMsg.includes('fbq') ||
        errorMsg.includes('twitter') ||
        errorMsg.includes('facebook') ||
        errorMsg.includes('soundcloud') ||
        errorMsg.includes('youtube')
      );
    });

    expect(relevantErrors).to.have.length(0);
  });
});

/**
 * Check for broken images on the page
 */
Cypress.Commands.add('checkImages', () => {
  cy.get('img').each(($img) => {
    // Skip data URIs and lazy-loaded images
    const src = $img.attr('src');
    if (src && !src.startsWith('data:') && !src.includes('placeholder')) {
      cy.wrap($img)
        .should('be.visible')
        .and(($img) => {
          // Check if image loaded successfully
          expect($img[0].naturalWidth).to.be.greaterThan(0);
        });
    }
  });
});

/**
 * Test responsive behavior at different viewports
 */
Cypress.Commands.add('testResponsive', (callback) => {
  const viewports = [
    { name: 'mobile', width: 375, height: 667 },
    { name: 'tablet', width: 768, height: 1024 },
    { name: 'desktop', width: 1280, height: 720 },
  ];

  viewports.forEach((viewport) => {
    cy.viewport(viewport.width, viewport.height);
    cy.log(
      `Testing at ${viewport.name} viewport: ${viewport.width}x${viewport.height}`
    );
    callback(viewport);
  });
});

/**
 * Wait for WordPress/theme page to be fully loaded
 * Uses data-testid selectors for reliable element detection
 */
Cypress.Commands.add('waitForWordPress', () => {
  // Wait for body to exist
  cy.get('body').should('exist');

  // Wait for theme's key elements using data-testid
  cy.get('[data-testid="site-header"]', { timeout: 10000 }).should('exist');
  cy.get('[data-testid="main-content"]', { timeout: 10000 }).should('exist');
});

/**
 * Get element by data-testid attribute
 * Convenience command for cleaner test syntax
 */
Cypress.Commands.add('getByTestId', (testId, options = {}) => {
  return cy.get(`[data-testid="${testId}"]`, options);
});

/**
 * Verify critical page structure is present
 * Checks for header, main content, and footer
 */
Cypress.Commands.add('verifyCriticalPageStructure', () => {
  cy.get('[data-testid="site-header"]').should('be.visible');
  cy.get('[data-testid="main-content"]').should('exist');
  cy.get('[data-testid="site-footer"]').should('be.visible');
});
