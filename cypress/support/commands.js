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
 * Test responsive behavior at different viewports.
 * Asserts critical page structure (header, main-content, footer) at each
 * breakpoint. Pass an optional callback for page-specific assertions.
 */
Cypress.Commands.add('testResponsive', (callback) => {
  const viewports = [
    { name: 'mobile', width: 375, height: 667 },
    { name: 'tablet', width: 768, height: 1024 },
    { name: 'desktop', width: 1280, height: 720 },
  ];

  cy.wrap(viewports).each((viewport) => {
    cy.viewport(viewport.width, viewport.height);
    cy.log(
      `Testing at ${viewport.name} viewport: ${viewport.width}x${viewport.height}`
    );

    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('be.visible');
    cy.get('[data-testid="site-footer"]').should('be.visible');

    if (callback) {
      callback(viewport);
    }
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

/**
 * Find a single post URL from a category archive page.
 *
 * Visits the given archive URL and finds the first post card link that matches
 * a WordPress year/month/day permalink pattern. Targets article.type-post
 * elements rendered by flex-post.php, excluding posts from serial podcast
 * categories that redirect to show pages instead of single post views.
 *
 * Serial categories are defined in lib/functions-hooks.php ($serial_categories).
 *
 * @param {string} archiveUrl - The category archive path to visit (e.g. '/category/audio')
 * @returns {Cypress.Chainable<string|null>} The post URL or null if none found
 */
Cypress.Commands.add('findPostUrlFromArchive', (archiveUrl) => {
  // Serial podcast categories that redirect to show pages instead of single
  // post views. Keep in sync with $serial_categories in lib/functions-hooks.php.
  const serialExclusions =
    ':not(.category-foreign-agent):not(.category-committed)';

  cy.visit(archiveUrl, { failOnStatusCode: false });

  return cy.get('body').then(($body) => {
    const postUrlPattern = /\/\d{4}\/\d{2}\/\d{2}\//;

    // Find links inside post card articles. WordPress post_class() adds
    // category-{slug} classes so we can exclude serial podcast posts.
    // No data-testid scoping — works whether or not testid attrs are deployed.
    const $links = $body.find(`article${serialExclusions} a`);

    let postUrl = null;
    $links.each((i, el) => {
      const href = Cypress.$(el).attr('href');
      if (!postUrl && href && postUrlPattern.test(href)) {
        postUrl = href;
      }
    });

    return postUrl;
  });
});
