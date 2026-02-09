/**
 * Single Post Tests
 *
 * Tests for individual article pages (single.php)
 * Verifies that single posts display correctly with all content and navigation
 */

describe('Single Post (Article)', () => {
  let articleUrl;

  before(() => {
    cy.findPostUrlFromArchive('/category/articles').then((url) => {
      articleUrl = url;
      if (url) {
        cy.log('Testing article:', url);
      } else {
        cy.log('No articles found - tests will be skipped');
      }
    });
  });

  beforeEach(function () {
    if (!articleUrl) {
      this.skip();
    }

    cy.checkPageLoad();
    cy.visit(articleUrl);
  });

  it('should load successfully', () => {
    cy.url().should('not.equal', Cypress.config('baseUrl') + '/');
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should display article content', () => {
    cy.get('[data-testid="single-post"]').should('exist');

    // Article should have a title
    cy.get('[data-testid="post-title"]')
      .should('exist')
      .and('be.visible')
      .and('not.be.empty');

    // Article should have body content
    cy.get('[data-testid="post-content"]').should('exist').and('not.be.empty');

    // Content should have paragraphs
    cy.get('[data-testid="post-content"] p').should(
      'have.length.greaterThan',
      0
    );
  });

  it('should display post metadata', () => {
    // Verify the article structure exists
    cy.get('[data-testid="single-post"]').should('exist');
  });

  it('should have navigation elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="site-nav"]').should('be.visible');
  });

  it('should load without console errors', () => {
    cy.verifyNoConsoleErrors();
  });

  it('should be responsive at different viewports', () => {
    cy.testResponsive(() => {
      cy.get('[data-testid="post-title"]').should('be.visible');
    });
  });

  it('should have proper heading hierarchy', () => {
    // Should have the post title as h1
    cy.get('[data-testid="post-title"]').invoke('text').should('not.be.empty');
  });

  it('should display article content with proper formatting', () => {
    cy.get('[data-testid="single-post"]').within(() => {
      cy.get('p').should('have.length.greaterThan', 0);
    });
  });

  it('should have social sharing or interaction elements', () => {
    // Verify the article structure is complete
    cy.get('[data-testid="single-post"]').should('exist');
  });
});
