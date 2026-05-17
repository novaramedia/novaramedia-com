/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import Cookies from 'js-cookie';

export class EmbedConsent {
  constructor() {}

  onReady() {
    const consented = Cookies.get('cookie-approval') === 'true';
    if (consented) {
      this.hydrateAllGates();
    } else {
      this.bindGateButtons();
      document.addEventListener('cookie-consent-granted', () => this.hydrateAllGates());
    }
  }

  hydrateAllGates() {
    const gates = document.querySelectorAll('.embed-consent-gate');
    gates.forEach(gate => this.hydrateGate(gate));
    if (gates.length > 0) {
      document.dispatchEvent(new CustomEvent('embed-consent-hydrated'));
    }
  }

  hydrateGate(gate) {
    const encoded = gate.getAttribute('data-embed-html');
    if (!encoded) {
      return;
    }
    // Content is server-generated WordPress oEmbed HTML, base64-encoded at render time.
    // We parse it via DOMParser to extract and re-execute script elements (required for
    // Twitter/X widget.js which won't run via innerHTML assignment in modern browsers).
    const html = atob(encoded);
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');

    // Scripts parsed via DOMParser don't auto-execute when inserted — recreate them
    doc.body.querySelectorAll('script').forEach(oldScript => {
      const newScript = document.createElement('script');
      Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
      newScript.textContent = oldScript.textContent;
      oldScript.replaceWith(newScript);
    });

    gate.replaceWith(...Array.from(doc.body.childNodes));
  }

  handleAccept() {
    Cookies.set('cookie-approval', 'true', { expires: 365 });
    document.dispatchEvent(new CustomEvent('cookie-consent-granted'));
    this.hydrateAllGates();
  }

  bindGateButtons() {
    document.querySelectorAll('.embed-consent-gate__accept').forEach(btn => {
      btn.addEventListener('click', () => this.handleAccept());
    });
  }
}
