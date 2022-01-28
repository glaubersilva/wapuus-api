<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function gs_api_photo_post( $request ) {

	$user = wp_get_current_user();

	if ( 0 === $user->ID ) {
		$response = new WP_Error( 'error', 'Usuário não possui permissão.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	$name   = sanitize_text_field( $request['name'] );
	$weight = sanitize_text_field( $request['weight'] );
	$age    = sanitize_text_field( $request['age'] );
	$files  = $request->get_file_params();

	if ( empty( $name ) || empty( $weight ) || empty( $age ) || empty( $files ) ) {
		$response = new WP_Error( 'error', 'Dados incompletos.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	$post = array(
		'post_author'  => $user->ID,
		'post_type'    => 'post',
		'post_status'  => 'publish',
		'post_title'   => $name,
		'post_content' => $name,
		'files'        => $files,
		'meta_input'   => array(
			'weight' => $weight,
			'age'    => $age,
			'views'  => 0,
		),
	);

	$post_id = wp_insert_post( $post );

	// These files need to be included as dependencies when on the front end.
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$photo_id = media_handle_upload( 'img', $post_id );
	update_post_meta( $post_id, 'img', $photo_id );

	$response = $post;

	return rest_ensure_response( $response );
}

function gs_register_api_photo_post() {

	register_rest_route(
		'gs-wapuus-api/v1',
		'/photo',
		array(
			'methods'  => WP_REST_Server::CREATABLE, // POST
			'callback' => 'gs_api_photo_post',
		)
	);

}
add_action( 'rest_api_init', 'gs_register_api_photo_post' );
