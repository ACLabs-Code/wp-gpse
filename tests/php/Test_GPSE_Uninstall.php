<?php
/**
 * Tests for uninstall.php.
 *
 * The uninstall file is included once in setUpBeforeClass after pre-populating
 * all three options. Each test then asserts a specific option was removed.
 */
class Test_GPSE_Uninstall extends WP_UnitTestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		// Pre-populate all plugin options.
		update_option( 'wp_gpse_cx_id', 'test-cx-123' );
		update_option( 'wp_gpse_results_page_id', 42 );
		update_option( 'wp_gpse_autocomplete_margin', 20 );

		// Define the constant uninstall.php guards against.
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', 'gpse/gpse.php' );
		}

		require dirname( __DIR__, 2 ) . '/gpse/uninstall.php';
	}

	public function test_cx_id_option_deleted() {
		$this->assertFalse( get_option( 'wp_gpse_cx_id' ) );
	}

	public function test_results_page_id_option_deleted() {
		$this->assertFalse( get_option( 'wp_gpse_results_page_id' ) );
	}

	public function test_autocomplete_margin_option_deleted() {
		$this->assertFalse( get_option( 'wp_gpse_autocomplete_margin' ) );
	}
}
