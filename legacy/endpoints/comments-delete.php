<?php
/**
 * The legacy API V1 endpoint for comment delete.
 *
 * @package Wapuus_API
 */

/**
 * Register the "comment delete" endpoint.
 */
function wappus_register_api_comment_delete() {

	register_rest_route(
		'wapuus-api/v1',
		'/comments/(?P<id>[0-9]+)',
		array( // The callback to the endpoint resource schema - note that the resource schema is the same for all methods that the endpoint accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comments_Resource::get_instance(), 'schema' ),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'wappus_api_comment_delete',
				'permission_callback' => 'wappus_api_comment_delete_permissions_check',
				'args'                => wappus_api_comment_delete_args(),
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wappus_register_api_comment_delete' );

/**
 * The callback to delete an image comment.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wappus_api_comment_delete( $request ) {

	$response = wp_delete_comment( sanitize_key( $request['id'] ), true );

	return rest_ensure_response( $response );
}

/**
 * The permission callback to delete an image comment.
 *
 * @param object|\WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wappus_api_comment_delete_permissions_check( $request ) {

	$user       = wp_get_current_user();
	$comment_id = sanitize_key( $request['id'] );
	$comment    = get_comment( $comment_id );

	/**
	 * To better understand the "client error responses", check the link below:
	 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 */
	if ( is_user_logged_in() ) {
		$status = 403;
		$code   = 'Forbidden';
	} else {
		$status = 401;
		$code   = 'Unauthorized';
	}

	if ( (int) $user->ID !== (int) $comment->user_id || ! isset( $comment ) ) {
		$response = new WP_Error( $code, __( 'User does not have permission.', 'wapuus-api' ), array( 'status' => $status ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new WP_Error( $code, __( 'Demo user does not have permission.', 'wapuus-api' ), array( 'status' => $status ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Get the expected arguments for the REST API endpoint.
 *
 * @return array Arguments.
 */
function wappus_api_comment_delete_args() {

	$args = array(
		'id' => array(
			'description' => 'The ID of the comment to delete.',
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}
