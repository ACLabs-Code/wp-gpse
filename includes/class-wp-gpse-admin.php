<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_GPSE_Admin {

	public function init() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_filter( 'plugin_action_links_' . WP_GPSE_BASENAME, array( $this, 'add_settings_link' ) );
	}

	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=wp-gpse">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function add_plugin_page() {
		add_options_page(
			'WP GPSE Settings',
			'WP GPSE',
			'manage_options',
			'wp-gpse',
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1>WP Google Programmable Search Engine</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wp_gpse_option_group' );
				do_settings_sections( 'wp-gpse-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function page_init() {
		register_setting(
			'wp_gpse_option_group',
			'wp_gpse_cx_id',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			'wp_gpse_option_group',
			'wp_gpse_results_page_id',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);

		add_settings_section(
			'wp_gpse_setting_section',
			'Configuration',
			array( $this, 'section_info' ),
			'wp-gpse-admin'
		);

		add_settings_field(
			'wp_gpse_cx_id',
			'Search Engine ID (CX)',
			array( $this, 'cx_id_callback' ),
			'wp-gpse-admin',
			'wp_gpse_setting_section'
		);

		add_settings_field(
			'wp_gpse_results_page_id',
			'Search Results Page',
			array( $this, 'results_page_callback' ),
			'wp-gpse-admin',
			'wp_gpse_setting_section'
		);
	}

	public function section_info() {
		echo 'Enter your Google Programmable Search Engine details below.';
	}

	public function cx_id_callback() {
		$cx_id = get_option( 'wp_gpse_cx_id' );
		?>
		<input type="text" name="wp_gpse_cx_id" value="<?php echo esc_attr( $cx_id ); ?>" class="regular-text" />
		<p class="description">You can find this in your Google Programmable Search Engine control panel.</p>
		<?php
	}

	public function results_page_callback() {
		$selected_page = get_option( 'wp_gpse_results_page_id' );
		
		$args = array(
			'depth'                 => 0,
			'child_of'              => 0,
			'selected'              => $selected_page,
			'echo'                  => 0,
			'name'                  => 'wp_gpse_results_page_id',
			'id'                    => 'wp_gpse_results_page_id',
			'class'                 => 'regular-text',
			'show_option_none'      => 'Select a page',
			'option_none_value'     => 0,
		);
		
		echo wp_dropdown_pages( $args );
		?>
		<p class="description">Select the page where you have placed the <code>[gpse_results]</code> shortcode. Native searches will be redirected here.</p>
		<?php
	}
}
