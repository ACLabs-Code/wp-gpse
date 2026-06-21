# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Build & package plugin to dist/
make build

# Run all tests (JS + PHP)
make test

# Individual test suites
make test-js          # Jest
make test-php         # PHPUnit in Docker

# Linting
make lint             # ESLint + StyleLint

# JS asset development
npm run start         # webpack watch (gpse/src/ → gpse/build/)

# Local WordPress environment
npm run env:start     # Start wp-env (PHP 8.2)
npm run env:stop      # Stop wp-env
```

`make test-php` spins up `docker-compose.test.yml` (PHP 8.5 + MySQL 9.7) — Docker must be running.

Run a single PHP test class: `make test-php` targets the full suite; to run one class pass it via phpunit directly inside the container or use `npm run test:php -- --filter ClassName`.

## Architecture

WordPress plugin that intercepts native WP search and redirects to a Google Programmable Search Engine (CSE) results page.

**Entry point**: `gpse/gpse.php` — defines constants, fires `plugins_loaded` to initialize all classes.

**Four PHP classes** (no namespaces; WP_ prefix convention):

| Class | File | Responsibility |
|-------|------|----------------|
| `WP_GPSE_Admin` | `gpse/includes/class-wp-gpse-admin.php` | Settings page under Settings > GPSE; registers three options: `wp_gpse_cx_id`, `wp_gpse_results_page_id`, `wp_gpse_autocomplete_margin` |
| `WP_GPSE_Frontend` | `gpse/includes/class-wp-gpse-frontend.php` | Search redirect via `template_redirect`; `[gpse_results]` shortcode; enqueues Google CSE script + plugin styles |
| `WP_GPSE_Blocks` | `gpse/includes/class-wp-gpse-blocks.php` | Registers `gpse/search-results` Gutenberg block (Block API v3, server-side dynamic) |
| `WP_GPSE_Helpers` | `gpse/includes/class-wp-gpse-helpers.php` | Static utilities for CSE HTML markup — shared between shortcode and block to avoid duplication |

**Frontend flow**: WP search → `template_redirect` redirects `/?s=query` to results page with `?q=query` → results page renders `[gpse_results]` shortcode or the GPSE Gutenberg block → Google CSE JS populates results using the configured CX ID.

**JS/React**: Source lives in `gpse/src/search-results/`. Compiled to `gpse/build/` via `@wordpress/scripts` (webpack). Never edit `gpse/build/` directly.

**Tests**:
- PHP: `tests/php/` — PHPUnit 9.6, bootstrap in `tests/bootstrap.php`
- JS: `gpse/src/search-results/index.test.js` — Jest

**Release artifact**: `dist/` directory produced by `make build`. PHP 8.2+ required.
