<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function gs_api_comment_post( $request ) {

    
	$user = wp_get_current_user();

	if ( $user->ID === 0 ) {
		$response = new WP_Error( 'error', 'Sem permissão.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

    $comment = sanitize_textarea_field( $request['comment'] );

	if ( empty( $comment) ) {
		$response = new WP_Error( 'error', 'Dados incompletos.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	$post_id = sanitize_key( $request['id'] );

	$response = array(
		'user_id'         => $user->ID,
		'comment_author'  => $user->user_login,
		'comment_content' => $comment,
		'comment_post_ID' => $post_id,
	);

	$comment_id = wp_insert_comment( $response );
	$comment = get_comment( $comment_id );

	return rest_ensure_response( $comment );
}

function gs_register_api_comment_post() {

	register_rest_route(
		'gs-wapuus-api/v1',
		'/comment/(?P<id>[0-9]+)',
		array(
			'methods'  => WP_REST_Server::CREATABLE, // POST
			'callback' => 'gs_api_comment_post',
		)
	);

}
add_action( 'rest_api_init', 'gs_register_api_comment_post' );
