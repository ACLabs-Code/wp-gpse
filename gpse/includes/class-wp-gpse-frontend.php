<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GPSE Frontend Class
 *
 * Handles all frontend functionality including shortcodes, search redirection,
 * form replacement, and Google CSE script/style enqueuing.
 *
 * @since 1.0.0
 */
class WP_GPSE_Frontend {

	/**
	 * Initialize frontend functionality.
	 *
	 * Registers shortcodes, hooks for search redirection, form filtering,
	 * and asset enqueuing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		add_shortcode( 'gpse_form', array( $this, 'render_search_form_shortcode' ) );
		add_shortcode( 'gpse_results', array( $this, 'render_search_results_shortcode' ) );

		add_action( 'template_redirect', array( $this, 'redirect_native_search' ) );
		add_filter( 'get_search_form', array( $this, 'filter_search_form' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_google_script' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
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
		wp_enqueue_style( 'gpse-style', GPSE_URL . 'assets/css/gpse.css', array(), GPSE_VERSION );

		$margin = get_option( 'wp_gpse_autocomplete_margin', 15 );
		if ( $margin !== 15 && $margin !== '' ) {
			$custom_css = ".gssb_c { margin-top: {$margin}px !important; } .gsc-completion-container { margin-top: {$margin}px !important; }";
			wp_add_inline_style( 'gpse-style', $custom_css );
		}
	}

	/**
	 * Enqueue the Google CSE script.
	 *
	 * Loads the Google Programmable Search Engine JavaScript file with the
	 * configured CX ID. Uses async loading strategy for better performance.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_google_script() {
		$cx_id = get_option( 'wp_gpse_cx_id' );
		if ( ! empty( $cx_id ) ) {
			wp_enqueue_script( 
				'google-cse', 
				'https://cse.google.com/cse.js?cx=' . esc_attr( $cx_id ), 
				array(), 
				GPSE_VERSION, 
				array( 'strategy' => 'async' ) 
			);
		}
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
	 * Render search form shortcode.
	 *
	 * Outputs the Google CSE search box div element configured for "Two Page"
	 * mode, pointing to the configured results page.
	 *
	 * Usage: [gpse_form]
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes (currently unused).
	 * @return string HTML markup for Google CSE search box.
	 */
	public function render_search_form_shortcode( $atts ) {
		return WP_GPSE_Helpers::get_search_form_html();
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
	 * Filter and replace the native WordPress search form.
	 *
	 * Intercepts calls to get_search_form() and replaces the theme's search
	 * form with the Google CSE search box.
	 *
	 * @since 1.0.0
	 * @param string $form The default search form HTML.
	 * @return string Replaced HTML markup for Google CSE search box.
	 */
	public function filter_search_form( $form ) {
		// We want to return the Google Search Box form instead of the theme's form.
		// However, get_search_form is often called within PHP tags, so we return string.
		// We'll reuse the shortcode logic.
		return $this->render_search_form_shortcode( array() );
	}
}
