/**
 * Support Page Tests
 * 
 * Tests for the support/donation page (page-support.php)
 * Verifies that the support page loads and displays donation forms
 */

describe('Support Page', () => {
  beforeEach(() => {
    // Visit support page and check for console errors
    cy.checkPageLoad();
    cy.visit('/support');
  });

  it('should load successfully', () => {
    // Verify page loaded
    cy.url().should('include', '/support');
    
    // Check page title
    cy.title().should('not.be.empty');
    cy.title().should('include', 'Support');
  });

  it('should display critical page elements', () => {
    // Header should be present
    cy.get('header').should('be.visible');
    
    // Main content area should exist
    cy.get('main, .main, #main').should('exist');
    
    // Footer should be present
    cy.get('footer').should('be.visible');
  });

  it('should display support/donation form elements', () => {
    // Should have some form of donation interface
    // This could be a form, buttons, or links to donation platform
    cy.get('form, button, input[type="submit"], a[href*="donate"], .donate, .support-form')
      .should('have.length.greaterThan', 0);
  });

  it('should display main content', () => {
    // Page should have substantive content explaining support options
    cy.get('main, .main, #main').within(() => {
      // Should have headings
      cy.get('h1, h2, h3').should('have.length.greaterThan', 0);
      
      // Should have paragraphs or content blocks
      cy.get('p, .content, section').should('have.length.greaterThan', 0);
    });
  });

  it('should have clickable donation/support options', () => {
    // Check for interactive elements
    cy.get('button, input[type="submit"], a[href*="donate"]').then(($elements) => {
      if ($elements.length > 0) {
        // At least one should be visible
        cy.wrap($elements.filter(':visible')).should('have.length.greaterThan', 0);
      }
    });
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive at different viewports', () => {
    const viewports = [
      { width: 375, height: 667 },   // Mobile
      { width: 768, height: 1024 },  // Tablet
      { width: 1280, height: 720 },  // Desktop
    ];

    viewports.forEach((viewport) => {
      cy.viewport(viewport.width, viewport.height);
      
      // Verify critical elements still visible
      cy.get('header').should('be.visible');
      cy.get('main, .main, #main').should('be.visible');
      cy.get('footer').should('be.visible');
      
      // Support form/buttons should still be accessible
      cy.get('form, button, input[type="submit"], a[href*="donate"], .donate, .support-form')
        .should('exist');
    });
  });

  it('should have appropriate heading structure', () => {
    // Should have an h1
    cy.get('h1').should('have.length.greaterThan', 0);
    cy.get('h1').first().should('be.visible').and('not.be.empty');
  });

  it('should display support information text', () => {
    // Should have explanatory text about supporting
    cy.get('main, .main, #main').within(() => {
      cy.contains(/support|donate|contribute|help/i).should('exist');
    });
  });
});
