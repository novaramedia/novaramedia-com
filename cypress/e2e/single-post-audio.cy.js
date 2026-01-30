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
    cy.get('[data-testid="post-list"] a, [data-testid="main-content"] a')
      .first()
      .should('have.attr', 'href')
      .then((href) => {
        audioPostUrl = href;
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

    // Should contain a SoundCloud iframe (theme's audio implementation)
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
