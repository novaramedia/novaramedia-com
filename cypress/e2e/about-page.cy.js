/**
 * About Page Tests
 *
 * Tests for the About page (page-about.php)
 * Verifies that the About page loads and displays organizational information
 */

describe('About Page', () => {
  beforeEach(() => {
    cy.checkPageLoad();
    cy.visit('/about');
  });

  it('should load successfully', () => {
    cy.url().should('include', '/about');
    cy.title().should('not.be.empty');
    cy.title().should('match', /about/i);
  });

  it('should display critical page elements', () => {
    cy.get('header').should('be.visible');
    cy.get('main, .main, #main').should('exist');
    cy.get('footer').should('be.visible');
  });

  it('should have main heading', () => {
    // About page should have some heading structure
    cy.get('h1, h2, h3, h4').should('have.length.greaterThan', 0);

    // Verify page has substantial content
    cy.get('body').then(($body) => {
      expect($body.text().length).to.be.greaterThan(200);
    });
  });

  it('should display about content', () => {
    // Should have headings for different sections
    cy.get('h1, h2, h3, h4').should('have.length.greaterThan', 0);

    // Should have substantial content
    cy.get('p, section, .content, div[class*="content"]').should(
      'have.length.greaterThan',
      0
    );
  });

  it('should display organizational information', () => {
    // About page typically contains mission, team, or history information
    cy.get('body').then(($body) => {
      const text = $body.text().toLowerCase();
      const hasRelevantContent =
        text.includes('novara') ||
        text.includes('media') ||
        text.includes('mission') ||
        text.includes('team') ||
        text.includes('about');

      expect(hasRelevantContent).to.be.true;
    });
  });

  it('should have proper content structure', () => {
    // About pages typically have sections or content blocks
    cy.get('main, .main, #main').within(() => {
      cy.get(
        'section, article, .section, .content-block, div[class*="grid"]'
      ).should('have.length.greaterThan', 0);
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

      // Some heading should be visible
      cy.get('h1, h2, h3, h4').first().should('be.visible');
    });
  });

  it('should have navigation links', () => {
    cy.get('header nav, nav').should('be.visible');
    cy.get('nav a').should('have.length.greaterThan', 0);
  });

  it('should have appropriate meta tags', () => {
    cy.get('head meta[name="description"]').should('exist');
    cy.get('head meta[property="og:title"]').should('exist');
  });
});
