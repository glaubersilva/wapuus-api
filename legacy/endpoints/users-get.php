<?php
/**
 * The legacy API V1 endpoint for "user get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "user get" endpoint.
 */
function wappus_api_register_user_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/users',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Users_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::READABLE,
				'args'                => wappus_api_user_get_args(),
				'permission_callback' => 'wappus_api_user_get_permissions_check',
				'callback'            => 'wappus_api_user_get',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);

}
add_action( 'rest_api_init', 'wappus_api_register_user_get' );

/**
 * Schema of the expected arguments for the "user get" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wappus_api_user_get_args() {

	$args = array();

	return $args;
}

/**
 * Permission callback for the "user get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wappus_api_user_get_permissions_check( $request ) {

	if ( ! is_user_logged_in() ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'User does not have permission.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Callback for the "user get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wappus_api_user_get( $request ) {

	$user = wp_get_current_user();

	$user = array(
		'id'       => $user->ID,
		'username' => $user->user_login,
		'email'    => $user->user_email,
	);

	$response = new \Wapuus_API\Src\Classes\Responses\Valid\OK( $user );

	return rest_ensure_response( $response );
}
