<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_password_permission_callback(){

	return true;
}

function wappus_api_password_lost( $request ) {

	$login = $request['login'];
	$url   = $request['url'];

	if ( empty( $login ) ) {

		$response = new WP_Error( 'error', 'Enter your email or login.', array( 'status' => 406 ) );
		return rest_ensure_response( $response );

	}

	$user = get_user_by( 'email', $login );	

	if ( empty( $user ) ) {
		$user = get_user_by( 'login', $login );
	}

	if ( empty( $user ) ) {

		$response = new WP_Error( 'error', 'User does not exist.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );

	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new WP_Error( 'error', 'Demo user does not have permission.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	$user_login = $user->user_login;
	$user_email = $user->user_email;
	$key        = get_password_reset_key( $user );
	$message    = "Use the link below to reset your password: \r\n";
	$url = esc_url_raw( $url . "/?key=$key&login=" . rawurlencode( $user_login ) . "\r\n" );
	$body = $message . $url;

	wp_mail( $user_email, 'Password Reset', $body );

	return rest_ensure_response( 'Email sent.' );
}

function wappus_register_api_password_lost() {

	register_rest_route(
		'wapuus-api/v1',
		'/password/lost',
		array(
			'methods'  => WP_REST_Server::CREATABLE, // POST
			'callback' => 'wappus_api_password_lost',
			'permission_callback' => 'wappus_api_password_permission_callback',
			'args' => wappus_api_password_lost_args(),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_password_lost' );


function wappus_api_password_lost_args() {

	$args = array(
		'login' => array(
			'description' => 'The username of the user object to reset the password.',
			'type'        => 'string',
			'required'    => true,
		),
		'url' => array(
			'description' => 'Base URL used to create the "reset password" link that is sent by email.',
			'type'        => 'string',
			'format'      => 'uri',
			'required'    => true,
		),
	);

	return $args;
}



function wappus_api_password_reset( $request ) {

	//return rest_ensure_response( 'Senha Alterada TESTE.' );
	$login = $request['login'];
	$password   = $request['password'];
	$key   = $request['key'];
	$user = get_user_by( 'login', $login );

	if ( empty( $user ) ) {

		$response = new WP_Error( 'error', 'User does not exist.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	if ( empty( $password ) ) {

		$response = new WP_Error( 'error', 'Password is required.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new WP_Error( 'error', 'Demo user does not have permission.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	$check_key = check_password_reset_key( $key, $login );

	if ( is_wp_error( $check_key ) ) {
		$response = new WP_Error( 'error', 'Expired token.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	reset_password( $user, $password );

	return rest_ensure_response( 'Password has been changed.' );
}

function wappus_register_api_password_reset() {

	register_rest_route(
		'wapuus-api/v1',
		'/password/reset',
		array(
			'methods'  => WP_REST_Server::CREATABLE, // POST
			'callback' => 'wappus_api_password_reset',
			'callback' => 'wappus_api_password_reset',
			'permission_callback' => 'wappus_api_password_permission_callback',
			'args' => wappus_api_password_reset_args(),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_password_reset' );

function wappus_api_password_reset_args() {

	$args = array(
		'login' => array(
			'description' => 'The username of the user object to reset the password.',
			'type'        => 'string',
			'required'    => true,
		),
		'password' => array(
			'description' => 'The new password of the user object.',
			'type'        => 'string',
			'required'    => true,
		),
		'key' => array(
			'description' => 'The password reset key for the user object.',
			'type'        => 'string',
			'required'    => true,
		),
	);

	return $args;
}
