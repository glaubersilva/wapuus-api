<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_user_get( $request ) {

	$user = wp_get_current_user();

	if ( 0 === $user->ID ) {
		$response = new WP_Error( 'error', 'Usuário não possui permissão', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	$response = array(
		'id'       => $user->ID,
		'username' => $user->user_login,
		'name'     => $user->display_name,
		'email'    => $user->user_email,
	);

	return rest_ensure_response( $response );
}

function wappus_register_api_user_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/user',
		array(
			'methods'  => WP_REST_Server::READABLE, // GET
			'callback' => 'wappus_api_user_get',
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_user_get' );
