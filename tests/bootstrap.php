<?php
/**
 * PHPUnit bootstrap file.
 *
 * Uses wp-tests-config.php for environment (DB, ABSPATH) and WordPress
 * test suite from vendor (Composer).
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

require dirname( __DIR__ ) . '/vendor/autoload.php';

$wp_tests_config = __DIR__ . '/wp-tests-config.php';

if ( ! file_exists( $wp_tests_config ) ) {
	echo 'wp-tests-config.php not found. Copy tests/wp-tests-config.dist.php to tests/wp-tests-config.php and set your DB credentials.' . PHP_EOL;
	exit( 1 );
}

define( 'WP_TESTS_CONFIG_FILE_PATH', $wp_tests_config );

// Ensure wp-content/uploads exists and is writable (vendor WordPress does not include it; WP_UnitTestCase and media_handle_sideload need it).
$wp_uploads_dir = dirname( __DIR__ ) . '/vendor/wordpress/wordpress/src/wp-content/uploads';
if ( ! is_dir( $wp_uploads_dir ) ) {
	mkdir( $wp_uploads_dir, 0755, true );
}
// Create year/month subdir so wp_upload_dir() and media_handle_sideload() can write (same as bash install).
$wp_uploads_subdir = $wp_uploads_dir . '/' . gmdate( 'Y' ) . '/' . gmdate( 'm' );
if ( ! is_dir( $wp_uploads_subdir ) ) {
	mkdir( $wp_uploads_subdir, 0755, true );
}

// Pre-register plugin load (WordPress test bootstrap will run muplugins_loaded later).
if ( ! isset( $GLOBALS['wp_filter'] ) ) {
	$GLOBALS['wp_filter'] = array();
}
$plugin_loader = function () {
	require dirname( __DIR__ ) . '/wapuus-api.php';
};
$GLOBALS['wp_filter']['muplugins_loaded'][10]['wapuus_api_plugin'] = array(
	'function'      => $plugin_loader,
	'accepted_args' => 1,
);

require dirname( __DIR__ ) . '/vendor/wordpress/wordpress/tests/phpunit/includes/bootstrap.php';

// Base test case classes (must be loaded before test files under legacy/ and Core/Endpoints/).
require_once __DIR__ . '/class-unit-test-case.php';
require_once __DIR__ . '/class-unit-api-test-case.php';
