<?php
/**
 * Plugin Name: Wapuus API
 * Plugin URI: https://glaubersilva.me/
 * Description: A simple plugin that implements the API used by the https://github.com/glaubersilva/wapuus project - Just an app built for study purposes with React as frontend and WordPress as backend.
 * Version: 1.0.0
 * Author: Glauber Silva
 * Author URI: https://glaubersilva.me/
 * License: GPLv2 or later
 * Text Domain: wapuus-api
 * Domain Path: /languages
 *
 * @package Wapuus_API
 */

defined( 'ABSPATH' ) || exit;

/**
 * Constants
 */
define( 'WAPUUS_API_DIR', dirname( __FILE__ ) );
define( 'WAPUUS_API_TEXT_DOMAIN', 'wapuus-api' );

/**
 * Initial Setup
 */
require_once WAPUUS_API_DIR . '/autoload.php';
\Wapuus_API\Src\Classes\General_Tweaks::get_instance();
\Wapuus_API\Src\Classes\Wapuus_Custom_Post_Type::get_instance();

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */
require_once WAPUUS_API_DIR . '/legacy/load-endpoints-v1.php';

/**
 * API V2 classes are loaded by this class.
 */
new \Wapuus_API\Src\Classes\Load_Endpoints_V2();





/**
 * Register an endpoint with the WordPress REST API.
 *
 * @param \Wapuus_API\Src\Interfaces\Endpoint $endpoint  Accept an implementation class of the "Endpoint" interface.
 */
/*function myplugin_register_endpoint( \Wapuus_API\Src\Interfaces\Endpoint $endpoint ) {
	register_rest_route(
		'wapuus-api/v2',
		$endpoint->get_path(),
		array(
			'args'                => $endpoint->get_arguments(),
			'callback'            => $endpoint->get_callback(),
			'methods'             => $endpoint->get_methods(),
			'permission_callback' => $endpoint->get_permission_callback(),
		)
	);
}*/


/**
 * Register all our endpoints with the WordPress REST API.
 */
/*function myplugin_register_endpoints() {

	$endpoints = apply_filters(
		'myplugin_endpoints',
		array(
			new \Wapuus_API\Src\Classes\Endpoints\Stats_Get(),
		)
	);

	foreach ( $endpoints as $endpoint ) {
		myplugin_register_endpoint( $endpoint );
	}
}
add_action( 'rest_api_init', 'myplugin_register_endpoints' );*/

