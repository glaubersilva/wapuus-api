<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_stats_get( $request ) {

	$user = wp_get_current_user();

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

function wappus_api_stats_permission_callback(){

	if ( ! is_user_logged_in() ) {

		$response = new \WP_Error( 'error', 'User does not have permission.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );

	}

	return true;
}

function wappus_register_api_stats_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/stats',
		array(
			'methods'  => WP_REST_Server::READABLE, // GET
			'callback' => 'wappus_api_stats_get',
			'permission_callback' => 'wappus_api_stats_permission_callback',
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_stats_get' );
