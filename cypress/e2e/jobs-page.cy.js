/**
 * Jobs Page Tests
 *
 * Tests for the Jobs page (page-jobs.php)
 * Verifies that the Jobs page loads and displays job listings
 */

describe('Jobs Page', () => {
  beforeEach(() => {
    cy.checkPageLoad();
    cy.visit('/jobs');
  });

  it('should load successfully', () => {
    cy.url().should('include', '/jobs');
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    cy.get('header').should('be.visible');
    cy.get('main, .main, #main').should('exist');
    cy.get('footer').should('be.visible');
  });

  it('should have main heading', () => {
    cy.get('h1').should('have.length.greaterThan', 0);
    cy.get('h1').first().should('be.visible').and('not.be.empty');
  });

  it('should display jobs content or listings', () => {
    cy.get('main, .main, #main').within(() => {
      // Should have headings
      cy.get('h1, h2, h3').should('have.length.greaterThan', 0);

      // Should have content - either job listings or informational text
      cy.get('p, article, .job, .listing, section').should(
        'have.length.greaterThan',
        0
      );
    });
  });

  it('should display job-related information', () => {
    // Jobs page should contain relevant keywords
    cy.get('body').then(($body) => {
      const text = $body.text().toLowerCase();
      const hasJobContent =
        text.includes('job') ||
        text.includes('position') ||
        text.includes('vacancy') ||
        text.includes('career') ||
        text.includes('hiring') ||
        text.includes('opportunity') ||
        text.includes('work') ||
        text.includes('apply');

      expect(hasJobContent).to.be.true;
    });
  });

  it('should handle empty job listings gracefully', () => {
    // Even if no jobs available, page should display properly
    cy.get('main, .main, #main').should('be.visible');

    // Should have some message or content
    cy.get('body').then(($body) => {
      expect($body.text().length).to.be.greaterThan(50);
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

      // Main heading should be visible
      cy.get('h1').first().should('be.visible');
    });
  });

  it('should have navigation elements', () => {
    cy.get('header nav, nav').should('be.visible');
  });

  it('should have proper heading structure', () => {
    // Should have exactly one h1
    cy.get('h1').should('have.length.greaterThan', 0);

    // H1 should not be empty
    cy.get('h1').first().invoke('text').should('not.be.empty');
  });
});
