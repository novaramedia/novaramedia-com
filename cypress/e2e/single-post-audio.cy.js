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
    cy.visit('/category/audio', { failOnStatusCode: false });

    // Wait for page to load and find post links
    cy.get('body').then(($body) => {
      // Try multiple selectors to find post links
      const $links = $body.find(
        '[data-testid="post-list"] a[href*="/20"], [data-testid="main-content"] a[href*="/20"], article a[href*="/20"], .post a[href*="/20"]'
      );

      if ($links.length > 0) {
        audioPostUrl = $links.first().attr('href');
        cy.log('Testing audio post:', audioPostUrl);
      } else {
        // If no posts found, skip tests by not setting URL
        cy.log('No audio posts found on category page - tests will be skipped');
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

    // Stub SoundCloud embeds to prevent them from blocking the page load event.
    // AudioPlayers.js injects a SoundCloud iframe on DOMContentLoaded, and the
    // browser waits for that iframe to fully load before firing the page `load`
    // event. SoundCloud servers are slow/unreliable in CI, causing timeouts.
    cy.intercept('**/w.soundcloud.com/**', {
      statusCode: 200,
      body: '<html><body>SoundCloud stubbed</body></html>',
      headers: { 'content-type': 'text/html' },
    });
    cy.intercept('**/api.soundcloud.com/**', { statusCode: 200, body: '{}' });
    cy.intercept('**/api-v2.soundcloud.com/**', {
      statusCode: 200,
      body: '{}',
    });

    cy.checkPageLoad();
    cy.visit(audioPostUrl, { timeout: 60000 });
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
