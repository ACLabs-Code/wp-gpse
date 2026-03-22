<?php
/**
 * Tests for WP_GPSE_Helpers.
 */
class Test_GPSE_Helpers extends WP_UnitTestCase {

	public function test_returns_string() {
		$this->assertIsString( WP_GPSE_Helpers::get_search_results_html() );
	}

	public function test_contains_gcse_class() {
		$html = WP_GPSE_Helpers::get_search_results_html();
		$this->assertStringContainsString( 'gcse-searchresults-only', $html );
	}

	public function test_contains_query_parameter_name() {
		$html = WP_GPSE_Helpers::get_search_results_html();
		$this->assertStringContainsString( 'data-queryParameterName="q"', $html );
	}

	public function test_contains_min_height() {
		$html = WP_GPSE_Helpers::get_search_results_html();
		$this->assertStringContainsString( 'min-height: 300px', $html );
	}

	public function test_contains_loading_indicator() {
		$html = WP_GPSE_Helpers::get_search_results_html();
		$this->assertStringContainsString( 'gpse-loading-indicator', $html );
	}
}
