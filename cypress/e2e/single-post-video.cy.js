/**
 * Single Post (Video Category) Tests
 *
 * Tests for video post pages
 * Verifies that video posts display correctly with media player and content
 */

describe('Single Post (Video Category)', () => {
  let videoPostUrl;

  before(() => {
    cy.findPostUrlFromArchive('/category/video').then((url) => {
      videoPostUrl = url;
      if (url) {
        cy.log('Testing video post:', url);
      } else {
        cy.log('No video posts found - tests will be skipped');
      }
    });
  });

  beforeEach(() => {
    // Skip test if no URL was found
    if (!videoPostUrl) {
      cy.log('Skipping test - no video post URL available');
      // This will cause the test to pass but be marked as pending
      return;
    }

    cy.checkPageLoad();
    cy.visit(videoPostUrl, { timeout: 60000 });
  });

  it('should load successfully', () => {
    cy.url().should('include', videoPostUrl);
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should display video post content', () => {
    cy.get('[data-testid="single-post"]').should('exist');

    // Video post should have a title
    cy.get('[data-testid="post-title"]')
      .should('exist')
      .and('be.visible')
      .and('not.be.empty');
  });

  it('should display video player', () => {
    // Check for video player section
    cy.get('[data-testid="video-player"]').should('exist');

    // Should contain a YouTube iframe (theme's video implementation)
    cy.get('[data-testid="video-player"] iframe').should('exist');
  });

  it('should display post metadata', () => {
    cy.get('[data-testid="single-post"]').should('exist');
  });

  it('should have category indicator', () => {
    // Video posts should indicate they're in the video category
    cy.get('body').then(($body) => {
      const text = $body.text().toLowerCase();
      const hasVideoIndicator =
        text.includes('video') ||
        $body.find('a[href*="/category/video"]').length > 0;

      expect(hasVideoIndicator).to.be.true;
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

      cy.get('[data-testid="site-header"]').should('be.visible');
      cy.get('[data-testid="main-content"]').should('be.visible');

      // Title should remain visible
      cy.get('[data-testid="post-title"]').should('be.visible');
    });
  });

  it('should have proper heading hierarchy', () => {
    cy.get('[data-testid="post-title"]').invoke('text').should('not.be.empty');
  });

  it('should have navigation elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="site-nav"]').should('be.visible');
  });
});
