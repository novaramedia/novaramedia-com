/**
 * Jobs Page Tests
 *
 * Tests for the Jobs page (page-jobs.php)
 * Verifies that the Jobs page loads and displays job listings
 */

describe('Jobs Page', () => {
  beforeEach(() => {
    cy.visit('/jobs');
  });

  it('should load successfully', () => {
    cy.url().should('include', '/jobs');
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should display jobs page content', () => {
    cy.get('[data-testid="jobs-page"]').should('exist');
  });

  it('should have main heading', () => {
    cy.get(
      '[data-testid="jobs-page"] h1, [data-testid="jobs-page"] h4, [data-testid="jobs-page"] h5'
    ).should('have.length.greaterThan', 0);

    // Verify page has substantial content
    cy.get('body').then(($body) => {
      expect($body.text().length).to.be.greaterThan(200);
    });
  });

  it('should display jobs content or listings', () => {
    cy.get('[data-testid="jobs-page"]').within(() => {
      // Should have headings
      cy.get('h1, h2, h3, h4, h5').should('have.length.greaterThan', 0);

      // Should have content - either job listings or informational text
      cy.get('p, li, div').should('have.length.greaterThan', 0);
    });
  });

  it('should display job-related information', () => {
    // Jobs page should contain relevant keywords
    cy.get('body').then(($body) => {
      const text = $body.text().toLowerCase();
      const hasJobContent =
        text.includes('job') ||
        text.includes('position') ||
        text.includes('hiring') ||
        text.includes('work') ||
        text.includes('no available positions');

      expect(hasJobContent).to.be.true;
    });
  });

  it('should handle empty job listings gracefully', () => {
    // Even if no jobs available, page should display properly
    cy.get('[data-testid="jobs-page"]').should('be.visible');

    // Should have some message or content
    cy.get('body').then(($body) => {
      expect($body.text().length).to.be.greaterThan(50);
    });
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive at different viewports', () => {
    cy.testResponsive(() => {
      cy.get(
        '[data-testid="jobs-page"] h1, [data-testid="jobs-page"] h4, [data-testid="jobs-page"] h5'
      )
        .first()
        .should('be.visible');
    });
  });

  it('should have navigation elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="site-nav"]').should('be.visible');
  });

  it('should have proper heading structure', () => {
    // Should have some heading structure
    cy.get(
      '[data-testid="jobs-page"] h1, [data-testid="jobs-page"] h4, [data-testid="jobs-page"] h5'
    )
      .should('have.length.greaterThan', 0)
      .first()
      .invoke('text')
      .should('not.be.empty');
  });
});
