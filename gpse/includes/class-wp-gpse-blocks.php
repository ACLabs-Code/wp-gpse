<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_GPSE_Blocks {

	/**
	 * Initialize the blocks.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Register Gutenberg blocks.
	 */
	public function register_blocks() {
		// Search Form Block
		register_block_type( 
			GPSE_PATH . 'build/search-form', 
			array(
				'render_callback' => array( $this, 'render_search_form_block' ),
			) 
		);

		// Search Results Block
		register_block_type( 
			GPSE_PATH . 'build/search-results', 
			array(
				'render_callback' => array( $this, 'render_search_results_block' ),
			) 
		);
	}

	/**
	 * Render callback for the Search Form block.
	 */
	public function render_search_form_block( $attributes, $content ) {
		// Reuse the logic from the frontend class if possible, 
		// but since we want to avoid tight coupling or redundant code, 
		// we'll just implement the output here.
		$results_page_id = get_option( 'wp_gpse_results_page_id' );
		$results_url     = $results_page_id ? get_permalink( $results_page_id ) : home_url( '/' );
		
		return '<div class="gcse-searchbox-only" data-resultsUrl="' . esc_url( $results_url ) . '" data-queryParameterName="q"></div>';
	}

	/**
	 * Render callback for the Search Results block.
	 */
	public function render_search_results_block( $attributes, $content ) {
		return '<div class="gcse-searchresults-only" data-queryParameterName="q"></div>';
	}
}
