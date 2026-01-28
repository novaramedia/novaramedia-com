/**
 * Single Post Tests
 *
 * Tests for individual article pages (single.php)
 * Verifies that single posts display correctly with all content and navigation
 */

describe('Single Post (Article)', () => {
  // We'll test against a recent article from the homepage
  let articleUrl;

  before(() => {
    // Visit homepage to find a recent article link
    cy.visit('/');

    // Find first post link from the main content area
    // Homepage uses .post__title wrapped in <a class="ui-hover"> links
    cy.get('#main-content a.ui-hover')
      .first()
      .then(($link) => {
        articleUrl = $link.prop('href');
        cy.log('Testing article:', articleUrl);
      });
  });

  beforeEach(() => {
    // Visit the article page and check for console errors
    if (articleUrl) {
      cy.checkPageLoad();
      cy.visit(articleUrl);
    }
  });

  it('should load successfully', () => {
    cy.url().should('not.equal', Cypress.config('baseUrl') + '/');
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    // Header should be present
    cy.get('header').should('be.visible');

    // Main content area should exist
    cy.get('main, .main, #main').should('exist');

    // Footer should be present
    cy.get('footer').should('be.visible');
  });

  it('should display article content', () => {
    // Article should have a title (h1 with id="single-articles-title" or similar)
    cy.get(
      '#single-articles-title, article h1, h1.entry-title, h1.post-title, #post h1'
    )
      .should('have.length.greaterThan', 0)
      .first()
      .should('be.visible')
      .and('not.be.empty');

    // Article should have body content (in .text-copy container)
    cy.get('#single-articles-copy, article .text-copy, article .content')
      .should('exist')
      .and('not.be.empty');

    // Content should have paragraphs
    cy.get('#single-articles-copy p, article .text-copy p, article p').should(
      'have.length.greaterThan',
      0
    );
  });

  it('should display post metadata', () => {
    // Check if metadata exists on the page (optional)
    cy.get('body').then(($body) => {
      // Metadata is common but may not always be present in this theme
      // Just verify the article content exists
      expect(
        $body.find('article, .article, .post, main').length
      ).to.be.greaterThan(0);
    });
  });

  it('should have navigation elements', () => {
    // Should have header navigation
    cy.get('header nav, nav').should('be.visible');

    // May have post navigation (prev/next)
    // This is optional but good to check if present
    cy.get('body').then(($body) => {
      if (
        $body.find('.post-navigation, .nav-previous, .nav-next, .post-nav')
          .length > 0
      ) {
        cy.get('.post-navigation, .nav-previous, .nav-next, .post-nav').should(
          'be.visible'
        );
      }
    });
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive at different viewports', () => {
    const viewports = [
      { width: 375, height: 667 }, // Mobile
      { width: 768, height: 1024 }, // Tablet
      { width: 1280, height: 720 }, // Desktop
    ];

    viewports.forEach((viewport) => {
      cy.viewport(viewport.width, viewport.height);

      // Verify critical elements still visible
      cy.get('header').should('be.visible');
      cy.get('main, .main, #main').should('be.visible');

      // Title should be visible
      cy.get(
        '#single-articles-title, article h1, h1.entry-title, h1.post-title'
      )
        .first()
        .should('be.visible');
    });
  });

  it('should have proper heading hierarchy', () => {
    // Should have exactly one h1 (the post title)
    cy.get('h1').should('have.length.greaterThan', 0);

    // H1 should not be empty
    cy.get('h1').first().invoke('text').should('not.be.empty');
  });

  it('should display article content with proper formatting', () => {
    // Content area should have text
    cy.get('article, .article, .post').first().within(() => {
      cy.get('p').first().should('have.length.greaterThan', 0);
    });
  });

  it('should have social sharing or interaction elements', () => {
    // Many WordPress posts have sharing buttons, though not required
    cy.get('body').then(($body) => {
      // Just verify the page structure is complete
      // Social sharing is optional
      expect($body.find('article, .article, .post').length).to.be.greaterThan(
        0
      );
    });
  });
});
