<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function gs_api_comment_get( $request ) {

	$post_id = sanitize_key( $request['id'] );
	
	$comments = get_comments( 
		array(
			'post_id' => $post_id
		)
	);

	return rest_ensure_response( $comments );
}

function gs_register_api_comment_get() {

	register_rest_route(
		'gs-wapuus-api/v1',
		'/comment/(?P<id>[0-9]+)',
		array(
			'methods'  => WP_REST_Server::READABLE, // GET
			'callback' => 'gs_api_comment_get',
		)
	);

}
add_action( 'rest_api_init', 'gs_register_api_comment_get' );
