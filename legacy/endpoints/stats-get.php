<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function gs_api_stats_get( $request ) {

	$user = wp_get_current_user();

	if ( 0 === $user->ID ) {

		$response = new WP_Error( 'error', 'Usuário não possui permissão.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );

	}

	$args = array(
		'post_type'     => 'wapuu',
		'author'        => $user->ID,
		'post_per_page' => -1,
	);

	$query = new WP_Query( $args );
	$posts = $query->posts;

	$stats = array();

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$stats[] = array(
				'id'    => $post->ID,
				'title' => $post->post_title,
				'views' => get_post_meta( $post->ID, 'views', true ),

			);
		}
	}

	return rest_ensure_response( $stats );
}

function gs_register_api_stats_get() {

	register_rest_route(
		'gs-wapuus-api/v1',
		'/stats',
		array(
			'methods'  => WP_REST_Server::READABLE, // GET
			'callback' => 'gs_api_stats_get',
		)
	);

}
add_action( 'rest_api_init', 'gs_register_api_stats_get' );
