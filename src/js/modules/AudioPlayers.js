/**
 * AudioPlayers Module
 *
 * Handles lazy loading of SoundCloud players by switching data-src to src
 * with simple throttling for multiple players.
 */
export class AudioPlayers {
  constructor() {
    this.players = [];
    this.loadDelay = 200; // milliseconds between player loads
  }

  onReady() {
    this.findPlayers();
    this.loadPlayersWithThrottling();
  }

  findPlayers() {
    this.players = document.querySelectorAll('.soundcloud-lazy[data-src]');
  }

  loadPlayersWithThrottling() {
    if (this.players.length === 0) return;

    // Load first player immediately
    this.loadPlayer(this.players[0]);

    // Load remaining players with throttling
    for (let i = 1; i < this.players.length; i++) {
      setTimeout(() => {
        this.loadPlayer(this.players[i]);
      }, this.loadDelay * i);
    }
  }

  loadPlayer(playerElement) {
    const dataSrc = playerElement.getAttribute('data-src');
    if (!dataSrc) return;

    // Create iframe element
    const iframe = document.createElement('iframe');
    iframe.src = dataSrc;
    iframe.width = playerElement.getAttribute('data-width') || '100%';
    iframe.height = playerElement.getAttribute('data-height') || '166';
    iframe.scrolling = 'no';
    iframe.frameBorder = 'no';
    iframe.allow = 'autoplay';

    // Replace the placeholder with the iframe
    playerElement.innerHTML = '';
    playerElement.appendChild(iframe);

    // Remove lazy class and data attributes
    playerElement.classList.remove('soundcloud-lazy');
    playerElement.removeAttribute('data-src');
    playerElement.removeAttribute('data-width');
    playerElement.removeAttribute('data-height');
  }
}
