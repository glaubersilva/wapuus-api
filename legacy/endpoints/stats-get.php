<?php
/**
 * The legacy API V1 endpoint for "stats get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "stats get" endpoint.
 */
function wapuus_api_register_stats_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/stats',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Stats_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::READABLE,
				'args'                => wapuus_api_stats_get_args(),
				'permission_callback' => 'wapuus_api_stats_get_permissions_check',
				'callback'            => 'wapuus_api_stats_get',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wapuus_api_register_stats_get' );

/**
 * Schema of the expected arguments for the "stats get" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_stats_get_args() {

	$args = array();

	return $args;
}

/**
 * Permission callback for the "stats get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_stats_get_permissions_check( $request ) {

	if ( ! is_user_logged_in() ) {
		/**
		 * To better understand the "client error responses", check the link below:
		 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
		 */
		$response = new WP_Error( 'Unauthorized', __( 'User does not have permission.', 'wapuus-api' ), array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Callback for the "stats get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_stats_get( $request ) {

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
