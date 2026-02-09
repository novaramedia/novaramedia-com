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
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should display about page content', () => {
    cy.get('[data-testid="about-page"]').should('exist');
  });

  it('should have main heading', () => {
    cy.get(
      '[data-testid="about-page"] h1, [data-testid="about-page"] h4'
    ).should('have.length.greaterThan', 0);

    // Verify page has substantial content
    cy.get('body').then(($body) => {
      expect($body.text().length).to.be.greaterThan(200);
    });
  });

  it('should display about content sections', () => {
    cy.get('[data-testid="about-page"]').within(() => {
      // Should have headings for different sections
      cy.get('h1, h2, h3, h4').should('have.length.greaterThan', 0);

      // Should have substantial content
      cy.get('p, div').should('have.length.greaterThan', 0);
    });
  });

  it('should display organizational information', () => {
    // About page typically contains mission, team, or history information
    cy.get('body').then(($body) => {
      const text = $body.text().toLowerCase();
      const hasRelevantContent =
        text.includes('novara') ||
        text.includes('media') ||
        text.includes('team') ||
        text.includes('about');

      expect(hasRelevantContent).to.be.true;
    });
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive at different viewports', () => {
    cy.testResponsive(() => {
      cy.get('[data-testid="about-page"] h1, [data-testid="about-page"] h4')
        .first()
        .should('be.visible');
    });
  });

  it('should have navigation links', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="site-nav"]').should('be.visible');
    cy.get('[data-testid="site-nav"] a, [data-testid="site-header"] a').should(
      'have.length.greaterThan',
      0
    );
  });

  it('should have appropriate meta tags', () => {
    cy.get('head meta[name="description"]').should('exist');
    cy.get('head meta[property="og:title"]').should('exist');
  });
});
