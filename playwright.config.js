const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: 'tests/e2e',

  // Tests are read-only against staging, so parallel workers are safe
  fullyParallel: true,

  forbidOnly: !!process.env.CI,

  // Retry configuration - retry failed tests in CI only
  retries: process.env.CI ? 2 : 0,

  // HTML report is uploaded as a CI artifact on failure
  reporter: [[process.env.CI ? 'github' : 'list'], ['html', { open: 'never' }]],

  // Timeout for expect() auto-waiting assertions
  expect: {
    timeout: 10000,
  },

  use: {
    // Base URL for the WordPress site - can be overridden with PLAYWRIGHT_BASE_URL env var
    baseURL: process.env.PLAYWRIGHT_BASE_URL || 'https://novaramedia.com',

    viewport: { width: 1280, height: 720 },

    navigationTimeout: 30000,

    // Failure artefacts
    video: 'retain-on-failure',
    screenshot: 'only-on-failure',
    trace: 'on-first-retry',
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
