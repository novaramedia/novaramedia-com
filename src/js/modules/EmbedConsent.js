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
      document.addEventListener('cookie-consent-granted', () => this.hydrateAllGates(), { once: true });
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
    const template = gate.querySelector('template.embed-consent-gate__template');
    if (!template) {
      return;
    }

    // Clone the inert template content (already browser-parsed, no string→HTML step).
    // Scripts inside <template> don't auto-execute when cloned — recreate them as live elements.
    const frag = template.content.cloneNode(true);
    frag.querySelectorAll('script').forEach(oldScript => {
      const newScript = document.createElement('script');
      Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
      newScript.textContent = oldScript.textContent;
      oldScript.replaceWith(newScript);
    });

    gate.replaceWith(frag);
  }

  handleAccept() {
    Cookies.set('cookie-approval', 'true', { expires: 365 });
    document.dispatchEvent(new CustomEvent('cookie-consent-granted'));
  }

  bindGateButtons() {
    document.querySelectorAll('.embed-consent-gate__accept').forEach(btn => {
      btn.addEventListener('click', () => this.handleAccept());
    });
  }
}
