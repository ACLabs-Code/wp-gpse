<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GPSE Gutenberg Blocks Class
 *
 * Handles registration and rendering of Gutenberg blocks for search form
 * and search results display.
 *
 * @since 1.1.0
 */
class WP_GPSE_Blocks {

	/**
	 * Initialize Gutenberg blocks functionality.
	 *
	 * Hooks into WordPress 'init' action to register the GPSE blocks
	 * for use in the block editor.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Register Gutenberg blocks.
	 *
	 * Registers two blocks using Block API v3:
	 * - gpse/search-form: Google CSE search input box
	 * - gpse/search-results: Google CSE search results display
	 *
	 * Both blocks use server-side rendering with dynamic callbacks.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function register_blocks() {
		// Search Results Block
		register_block_type(
			GPSE_PATH . 'build/search-results',
			array(
				'render_callback' => array( $this, 'render_search_results_block' ),
			)
		);
	}

	/**
	 * Render callback for the Search Results block.
	 *
	 * Outputs the Google CSE search results div element configured to display
	 * results based on the 'q' query parameter. This is the server-side
	 * rendering callback for the gpse/search-results block.
	 *
	 * @since 1.1.0
	 * @param array  $attributes Block attributes from the editor.
	 * @param string $content    Block content (unused for dynamic blocks).
	 * @return string HTML markup for Google CSE search results.
	 */
	public function render_search_results_block( $attributes, $content ) {
		return WP_GPSE_Helpers::get_search_results_html();
	}
}
