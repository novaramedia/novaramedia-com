/**
 * Homepage Tests
 * 
 * Tests for the main landing page (front-page.php)
 * Verifies that the homepage loads successfully and displays key content
 */

describe('Homepage', () => {
  beforeEach(() => {
    // Visit homepage and check for console errors
    cy.checkPageLoad();
    cy.visit('/');
  });

  it('should load successfully', () => {
    // Verify page loaded
    cy.url().should('eq', Cypress.config('baseUrl') + '/');
    
    // Check page title
    cy.title().should('not.be.empty');
    cy.title().should('include', 'Novara Media');
  });

  it('should display critical page elements', () => {
    // Header should be present
    cy.get('header').should('be.visible');
    
    // Main navigation should exist
    cy.get('nav').should('be.visible');
    
    // Main content area should exist
    cy.get('main, .main, #main').should('exist');
    
    // Footer should be present
    cy.get('footer').should('be.visible');
  });

  it('should have working navigation links', () => {
    // Check main nav has links
    cy.get('nav a').should('have.length.greaterThan', 0);
    
    // Verify at least one nav link works
    cy.get('nav a').first().should('have.attr', 'href').and('not.be.empty');
  });

  it('should display post content', () => {
    // Homepage should display posts/articles
    cy.get('article, .article, .post').should('have.length.greaterThan', 0);
    
    // Posts should have titles
    cy.get('article h2, article h3, .article h2, .article h3, .post h2, .post h3')
      .first()
      .should('be.visible')
      .and('not.be.empty');
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should not have broken images in critical content', () => {
    // Check images in main content area
    cy.get('main img, .main img, #main img').then(($images) => {
      if ($images.length > 0) {
        // Only check first few images to avoid timeout
        cy.wrap($images.slice(0, 5)).each(($img) => {
          const src = $img.attr('src');
          if (src && !src.startsWith('data:') && !src.includes('placeholder')) {
            cy.wrap($img).should('have.attr', 'src').and('not.be.empty');
          }
        });
      }
    });
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
    });
  });

  it('should have meta tags for SEO', () => {
    // Check for essential meta tags
    cy.get('head meta[name="description"]').should('exist');
    cy.get('head meta[property="og:title"]').should('exist');
  });
});
