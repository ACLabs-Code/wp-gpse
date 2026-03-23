<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GPSE Frontend Class
 *
 * Handles all frontend functionality including shortcodes, search redirection,
 * and Google CSE script/style enqueuing.
 *
 * @since 1.0.0
 */
class WP_GPSE_Frontend {

	/**
	 * Initialize frontend functionality.
	 *
	 * Registers shortcodes, hooks for search redirection, and asset enqueuing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		add_shortcode( 'gpse_results', array( $this, 'render_search_results_shortcode' ) );

		add_action( 'template_redirect', array( $this, 'redirect_native_search' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_google_script' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_init_script' ) );

		// Populate search forms with query parameter value
		add_filter( 'get_search_form', array( $this, 'populate_search_form_value' ) );
		add_filter( 'render_block', array( $this, 'populate_search_block_value' ), 10, 2 );
	}

	/**
	 * Enqueue plugin styles.
	 *
	 * Loads the main GPSE stylesheet and optionally adds inline CSS for
	 * custom autocomplete margin adjustment if configured.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		$results_page_id = get_option( 'wp_gpse_results_page_id' );
		if ( empty( $results_page_id ) || ! is_page( $results_page_id ) ) {
			return;
		}

		wp_enqueue_style( 'gpse-style', GPSE_URL . 'assets/css/gpse.css', array(), GPSE_VERSION );

		// Get margin value and ensure it's a positive integer (defense in depth)
		$margin = absint( get_option( 'wp_gpse_autocomplete_margin', 15 ) );
		if ( $margin !== 15 && $margin !== 0 ) {
			// Safe to use in CSS as $margin is guaranteed to be a positive integer
			$custom_css = ".gssb_c { margin-top: {$margin}px !important; } .gsc-completion-container { margin-top: {$margin}px !important; }";
			wp_add_inline_style( 'gpse-style', $custom_css );
		}
	}

	/**
	 * Enqueue the Google CSE script.
	 *
	 * Loads the Google Programmable Search Engine JavaScript file with the
	 * configured CX ID. Uses defer loading strategy to ensure DOM is ready
	 * before script execution, improving mobile initialization reliability.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_google_script() {
		$results_page_id = get_option( 'wp_gpse_results_page_id' );
		if ( empty( $results_page_id ) || ! is_page( $results_page_id ) ) {
			return;
		}

		$cx_id = get_option( 'wp_gpse_cx_id' );
		if ( ! empty( $cx_id ) ) {
			wp_enqueue_script(
				'google-cse',
				'https://cse.google.com/cse.js?cx=' . esc_attr( $cx_id ),
				array(),
				GPSE_VERSION,
				array( 'strategy' => 'defer', 'in_footer' => true )
			);
		}
	}

	/**
	 * Enqueue initialization detection script.
	 *
	 * Loads a script that detects if Google CSE initializes successfully
	 * and provides fallback error messaging if it fails. Particularly useful
	 * for debugging mobile initialization issues.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	public function enqueue_init_script() {
		$results_page_id = get_option( 'wp_gpse_results_page_id' );
		if ( empty( $results_page_id ) || ! is_page( $results_page_id ) ) {
			return;
		}

		wp_enqueue_script(
			'gpse-init',
			GPSE_URL . 'assets/js/gpse-init.js',
			array(),
			GPSE_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}

	/**
	 * Redirect native WordPress search to custom results page.
	 *
	 * Intercepts native WordPress search queries (?s=query) and redirects
	 * them to the configured GPSE results page with the query parameter 'q'.
	 * Only runs on frontend search pages when a results page is configured.
	 *
	 * @since 1.0.0
	 * @return void
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
					wp_safe_redirect( $url );
					exit;
				}
			}
		}
	}

	/**
	 * Render search results shortcode.
	 *
	 * Outputs the Google CSE search results div element configured to display
	 * results based on the 'q' query parameter.
	 *
	 * Usage: [gpse_results]
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes (currently unused).
	 * @return string HTML markup for Google CSE search results.
	 */
	public function render_search_results_shortcode( $atts ) {
		return WP_GPSE_Helpers::get_search_results_html();
	}

	/**
	 * Populate search form value from query parameter.
	 *
	 * Filters the HTML output of search forms to pre-populate the search input
	 * with the value from the 'q' URL parameter. This ensures that search forms
	 * on the results page display the user's search term.
	 *
	 * Applies to traditional search forms generated by get_search_form() including
	 * theme search forms and search widgets.
	 *
	 * @since 1.2.2
	 * @param string $form The search form HTML.
	 * @return string Modified search form HTML with populated value.
	 */
	public function populate_search_form_value( $form ) {
		// Get and sanitize the search query from URL
		$query = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

		if ( empty( $query ) ) {
			return $form; // No query to populate
		}

		// Escape for use in HTML attribute
		$escaped_query = esc_attr( $query );

		// First, remove any existing value attribute to avoid duplication
		$form = preg_replace(
			'/(<input[^>]*)(value=["\'][^"\']*["\'])([^>]*>)/i',
			'$1$3',
			$form
		);

		// Now add the value attribute to search inputs
		// Matches: <input type="search" ...> or <input ... name="s" ...>
		$form = preg_replace(
			'/(<input[^>]*(?:type=["\']search["\']|name=["\']s["\'])[^>]*?)(>)/i',
			'$1 value="' . $escaped_query . '"$2',
			$form
		);

		return $form;
	}

	/**
	 * Populate WordPress Search block value from query parameter.
	 *
	 * Filters the output of WordPress Search blocks to pre-populate the search
	 * input with the value from the 'q' URL parameter. This ensures that Search
	 * blocks on the results page display the user's search term.
	 *
	 * @since 1.2.2
	 * @param string $block_content The block content.
	 * @param array  $block The block data.
	 * @return string Modified block content with populated value.
	 */
	public function populate_search_block_value( $block_content, $block ) {
		// Only process Search blocks
		if ( 'core/search' !== $block['blockName'] ) {
			return $block_content;
		}

		// Get and sanitize the search query from URL
		$query = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

		if ( empty( $query ) ) {
			return $block_content; // No query to populate
		}

		// Escape for use in HTML attribute
		$escaped_query = esc_attr( $query );

		// First, remove any existing value attribute to avoid duplication
		$block_content = preg_replace(
			'/(<input[^>]*)(value=["\'][^"\']*["\'])([^>]*>)/i',
			'$1$3',
			$block_content
		);

		// Now add the value attribute to search inputs
		$block_content = preg_replace(
			'/(<input[^>]*type=["\']search["\'][^>]*?)(>)/i',
			'$1 value="' . $escaped_query . '"$2',
			$block_content
		);

		return $block_content;
	}
}
