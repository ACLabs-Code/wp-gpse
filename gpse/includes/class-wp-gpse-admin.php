<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GPSE Admin Settings Class
 *
 * Handles all admin-related functionality including settings page,
 * option registration, and admin interface rendering.
 *
 * @since 1.0.0
 */
class WP_GPSE_Admin {

	/**
	 * Initialize admin functionality.
	 *
	 * Hooks into WordPress admin_menu and admin_init actions to register
	 * the settings page and configuration options.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_filter( 'plugin_action_links_' . GPSE_BASENAME, array( $this, 'add_settings_link' ) );
	}

	/**
	 * Add Settings link to plugin action links.
	 *
	 * Adds a "Settings" link to the plugin's entry on the Plugins page,
	 * providing quick access to the configuration page.
	 *
	 * @since 1.0.0
	 * @param array $links Existing plugin action links.
	 * @return array Modified array of plugin action links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=gpse">' . esc_html__( 'Settings', 'gpse' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Add plugin settings page to WordPress admin menu.
	 *
	 * Registers the GPSE settings page under Settings > GPSE in the
	 * WordPress admin menu. Requires 'manage_options' capability.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_plugin_page() {
		add_options_page(
			esc_html__( 'GPSE Settings', 'gpse' ),
			esc_html__( 'GPSE', 'gpse' ),
			'manage_options',
			'gpse',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Render the admin settings page.
	 *
	 * Outputs the HTML for the GPSE settings page, including the form
	 * fields for CX ID, results page selection, and autocomplete margin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function create_admin_page() {
		// Verify user has permission to access this page
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'gpse' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'GPSE Search', 'gpse' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				// settings_fields() handles nonce generation and verification automatically
				settings_fields( 'wp_gpse_option_group' );
				do_settings_sections( 'gpse-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Initialize and register all plugin settings.
	 *
	 * Registers three plugin options (CX ID, results page ID, autocomplete margin)
	 * and creates the settings section with associated form fields.
	 *
	 * @since 1.0.0
	 * @return void
	 */
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

		register_setting(
			'wp_gpse_option_group',
			'wp_gpse_autocomplete_margin',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 15,
			)
		);

		add_settings_section(
			'wp_gpse_setting_section',
			esc_html__( 'Configuration', 'gpse' ),
			array( $this, 'section_info' ),
			'gpse-admin'
		);

		add_settings_field(
			'wp_gpse_cx_id',
			esc_html__( 'Search Engine ID (CX)', 'gpse' ),
			array( $this, 'cx_id_callback' ),
			'gpse-admin',
			'wp_gpse_setting_section'
		);

		add_settings_field(
			'wp_gpse_results_page_id',
			esc_html__( 'Search Results Page', 'gpse' ),
			array( $this, 'results_page_callback' ),
			'gpse-admin',
			'wp_gpse_setting_section'
		);

		add_settings_field(
			'wp_gpse_autocomplete_margin',
			esc_html__( 'Autocomplete Top Margin (px)', 'gpse' ),
			array( $this, 'autocomplete_margin_callback' ),
			'gpse-admin',
			'wp_gpse_setting_section'
		);
	}

	/**
	 * Display settings section description.
	 *
	 * Outputs introductory text for the GPSE configuration section,
	 * displayed above the settings fields.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function section_info() {
		echo esc_html__( 'Enter your Google Programmable Search Engine details below.', 'gpse' );
	}

	/**
	 * Render CX ID input field.
	 *
	 * Displays the text input field for entering the Google Programmable
	 * Search Engine ID (CX). This ID is required for the search to function.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function cx_id_callback() {
		$cx_id = get_option( 'wp_gpse_cx_id' );
		?>
		<input type="text" name="wp_gpse_cx_id" value="<?php echo esc_attr( $cx_id ); ?>" class="regular-text" />
		<p class="description"><?php echo esc_html__( 'You can find this in your Google Programmable Search Engine control panel.', 'gpse' ); ?></p>
		<?php
	}

	/**
	 * Render results page dropdown field.
	 *
	 * Displays a dropdown of WordPress pages where users can select the page
	 * that will display search results. Native WordPress searches will redirect
	 * to this page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
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
			'show_option_none'      => __( 'Select a page', 'gpse' ),
			'option_none_value'     => 0,
		);

		echo wp_kses(
			wp_dropdown_pages( $args ),
			array(
				'select' => array(
					'name'  => array(),
					'id'    => array(),
					'class' => array(),
				),
				'option' => array(
					'value'    => array(),
					'selected' => array(),
					'class'    => array(),
				),
			)
		);
		?>
		<p class="description">
			<?php
			echo wp_kses(
				sprintf(
					/* translators: %s: shortcode tag [gpse_results] */
					__( 'Select the page where you have placed the %s shortcode. Native searches will be redirected here.', 'gpse' ),
					'<code>[gpse_results]</code>'
				),
				array( 'code' => array() )
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render autocomplete margin input field.
	 *
	 * Displays a number input for adjusting the top margin of Google's
	 * autocomplete dropdown. Useful when the dropdown overlaps with the
	 * search input field due to theme styling conflicts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function autocomplete_margin_callback() {
		$margin = get_option( 'wp_gpse_autocomplete_margin', 15 );
		?>
		<input type="number" name="wp_gpse_autocomplete_margin" value="<?php echo esc_attr( $margin ); ?>" class="small-text" /> <?php echo esc_html_x( 'px', 'pixels unit abbreviation', 'gpse' ); ?>
		<p class="description"><?php echo esc_html__( 'Adjust this if the autocomplete dropdown covers your search input. Default is 15.', 'gpse' ); ?></p>
		<?php
	}
}
