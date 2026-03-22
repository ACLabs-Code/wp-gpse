<?php
/**
 * Tests for WP_GPSE_Admin.
 */
class Test_GPSE_Admin extends WP_UnitTestCase {

	private WP_GPSE_Admin $admin;

	public function setUp(): void {
		parent::setUp();
		$this->admin = new WP_GPSE_Admin();
	}

	// --- add_settings_link ---

	public function test_settings_link_prepended_to_existing_links() {
		$links  = [ '<a href="deactivate">Deactivate</a>' ];
		$result = $this->admin->add_settings_link( $links );
		$this->assertCount( 2, $result );
		$this->assertStringContainsString( 'options-general.php?page=gpse', $result[0] );
	}

	public function test_settings_link_contains_settings_label() {
		$result = $this->admin->add_settings_link( [] );
		$this->assertStringContainsString( 'Settings', $result[0] );
	}

	// --- page_init / register_setting ---

	public function test_all_three_options_registered() {
		$this->admin->page_init();
		global $wp_registered_settings;
		$this->assertArrayHasKey( 'wp_gpse_cx_id', $wp_registered_settings );
		$this->assertArrayHasKey( 'wp_gpse_results_page_id', $wp_registered_settings );
		$this->assertArrayHasKey( 'wp_gpse_autocomplete_margin', $wp_registered_settings );
	}

	public function test_cx_id_registered_as_string_type() {
		$this->admin->page_init();
		global $wp_registered_settings;
		$this->assertSame( 'string', $wp_registered_settings['wp_gpse_cx_id']['type'] );
	}

	public function test_results_page_id_registered_as_integer_type() {
		$this->admin->page_init();
		global $wp_registered_settings;
		$this->assertSame( 'integer', $wp_registered_settings['wp_gpse_results_page_id']['type'] );
	}

	public function test_autocomplete_margin_default_is_15() {
		$this->admin->page_init();
		global $wp_registered_settings;
		$this->assertSame( 15, $wp_registered_settings['wp_gpse_autocomplete_margin']['default'] );
	}

	public function test_cx_id_sanitize_callback_is_sanitize_text_field() {
		$this->admin->page_init();
		global $wp_registered_settings;
		$this->assertSame(
			'sanitize_text_field',
			$wp_registered_settings['wp_gpse_cx_id']['sanitize_callback']
		);
	}

	public function test_results_page_id_sanitize_callback_is_absint() {
		$this->admin->page_init();
		global $wp_registered_settings;
		$this->assertSame(
			'absint',
			$wp_registered_settings['wp_gpse_results_page_id']['sanitize_callback']
		);
	}
}
