const { defineConfig } = require('cypress');

module.exports = defineConfig({
  e2e: {
    // Base URL for the WordPress site - can be overridden with CYPRESS_BASE_URL env var
    baseUrl: process.env.CYPRESS_BASE_URL || 'https://novaramedia.com',
    
    // Test configuration
    viewportWidth: 1280,
    viewportHeight: 720,
    
    // Video recording settings
    video: true,
    videoCompression: 32,
    
    // Screenshot settings
    screenshotOnRunFailure: true,
    
    // Timeouts
    defaultCommandTimeout: 10000,
    pageLoadTimeout: 30000,
    
    // Retry configuration
    retries: {
      runMode: 2,  // Retry failed tests in CI
      openMode: 0  // Don't retry in interactive mode
    },
    
    // Test isolation - keep cookies between tests in same spec for performance
    testIsolation: false,
    
    setupNodeEvents(on, config) {
      // implement node event listeners here
      return config;
    },
  },
  
  // Chrome flags for better CI performance
  chromeWebSecurity: false,
});
