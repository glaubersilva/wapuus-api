<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_comment_delete( $request ) {

	$comment_id = sanitize_key( $request['id'] );
	$comment    = get_comment( $comment_id ); // $comment_author = get_user_by( 'email', $comment->comment_author_email );
	$user       = wp_get_current_user();

	if ( (int) $user->ID !== (int) $comment->user_id || ! isset( $comment ) ) {
		$response = new WP_Error( 'error', 'Sem permissão.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	$response = wp_delete_comment( $comment_id, true );

	return rest_ensure_response( $response );
}

function wappus_api_comment_delete_permission_callback(){
	
	return true;
}

function wappus_register_api_comment_delete() {

	register_rest_route(
		'wapuus-api/v1',
		'/comment/(?P<id>[0-9]+)',
		array(
			'methods'  => WP_REST_Server::DELETABLE, // DELETE
			'callback' => 'wappus_api_comment_delete',
			'permission_callback' => 'wappus_api_comment_delete_permission_callback',
		)
	);
}
add_action( 'rest_api_init', 'wappus_register_api_comment_delete' );
