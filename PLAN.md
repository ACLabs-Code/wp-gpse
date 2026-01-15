# GPSE Plugin Improvement Plan

## 1. Internationalization (i18n)
*   **Why:** Currently, all user-facing text is hardcoded in English.
*   **Plan:**
    *   Load the plugin text domain using `load_plugin_textdomain()` in `gpse.php`.
    *   Wrap all static strings in translation functions like `__()`, `_e()`, and `esc_html__()`.
    *   Generate a `.pot` file for translators.

## 2. Clean Uninstallation
*   **Why:** The plugin leaves database options (`wp_gpse_cx_id`, `wp_gpse_results_page_id`, etc.) behind when deleted.
*   **Plan:**
    *   Create an `uninstall.php` file in the root directory.
    *   Use `delete_option()` to remove all stored settings when the user deletes the plugin.

## 3. Performance Optimization (Conditional Loading)
*   **Why:** The Google CSE script and CSS are currently loaded on *every* page.
*   **Plan:**
    *   Modify `enqueue_google_script` and `enqueue_styles` to check if the current page contains the search shortcode, is the search results page, or if the search form is active before loading assets.

## 4. Modernization (Gutenberg Blocks)
*   **Why:** Shortcodes are becoming legacy. Blocks provide a better editing experience.
*   **Plan:**
    *   Create a "GPSE Search Form" block and a "GPSE Search Results" block.
    *   Register these blocks in JavaScript (using `@wordpress/scripts`) and PHP to replace or supplement the shortcodes.

## 5. Automated Testing
*   **Why:** To ensure stability and prevent regressions.
*   **Plan:**
    *   Set up `phpunit` and a `tests/` directory.
    *   Write tests for key logic: option retrieval, shortcode rendering, and redirection logic.
    *   Add a `phpcs.xml` file to enforce WordPress Coding Standards.
