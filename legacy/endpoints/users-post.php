<?php
/**
 * The legacy API V1 endpoint for "user post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "user post" endpoint.
 */
function wapuus_api_register_user_post() {

	register_rest_route(
		'wapuus-api/v1',
		'/users',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Users_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'args'                => wapuus_api_user_post_args(),
				'permission_callback' => 'wapuus_api_user_post_permissions_check',
				'callback'            => 'wapuus_api_user_post',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wapuus_api_register_user_post' );

/**
 * Schema of the expected arguments for the "user post" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_user_post_args() {

	$args = array(
		'username' => array(
			'description' => __( 'Login name for the user.', 'wapuus-api' ),
			'type'        => 'string',
			'required'    => true,
		),
		'email'    => array(
			'description' => __( 'The email address for the user.', 'wapuus-api' ),
			'type'        => 'string',
			'format'      => 'email',
			'required'    => true,
		),
		'url'      => array(
			'description' => __( 'Base URL used to create the "password creation" link that is sent by email.', 'wapuus-api' ),
			'type'        => 'string',
			'format'      => 'uri',
			'required'    => true,
		),
	);

	return $args;
}

/**
 * Permission callback for the "user post" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_user_post_permissions_check( $request ) {

	return true;
}

/**
 * Callback for the "user post" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_user_post( $request ) {

	$email    = sanitize_email( $request['email'] );
	$username = sanitize_user( $request['username'] );

	if ( empty( $email ) || empty( $username ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\Incomplete_Data( __( 'Email and username are required.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	if ( username_exists( $username ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\Not_Acceptable( __( 'Username already in use.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	if ( email_exists( $email ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\Not_Acceptable( __( 'Email already in use.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	$url = $request['url'];

	$user_id = wp_insert_user(
		array(
			'user_login' => $username,
			'user_email' => $email,
			'user_pass'  => wp_generate_password(),
			'role'       => 'subscriber',
		)
	);

	if ( $user_id && ! is_wp_error( $user_id ) ) {

		$user    = get_user_by( 'ID', $user_id );
		$key     = get_password_reset_key( $user );
		$message = __( 'Use the link below to create your password:', 'wapuus-api' ) . "\r\n";
		$url     = esc_url_raw( $url . "/?key=$key&login=" . rawurlencode( $username ) . "\r\n" );
		$body    = $message . $url;

		wp_mail( $email, __( 'Password Creation', 'wapuus-api' ), $body );
	}

	$user = array(
		'id'       => $user_id,
		'username' => $username,
		'email'    => $email,
	);

	$response = new \Wapuus_API\Src\Classes\Responses\Valid\Created( $user );

	return rest_ensure_response( $response );
}
