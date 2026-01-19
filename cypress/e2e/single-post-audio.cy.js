/**
 * Single Post (Audio Category) Tests
 *
 * Tests for audio/podcast post pages
 * Verifies that audio posts display correctly with audio player and content
 */

describe('Single Post (Audio Category)', () => {
  let audioPostUrl;

  before(() => {
    // Visit audio category archive to find an audio post
    cy.visit('/category/audio');

    // Find first audio post link from the main content area
    cy.get('#main-content a.ui-hover')
      .first()
      .then(($link) => {
        audioPostUrl = $link.prop('href');
        cy.log('Testing audio post:', audioPostUrl);
      });
  });

  beforeEach(() => {
    if (audioPostUrl) {
      cy.checkPageLoad();
      cy.visit(audioPostUrl);
    }
  });

  it('should load successfully', () => {
    cy.url().should('include', audioPostUrl);
    cy.title().should('not.be.empty');
  });

  it('should display critical page elements', () => {
    cy.get('header').should('be.visible');
    cy.get('main, .main, #main').should('exist');
    cy.get('footer').should('be.visible');
  });

  it('should display audio post content', () => {
    // Audio post should have a title
    cy.get('article h1, .article h1, .post h1, h1.entry-title, h1.post-title')
      .should('have.length.greaterThan', 0)
      .first()
      .should('be.visible')
      .and('not.be.empty');

    // Should have main content area
    cy.get('article, .article, .post').should('exist');
  });

  it('should display audio player or embed', () => {
    // Check for common audio elements
    cy.get('body').then(($body) => {
      const hasAudio =
        $body.find('audio').length > 0 ||
        $body.find('iframe[src*="soundcloud"]').length > 0 ||
        $body.find('iframe[src*="spotify"]').length > 0 ||
        $body.find('iframe[src*="simplecast"]').length > 0 ||
        $body.find('.audio-player, .podcast-player, .audio-embed').length > 0;

      // Audio posts should have some form of audio player
      expect(hasAudio).to.be.true;
    });
  });

  it('should display post metadata', () => {
    cy.get('article').within(() => {
      cy.get(
        '.author, .byline, .posted-by, time, .date, .category, .meta, [class*="meta"]'
      ).should('have.length.greaterThan', 0);
    });
  });

  it('should have category indicator', () => {
    // Audio posts should indicate they're in the audio category
    cy.get('body').then(($body) => {
      const hasAudioIndicator =
        $body.find('a[href*="/category/audio"]').length > 0 ||
        $body.find('.category:contains("Audio")').length > 0 ||
        $body.find('.category:contains("Podcast")').length > 0 ||
        $body.text().toLowerCase().includes('audio') ||
        $body.text().toLowerCase().includes('podcast');

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

      cy.get('header').should('be.visible');
      cy.get('main, .main, #main').should('be.visible');

      // Title should remain visible
      cy.get('article h1, .article h1, h1.entry-title, h1.post-title')
        .first()
        .should('be.visible');
    });
  });

  it('should have proper heading hierarchy', () => {
    cy.get('h1').should('have.length.greaterThan', 0);
    cy.get('h1').first().invoke('text').should('not.be.empty');
  });

  it('should have navigation elements', () => {
    cy.get('header nav, nav').should('be.visible');
  });
});
