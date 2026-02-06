=== GPSE Search ===
Contributors: gemini
Tags: search, google, gcse, programmable search, cse
Requires at least: 6.4
Tested up to: 6.9.1
Stable tag: 1.2.2
Requires PHP: 8.2
License: AGPL v3 or later
License URI: https://www.gnu.org/licenses/agpl-3.0.html

Redirects WordPress searches to display Google Programmable Search Engine (GCSE) results.

== Description ==

Redirects WordPress searches to display Google Programmable Search Engine (GCSE) results. Works seamlessly with the standard WordPress Search block or your theme's search form.

=== Features ===

*   **Seamless Integration**: Works with the standard WordPress Search block or your theme's search form.
*   **Search Results Page**: Redirects all searches to a dedicated page of your choice.
*   **Configurable**: Easy settings to add your Search Engine ID (CX) and select the results page.
*   **Google-Powered Results**: Display Google CSE results using the GPSE Search Results block or `[gpse_results]` shortcode.

=== How it Works ===

*   **Search Box**: Use the standard WordPress Search block or your theme's search form to allow users to search your site.
*   **Redirection**: When a user searches (e.g., `/?s=myquery`), they are automatically redirected to your custom Results Page (e.g., `/search-results/?q=myquery`).
*   **Google CSE Results**: The Results Page displays Google-powered search results using your configured Google Programmable Search Engine.

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
4.  **Add Search Form**:
    *   Add the standard WordPress **Search** block to your site (header, sidebar, page, etc.).
    *   Or use your theme's built-in search form.

== Changelog ==

= 1.2.2 =
* Fixed blank search box on results page - search forms now display the search term.
* Added PHP filters to populate search input values from query parameter.
* Improved mobile performance by using server-side rendering instead of JavaScript.

= 1.2.0 =
* Removed custom GPSE Search Form block - use standard WordPress Search block instead.
* Removed [gpse_form] shortcode - use core WordPress search functionality.
* Removed automatic search form replacement filter.
* Simplified plugin to focus on search redirection and results display.
* Preserved redirect functionality - standard WP searches still use Google CSE results.

= 1.1.0 =
* Added Gutenberg blocks for Search Form and Search Results.
* Modernized build process with @wordpress/scripts.

= 1.0.0 =
* Initial release.
