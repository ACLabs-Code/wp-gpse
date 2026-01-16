<?php
/**
 * Plugin Name: GPSE Search
 * Description: Redirects WordPress searches to display Google Programmable Search Engine (GCSE) results.
 * Version: 1.2.2
 * Author: Gemini
 * Text Domain: gpse
 * License: AGPL v3 or later
 * License URI: https://www.gnu.org/licenses/agpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GPSE_VERSION', '1.2.2' );
define( 'GPSE_PATH', plugin_dir_path( __FILE__ ) );
define( 'GPSE_URL', plugin_dir_url( __FILE__ ) );
define( 'GPSE_BASENAME', plugin_basename( __FILE__ ) );

require_once GPSE_PATH . 'includes/class-wp-gpse-helpers.php';
require_once GPSE_PATH . 'includes/class-wp-gpse-admin.php';
require_once GPSE_PATH . 'includes/class-wp-gpse-frontend.php';
require_once GPSE_PATH . 'includes/class-wp-gpse-blocks.php';

function gpse_init() {
	// Note: load_plugin_textdomain() is not needed since WordPress 4.6+
	// WordPress automatically loads translations for plugins based on the Text Domain header

	$plugin_admin = new WP_GPSE_Admin();
	$plugin_admin->init();

	$plugin_frontend = new WP_GPSE_Frontend();
	$plugin_frontend->init();

	$plugin_blocks = new WP_GPSE_Blocks();
	$plugin_blocks->init();
}
add_action( 'plugins_loaded', 'gpse_init' );
