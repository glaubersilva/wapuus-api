<?php
/**
 * The legacy API V1 endpoint for comment delete.
 *
 * @package Wapuus_API
 */

/**
 * Register the "comment get" endpoint.
 */
function wappus_register_api_comment_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/comments/(?P<id>[0-9]+)',
		array( // The callback to the endpoint resource schema - note that the resource schema is the same for all methods that the endpoint accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comments_Resource::get_instance(), 'schema' ),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'wappus_api_comment_get',
				'permission_callback' => 'wappus_api_comment_get_permissions_check',
				'args'                => wappus_api_comment_get_args(),
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wappus_register_api_comment_get' );

/**
 * The callback to get an image comment.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wappus_api_comment_get( $request ) {

	$post_id = sanitize_key( $request['id'] );

	$comments = get_comments(
		array(
			'post_id' => $post_id,
		)
	);

	foreach ( $comments as $key => $comment ) {
		$comments[ $key ] = wappus_api_get_comment_data( $comment );
	}

	return rest_ensure_response( $comments );
}

/**
 * The permission callback to delete an image comment.
 *
 * @param object|\WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wappus_api_comment_get_permissions_check( $request ) {

	return true;
}

/**
 * Get the expected arguments for the REST API endpoint.
 *
 * @return array Arguments.
 */
function wappus_api_comment_get_args() {

	$args = array(
		'id' => array(
			'description' => 'The ID of the image object to retrieve the comments.',
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}
