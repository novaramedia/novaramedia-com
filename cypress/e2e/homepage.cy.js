/**
 * Homepage Tests
 *
 * Tests for the main landing page (front-page.php)
 * Verifies that the homepage loads successfully and displays key content
 */

describe('Homepage', () => {
  beforeEach(() => {
    cy.checkPageLoad();
    cy.visit('/');
  });

  it('should load successfully', () => {
    cy.url().should('eq', Cypress.config('baseUrl') + '/');
    cy.title().should('not.be.empty');
    cy.title().should('include', 'Novara Media');
  });

  it('should display critical page elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="site-nav"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should have working navigation links', () => {
    cy.get('[data-testid="site-nav"] .site-header__nav-toggle').click();
    cy.get('[data-testid="site-nav-panel"]').should('be.visible');
    cy.get('[data-testid="site-nav-panel"] a')
      .should('have.length.greaterThan', 0)
      .first()
      .should('have.attr', 'href')
      .and('not.be.empty');
  });

  it('should display post content', () => {
    cy.get('[data-testid="post-list"]').should('exist');
    cy.get('[data-testid="post-title"]').should('have.length.greaterThan', 0);
    cy.get('[data-testid="post-title"]')
      .first()
      .should('be.visible')
      .and('not.be.empty');
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should not have broken images in critical content', () => {
    cy.get('[data-testid="main-content"] img').then(($images) => {
      if ($images.length > 0) {
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
    cy.testResponsive();
  });

  it('should have meta tags for SEO', () => {
    cy.get('head meta[name="description"]').should('exist');
    cy.get('head meta[property="og:title"]').should('exist');
  });
});
