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
	 * Generate HTML for Google CSE search form.
	 *
	 * Creates the div element for Google's searchbox-only component,
	 * configured for "Two Page" mode with the results URL pointing to
	 * the configured WordPress page.
	 *
	 * @since 1.1.0
	 * @return string HTML markup for Google CSE search box.
	 */
	public static function get_search_form_html() {
		$results_page_id = get_option( 'wp_gpse_results_page_id' );
		$results_url     = $results_page_id ? get_permalink( $results_page_id ) : home_url( '/' );

		return '<div class="gcse-searchbox-only" data-resultsUrl="' . esc_url( $results_url ) . '" data-queryParameterName="q"></div>';
	}

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
