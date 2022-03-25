<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_user_post( $request ) {

	$email    = sanitize_email( $request['email'] );
	$username = sanitize_text_field( $request['username'] );
	//$password = $request['password'];
	$url      = $request['url']; // Needs impletation on the frontend

	if ( empty( $email ) || empty( $username ) /*|| empty( $password )*/ ) {
		$response = new WP_Error( 'error', 'Dados incompletos', array( 'status' => 406 ) );
		return rest_ensure_response( $response );
	}

	if ( username_exists( $username ) ) {
		$response = new WP_Error( 'error', 'Username já cadastrado', array( 'status' => 403 ) );
		return rest_ensure_response( $response );
	}

	if ( email_exists( $email ) ) {
		$response = new WP_Error( 'error', 'E-mail já cadastrado', array( 'status' => 403 ) );
		return rest_ensure_response( $response );
	}

	$user_id = wp_insert_user(
		array(
			'user_login' => $username,
			'user_email' => $email,
			//'user_pass'  => $password,
			'role'       => 'subscriber',
		)
	);

	if ( $user_id && ! is_wp_error( $user_id ) ) {

		$user    = get_user_by( 'ID', $user_id );
		$key     = get_password_reset_key( $user );
		$message = "Use the link below to create your password: \r\n";
		$url     = esc_url_raw( $url . "/?key=$key&login=" . rawurlencode( $username ) . "\r\n" );
		$body    = $message . $url;

		wp_mail( $email, 'Password Creation', $body );
	}

	return rest_ensure_response( $user_id );
}

function wappus_register_api_user_post_permission_callback(){

	return true;
}

function wappus_register_api_user_post() {

	register_rest_route(
		'wapuus-api/v1',
		'/users',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Users_Resource::get_instance(), 'schema' ),
			array(
				'methods'  => WP_REST_Server::CREATABLE, // POST
				'callback' => 'wappus_api_user_post',
				'permission_callback' => 'wappus_register_api_user_post_permission_callback',
				'args' => wappus_api_user_post_args(),
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_user_post' );

function wappus_api_user_post_args() {

	$args = array(
		'username' => array(
			'description' => __( 'Login name for the user.' ),
			'type'        => 'string',
			'context'     => array( 'edit' ),
			'required'    => true,
			/*'arg_options' => array(
				'sanitize_callback' => array( $this, 'check_username' ),
			),*/
		),
		'email'    => array(
			'description' => __( 'The email address for the user.' ),
			'type'        => 'string',
			'format'      => 'email',
			'context'     => array( 'edit' ),
			'required'    => true,
		),
		'url'    => array(
			'description' => __( 'Base URL used to create the "password creation" link that is sent by email.' ),
			'type'        => 'string',
			'format'      => 'uri',
			'context'     => array( 'view', 'edit', 'embed' ),
			'readonly'    => true,
		),
	);

	return $args;
}

