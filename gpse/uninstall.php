<?php
/**
 * GPSE Search Uninstall Handler
 *
 * Fired when the plugin is uninstalled (deleted, not just deactivated).
 * Removes all plugin data from the WordPress database.
 *
 * @package GPSE_Search
 * @since 1.1.0
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options
delete_option( 'wp_gpse_cx_id' );
delete_option( 'wp_gpse_results_page_id' );
delete_option( 'wp_gpse_autocomplete_margin' );

// For multisite installations, delete options from all sites
if ( is_multisite() ) {
	global $wpdb;

	// Get all blog IDs
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );

		// Delete options for this site
		delete_option( 'wp_gpse_cx_id' );
		delete_option( 'wp_gpse_results_page_id' );
		delete_option( 'wp_gpse_autocomplete_margin' );

		restore_current_blog();
	}
}
