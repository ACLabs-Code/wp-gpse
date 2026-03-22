<?php
/**
 * PHPUnit bootstrap for GPSE Search plugin tests.
 *
 * Loads the WordPress test library, then loads the plugin via
 * the muplugins_loaded hook so all plugin classes are available
 * to every test case.
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( "$_tests_dir/includes/functions.php" ) ) {
	echo "Could not find WordPress test library at: $_tests_dir\n";
	echo "Set the WP_TESTS_DIR environment variable or start wp-env and run:\n";
	echo "  npm run test:php\n";
	exit( 1 );
}

// Required by the WP test bootstrap for PHPUnit 9+ compatibility.
define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );

require_once "$_tests_dir/includes/functions.php";

/**
 * Load the plugin before the test suite runs.
 */
function _gpse_load_plugin() {
	require dirname( __DIR__ ) . '/gpse/gpse.php';
}
tests_add_filter( 'muplugins_loaded', '_gpse_load_plugin' );

require "$_tests_dir/includes/bootstrap.php";
