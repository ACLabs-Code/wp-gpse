<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_GPSE_Frontend {

	public function init() {
		add_shortcode( 'gpse_form', array( $this, 'render_search_form_shortcode' ) );
		add_shortcode( 'gpse_results', array( $this, 'render_search_results_shortcode' ) );
		
		add_action( 'template_redirect', array( $this, 'redirect_native_search' ) );
		add_filter( 'get_search_form', array( $this, 'filter_search_form' ) );
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_google_script' ) );
	}

	/**
	 * Enqueue the Google CSE script.
	 */
	public function enqueue_google_script() {
		$cx_id = get_option( 'wp_gpse_cx_id' );
		if ( ! empty( $cx_id ) ) {
			// We can't use wp_enqueue_script for this easily because it has query params that might get escaped/handled weirdly, 
			// and it's an async external script. But let's try standard way or print inline.
			// Google recommends: <script async src="https://cse.google.com/cse.js?cx=YOUR_ENGINE_ID"></script>
			
			// We will output it in head or footer.
			add_action( 'wp_head', function() use ( $cx_id ) {
				echo '<script async src="https://cse.google.com/cse.js?cx=' . esc_attr( $cx_id ) . '"></script>';
			} );
		}
	}

	/**
	 * Redirect native WP search (?s=query) to the configured custom page.
	 */
	public function redirect_native_search() {
		if ( is_search() && ! is_admin() ) {
			$results_page_id = get_option( 'wp_gpse_results_page_id' );
			
			if ( ! empty( $results_page_id ) ) {
				$query = get_search_query( false );
				$url   = get_permalink( $results_page_id );
				
				// Append query. Google default is 'q' or 's'. We use 'q' to distinguish/standardize.
				// However, if the Search Box Widget is used, it uses 'q' by default.
				if ( $url ) {
					$url = add_query_arg( 'q', $query, $url );
					wp_redirect( $url );
					exit;
				}
			}
		}
	}

	/**
	 * Shortcode for Search Box: [gpse_form]
	 */
	public function render_search_form_shortcode( $atts ) {
		$results_page_id = get_option( 'wp_gpse_results_page_id' );
		$results_url     = $results_page_id ? get_permalink( $results_page_id ) : home_url( '/' ); // Fallback
		
		// If results page is not set, we might default to standard search behavior, 
		// but the widget requires a target for "Two Page" mode usually.
		// If they use "Overlay", resultsUrl might not be needed.
		// But assuming "Two Page" or "Results Only" flow:
		
		$output = '<div class="gcse-searchbox-only" data-resultsUrl="' . esc_url( $results_url ) . '" data-queryParameterName="q"></div>';
		return $output;
	}

	/**
	 * Shortcode for Search Results: [gpse_results]
	 */
	public function render_search_results_shortcode( $atts ) {
		// Just the results div.
		return '<div class="gcse-searchresults-only" data-queryParameterName="q"></div>';
	}

	/**
	 * Replace the native search form.
	 */
	public function filter_search_form( $form ) {
		// We want to return the Google Search Box form instead of the theme's form.
		// However, get_search_form is often called within PHP tags, so we return string.
		// We'll reuse the shortcode logic.
		return $this->render_search_form_shortcode( array() );
	}
}
