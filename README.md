# Google Programmable Search Engine (GPSE)

A WordPress plugin that replaces the native search with Google Programmable Search Engine (formerly Custom Search Engine).

## Features

-   **Seamless Integration**: Replaces the standard WordPress search form automatically.
-   **Search Results Page**: Redirects all searches to a dedicated page of your choice.
-   **Configurable**: Easy settings to add your Search Engine ID (CX) and select the results page.
-   **Shortcodes**: Use `[gpse_form]` and `[gpse_results]` to place the search box and results anywhere.

## Installation & Setup

1.  **Install the Plugin**: Upload the `wp-gpse` folder to your `/wp-content/plugins/` directory and activate it.
2.  **Create a Results Page**:
    *   Create a new Page in WordPress (e.g., "Search Results").
    *   Add the shortcode `[gpse_results]` to the page content.
    *   Publish the page.
3.  **Configure**:
    *   Go to **Settings > GPSE**.
    *   Enter your **Google Search Engine ID (CX)**. (Get this from [programmablesearchengine.google.com](https://programmablesearchengine.google.com/)).
    *   Select your "Search Results" page from the dropdown.
    *   Save Changes.

## How it Works

*   **Search Box**: The plugin automatically filters `get_search_form()` to display the Google Search Box.
*   **Redirection**: If a user tries to use the default WordPress search URL (e.g., `/?s=myquery`), they are automatically redirected to your custom Results Page (e.g., `/search-results/?q=myquery`).
*   **Styles**: The search interface inherits styles from your Google Programmable Search Engine configuration (Overlay, Two Page, etc.). Ensure you select "Two Page" or "Results Only" layout in Google's control panel for the best experience with the "Results Page" setup.
