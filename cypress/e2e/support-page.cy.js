/**
 * Support Page Tests
 *
 * Tests for the support/donation page (page-support.php)
 * Verifies that the support page loads and displays donation forms
 */

describe('Support Page', () => {
  beforeEach(() => {
    cy.checkPageLoad();
    cy.visit('/support');
  });

  it('should load successfully', () => {
    cy.url().should('include', '/support');
    cy.title().should('not.be.empty');
    cy.title().should('include', 'Support');
  });

  it('should display critical page elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should display support page content', () => {
    cy.get('[data-testid="support-page"]').should('exist');
  });

  it('should display support/donation form elements', () => {
    // Should have some form of donation interface
    cy.get('[data-testid="support-page"]').within(() => {
      cy.get('form, button, a[href*="donate"]').should(
        'have.length.greaterThan',
        0
      );
    });
  });

  it('should display main content', () => {
    cy.get('[data-testid="support-page"]').within(() => {
      // Should have headings
      cy.get('h1, h2, h3, h4').should('have.length.greaterThan', 0);

      // Should have paragraphs or content blocks
      cy.get('p, div').should('have.length.greaterThan', 0);
    });
  });

  it('should have support form submit button', () => {
    // Check specifically for the support form submit button
    cy.get('[data-testid="support-page"]').within(() => {
      cy.get('form button[type="submit"], form input[type="submit"]').should(
        'exist'
      );
    });
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive at different viewports', () => {
    cy.testResponsive(() => {
      cy.get('[data-testid="support-page"]').should('exist');
    });
  });

  it('should have appropriate heading structure', () => {
    cy.get('[data-testid="support-page"] h3, [data-testid="support-page"] h4')
      .should('have.length.greaterThan', 0)
      .first()
      .should('be.visible')
      .and('not.be.empty');
  });

  it('should display support information text', () => {
    cy.get('[data-testid="support-page"]').within(() => {
      cy.contains(/support|donate|contribute|help/i).should('exist');
    });
  });
});
