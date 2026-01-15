<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GPSE Helper Functions Class
 *
 * Provides shared utility methods for generating Google CSE HTML markup.
 * Used by both shortcodes and Gutenberg blocks to avoid code duplication.
 *
 * @since 1.1.0
 */
class WP_GPSE_Helpers {

	/**
	 * Generate HTML for Google CSE search results.
	 *
	 * Creates the div element for Google's searchresults-only component,
	 * configured to display results based on the 'q' query parameter.
	 *
	 * @since 1.1.0
	 * @return string HTML markup for Google CSE search results.
	 */
	public static function get_search_results_html() {
		return '<div class="gcse-searchresults-only" data-queryParameterName="q"></div>';
	}
}
