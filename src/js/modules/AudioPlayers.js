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
    if (this.players.length === 0) {
      return;
    }

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
    if (!dataSrc) {
      return;
    }

    // Validate the data-src URL before using it for the iframe src
    let url;
    try {
      url = new URL(dataSrc, window.location.href);
    } catch (error) {
      // Malformed URL, do not create the iframe
      console.log('Invalid SoundCloud URL:', dataSrc, error);
      return;
    }

    const allowedHosts = [
      'soundcloud.com',
      'www.soundcloud.com',
      'w.soundcloud.com',
    ];

    if (
      (url.protocol !== 'https:' && url.protocol !== 'http:') ||
      !allowedHosts.includes(url.hostname)
    ) {
      // Disallow unexpected protocols or hosts
      return;
    }

    // Create iframe element
    const iframe = document.createElement('iframe');
    iframe.src = url.toString();
    iframe.width = playerElement.getAttribute('data-width') || '100%';
    iframe.height = playerElement.getAttribute('data-height') || '166';
    iframe.style.overflow = 'hidden';

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
