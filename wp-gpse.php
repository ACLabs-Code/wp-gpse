<?php
/**
 * Plugin Name: WP Google Programmable Search Engine
 * Description: Replaces the standard WordPress search with a Google Programmable Search Engine (GCSE).
 * Version: 1.0.0
 * Author: Gemini
 * Text Domain: wp-gpse
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_GPSE_VERSION', '1.0.0' );
define( 'WP_GPSE_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_GPSE_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_GPSE_BASENAME', plugin_basename( __FILE__ ) );

require_once WP_GPSE_PATH . 'includes/class-wp-gpse-admin.php';
require_once WP_GPSE_PATH . 'includes/class-wp-gpse-frontend.php';

function wp_gpse_init() {
	$plugin_admin = new WP_GPSE_Admin();
	$plugin_admin->init();

	$plugin_frontend = new WP_GPSE_Frontend();
	$plugin_frontend->init();
}
add_action( 'plugins_loaded', 'wp_gpse_init' );
