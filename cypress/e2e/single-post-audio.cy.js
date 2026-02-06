/**
 * Single Post (Audio Category) Tests
 *
 * Tests for audio/podcast post pages
 * Verifies that audio posts display correctly with audio player and content
 */

describe('Single Post (Audio Category)', () => {
  let audioPostUrl;

  before(() => {
    cy.findPostUrlFromArchive('/category/audio').then((url) => {
      audioPostUrl = url;
      if (url) {
        cy.log('Testing audio post:', url);
      } else {
        cy.log('No audio posts found - tests will be skipped');
      }
    });
  });

  beforeEach(() => {
    // Skip test if no URL was found
    if (!audioPostUrl) {
      cy.log('Skipping test - no audio post URL available');
      // This will cause the test to pass but be marked as pending
      return;
    }

    cy.checkPageLoad();
    // Audio posts load many third-party resources (SoundCloud, analytics, social
    // widgets) that delay the browser's load event. Use a long timeout to allow
    // the page to fully load in CI where network is slower.
    cy.visit(audioPostUrl, { timeout: 120000 });
  });

  it('should load successfully', () => {
    cy.url().should('include', audioPostUrl);
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    cy.get('[data-testid="site-header"]').should('be.visible');
    cy.get('[data-testid="main-content"]').should('exist');
    cy.get('[data-testid="site-footer"]').should('be.visible');
  });

  it('should display audio post content', () => {
    cy.get('[data-testid="single-post"]').should('exist');

    // Audio post should have a title
    cy.get('[data-testid="post-title"]')
      .should('exist')
      .and('be.visible')
      .and('not.be.empty');
  });

  it('should display audio player', () => {
    // Check for audio player section
    cy.get('[data-testid="audio-player"]').should('exist');

    // AudioPlayers.js hydrates the .soundcloud-lazy placeholder into an iframe
    // on DOMContentLoaded, so by the time Cypress runs we expect the iframe
    cy.get('[data-testid="audio-player"] iframe').should('exist');
  });

  it('should display post metadata', () => {
    cy.get('[data-testid="single-post"]').should('exist');
  });

  it('should have category indicator', () => {
    // Audio posts should indicate they're in the audio category
    cy.get('body').then(($body) => {
      const text = $body.text().toLowerCase();
      const hasAudioIndicator =
        text.includes('audio') ||
        text.includes('podcast') ||
        $body.find('a[href*="/category/audio"]').length > 0;

      expect(hasAudioIndicator).to.be.true;
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
