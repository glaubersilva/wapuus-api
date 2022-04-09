<?php
/**
 * The legacy API V1 endpoints for "password reset".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "password reset" endpoint.
 */
function wapuus_api_register_password_reset() {

	register_rest_route(
		'wapuus-api/v1',
		'/password/reset',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'args'                => wapuus_api_password_reset_args(),
			'permission_callback' => 'wapuus_api_password_reset_permissions_check',
			'callback'            => 'wapuus_api_password_reset',
		)
	);
}
add_action( 'rest_api_init', 'wapuus_api_register_password_reset' );

/**
 * Schema of the expected arguments for the "password reset" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_password_reset_args() {

	$args = array(
		'login'    => array(
			'description' => __( 'The username of the user object to reset the password.', 'wapuus-api' ),
			'type'        => 'string',
			'required'    => true,
		),
		'password' => array(
			'description' => __( 'The new password of the user object.', 'wapuus-api' ),
			'type'        => 'string',
			'required'    => true,
		),
		'key'      => array(
			'description' => __( 'The password reset key for the user object.', 'wapuus-api' ),
			'type'        => 'string',
			'required'    => true,
		),
	);

	return $args;
}

/**
 * Permission callback for the "password reset" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_password_reset_permissions_check( $request ) {

	return true;
}

/**
 * Callback for the "password reset" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_password_reset( $request ) {

	$login = $request['login'];
	$key   = $request['key'];
	$user  = get_user_by( 'login', $login );

	if ( empty( $user ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\Not_Found( __( 'User does not exist.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	$check_key = check_password_reset_key( $key, $login );

	if ( is_wp_error( $check_key ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\Not_Acceptable( __( 'Expired token.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	$login    = $request['login'];
	$password = $request['password'];
	$user     = get_user_by( 'login', $login );

	reset_password( $user, $password );

	$response = new \Wapuus_API\Src\Classes\Responses\Valid\OK( __( 'Password has been changed.', 'wapuus-api' ) );

	return rest_ensure_response( $response );
}
