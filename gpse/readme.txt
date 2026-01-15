=== GPSE Search ===
Contributors: gemini
Tags: search, google, gcse, programmable search, cse
Requires at least: 6.4
Tested up to: 6.9
Stable tag: 1.1.0
Requires PHP: 8.0
License: AGPL v3 or later
License URI: https://www.gnu.org/licenses/agpl-3.0.html

Replaces the standard WordPress search with a Google Programmable Search Engine (GCSE).

== Description ==

Replaces the standard WordPress search with a Google Programmable Search Engine (GCSE).

=== Features ===

*   **Seamless Integration**: Replaces the standard WordPress search form automatically.
*   **Search Results Page**: Redirects all searches to a dedicated page of your choice.
*   **Configurable**: Easy settings to add your Search Engine ID (CX) and select the results page.
*   **Blocks & Shortcodes**: Use Gutenberg blocks or shortcodes (`[gpse_form]` and `[gpse_results]`) to place the search box and results anywhere.

=== How it Works ===

*   **Search Box**: The plugin automatically filters `get_search_form()` to display the Google Search Box.
*   **Redirection**: If a user tries to use the default WordPress search URL (e.g., `/?s=myquery`), they are automatically redirected to your custom Results Page (e.g., `/search-results/?q=myquery`).
*   **Styles**: The search interface inherits styles from your Google Programmable Search Engine configuration (Overlay, Two Page, etc.). Ensure you select "Two Page" or "Results Only" layout in Google's control panel for the best experience with the "Results Page" setup.

== Installation ==

1.  **Install the Plugin**: Upload the `gpse` folder to your `/wp-content/plugins/` directory and activate it.
2.  **Create a Results Page**:
    *   Create a new Page in WordPress (e.g., "Search Results").
    *   Add the **GPSE Search Results** block (or the `[gpse_results]` shortcode) to the page content.
    *   Publish the page.
3.  **Configure**:
    *   Go to **Settings > GPSE**.
    *   Enter your **Google Search Engine ID (CX)**. (Get this from [programmablesearchengine.google.com](https://programmablesearchengine.google.com/)).
    *   Select your "Search Results" page from the dropdown.
    *   Save Changes.

== Changelog ==

= 1.1.0 =
* Added Gutenberg blocks for Search Form and Search Results.
* Modernized build process with @wordpress/scripts.

= 1.0.0 =
* Initial release.
