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

		$response = new WP_Error( 'error', 'Informe o email ou login.', array( 'status' => 406 ) );
		return rest_ensure_response( $response );

	}

	$user = get_user_by( 'email', $login );

	if ( empty( $user ) ) {
		$user = get_user_by( 'login', $login );
	}

	if ( empty( $user ) ) {

		$response = new WP_Error( 'error', 'Usuário não existe.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );

	}

	$user_login = $user->user_login;
	$user_email = $user->user_email;
	$key        = get_password_reset_key( $user );
	$message    = "utilize o link abaixo para resetar a sua senha: \r\n";
	$url = esc_url_raw( $url . "/?key=$key&login=" . rawurlencode( $user_login ) . "\r\n" );
	$body = $message . $url;

	wp_mail( $user_email, 'Password Reset', $body );

	return rest_ensure_response( 'Email enviado.' );
}

function wappus_register_api_password_lost() {

	register_rest_route(
		'wapuus-api/v1',
		'/password/lost',
		array(
			'methods'  => WP_REST_Server::CREATABLE, // POST
			'callback' => 'wappus_api_password_lost',
			'permission_callback' => 'wappus_api_password_permission_callback',
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_password_lost' );






function wappus_api_password_reset( $request ) {

	//return rest_ensure_response( 'Senha Alterada TESTE.' );
	$login = $request['login'];
	$password   = $request['password'];
	$key   = $request['key'];
	$user = get_user_by( 'login', $login );

	if ( empty( $user ) ) {

		$response = new WP_Error( 'error', 'Usuário não existe.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );

	}

	$check_key = check_password_reset_key( $key, $login );

	if ( is_wp_error( $check_key ) ) {
		$response = new WP_Error( 'error', 'Token expirado.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	reset_password( $user, $password );

	return rest_ensure_response( 'Senha Alterada.' );
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
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_password_reset' );
