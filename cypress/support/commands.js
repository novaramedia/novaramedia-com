// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
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
        errorMsg.includes('fbq')
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
 * Wait for WordPress page to be fully loaded
 */
Cypress.Commands.add('waitForWordPress', () => {
  // Wait for common WordPress elements
  cy.get('body').should('exist');
  cy.get('body').should('have.class', 'wordpress');

  // Wait for any loading states to complete
  cy.get('.loading, .loader', { timeout: 10000 }).should('not.exist');
});
