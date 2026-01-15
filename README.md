=== GPSE Search ===
Contributors: gemini
Tags: search, google, gcse, programmable search, cse
Requires at least: 6.4
Tested up to: 6.9
Stable tag: 1.2.0
Requires PHP: 8.4
License: AGPL v3 or later
License URI: https://www.gnu.org/licenses/agpl-3.0.html

Redirects WordPress searches to display Google Programmable Search Engine (GCSE) results.

## Features

-   **Seamless Integration**: Works with the standard WordPress Search block or your theme's search form.
-   **Search Results Page**: Redirects all searches to a dedicated page of your choice.
-   **Configurable**: Easy settings to add your Search Engine ID (CX) and select the results page.
-   **Google-Powered Results**: Display Google CSE results using the GPSE Search Results block or `[gpse_results]` shortcode.

## Installation & Setup

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

## How it Works

*   **Search Box**: Use the standard WordPress Search block or your theme's search form to allow users to search your site.
*   **Redirection**: When a user searches (e.g., `/?s=myquery`), they are automatically redirected to your custom Results Page (e.g., `/search-results/?q=myquery`).
*   **Google CSE Results**: The Results Page displays Google-powered search results using your configured Google Programmable Search Engine.
