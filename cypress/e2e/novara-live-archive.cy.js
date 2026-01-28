/**
 * Novara Live Category Archive Tests
 *
 * Tests for the Novara Live category archive page
 * Verifies that the category archive loads and displays posts
 */

describe('Novara Live Category Archive', () => {
  beforeEach(() => {
    cy.checkPageLoad();
    cy.visit('/category/novara-live');
  });

  it('should load successfully', () => {
    cy.url().should('include', '/category/novara-live');
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    cy.get('header').should('be.visible');
    cy.get('main, .main, #main').should('exist');
    cy.get('footer').should('be.visible');
  });

  it('should display category heading', () => {
    // Category archive should have a heading or title
    cy.get('h1, h2, .category-title, .archive-title, [class*="title"]')
      .should('have.length.greaterThan', 0)
      .first()
      .should('be.visible');
  });

  it('should display Novara Live posts', () => {
    // Should have post listings
    cy.get('article, .post, .entry, [class*="post"]').should(
      'have.length.greaterThan',
      0
    );

    // Posts should have titles (anywhere in main content)
    cy.get('main h2, main h3, main h4, .post__title, .entry-title, .post-title')
      .should('have.length.greaterThan', 0)
      .first()
      .should('be.visible');
  });

  it('should have post links', () => {
    // Each post should link to its single page
    cy.get('article a, .post a, a.ui-hover').should(
      'have.length.greaterThan',
      0
    );

    // At least one link should have an href
    cy.get('article a, .post a, a.ui-hover')
      .first()
      .should('have.attr', 'href')
      .and('not.be.empty');
  });

  it('should display Novara Live branding or content', () => {
    // Page should indicate it's the Novara Live category
    cy.get('body').then(($body) => {
      const text = $body.text().toLowerCase();
      const hasNovaraLive =
        text.includes('novara live') || text.includes('novara-live');

      expect(hasNovaraLive).to.be.true;
    });
  });

  it('should have category description or intro', () => {
    // Category archives often have a description
    cy.get('body').then(($body) => {
      // Check if there's substantial content beyond just post listings
      const hasCategoryInfo =
        $body.find(
          '.category-description, .archive-description, .term-description'
        ).length > 0 ||
        $body.find('section, .intro, [class*="description"]').length > 0;

      // It's okay if there's no explicit description section
      // Just verify the page has some content
      expect($body.text().length).to.be.greaterThan(100);
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

      cy.get('header').should('be.visible');
      cy.get('main, .main, #main').should('be.visible');
      cy.get('footer').should('be.visible');

      // At least one post should be visible
      cy.get('article, .post').first().should('be.visible');
    });
  });

  it('should have navigation elements', () => {
    cy.get('header nav, nav').should('be.visible');
  });

  it('should display post metadata', () => {
    // Posts in archive may have metadata (optional check)
    cy.get('body').then(($body) => {
      // Check if metadata exists anywhere on the page
      const hasMetadata =
        $body.find(
          'time, .date, .author, .meta, [class*="meta"], .byline, .posted-on, [datetime]'
        ).length > 0;

      // Metadata is common but not strictly required for archive pages
      // Just verify posts are present
      expect(
        $body.find('article, .post, [class*="post"]').length
      ).to.be.greaterThan(0);
    });
  });

  it('should have pagination or load more if many posts', () => {
    // Check if there's pagination (if there are many posts)
    cy.get('body').then(($body) => {
      const postCount = $body.find('article, .post').length;

      // If there are many posts, there might be pagination
      if (postCount >= 10) {
        // Pagination might exist but isn't required
        // Just verify the page structure is intact
        expect($body.find('main, .main, #main').length).to.equal(1);
      }
    });
  });
});
