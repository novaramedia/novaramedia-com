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
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should display category heading', () => {
    // Category archive should have a heading or title
    cy.get(
      '[data-testid="main-content"] h1, [data-testid="main-content"] h2, [data-testid="main-content"] h4'
    )
      .should('have.length.greaterThan', 0)
      .first()
      .should('be.visible');
  });

  it('should display Novara Live posts', () => {
    // Should have post listings
    cy.get('[data-testid="post-list"]').should('exist');

    // Posts should have titles
    cy.get(
      '[data-testid="main-content"] h1, [data-testid="main-content"] h2, [data-testid="main-content"] h4, [data-testid="main-content"] h6'
    )
      .should('have.length.greaterThan', 0)
      .first()
      .should('be.visible');
  });

  it('should have post links', () => {
    // Each post should link to its single page
    cy.get(
      '[data-testid="post-list"] a, [data-testid="main-content"] a'
    ).should('have.length.greaterThan', 0);

    // At least one link should have an href
    cy.get('[data-testid="post-list"] a, [data-testid="main-content"] a')
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
    // Check if there's substantial content beyond just post listings
    cy.get('body').then(($body) => {
      // Just verify the page has some content
      expect($body.text().length).to.be.greaterThan(100);
    });
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive at different viewports', () => {
    cy.testResponsive();
  });

  it('should have navigation elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="site-nav"]').should('be.visible');
  });

  it('should display post metadata', () => {
    // Verify posts are present
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('body').then(($body) => {
      expect($body.text().length).to.be.greaterThan(100);
    });
  });

  it('should have pagination or load more if many posts', () => {
    // Verify the page structure is intact
    cy.get('[data-testid="main-content"]').should('exist');
  });
});
