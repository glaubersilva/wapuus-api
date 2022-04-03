<?php
/**
 * The legacy API V1 endpoint for "comment post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

/**
 * Register the "comment post" endpoint.
 */
function wappus_register_api_comment_post() {

	register_rest_route(
		'wapuus-api/v1',
		'/comments/(?P<id>[0-9]+)',
		array( // The callback to the endpoint resource schema - note that the resource schema is the same for all methods that the endpoint accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comments_Resource::get_instance(), 'schema' ),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'wappus_api_comment_post',
				'permission_callback' => 'wappus_api_comment_post_permissions_check',
				'args'                => wappus_api_comment_post_args(),
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_comment_post' );

/**
 * The callback to post an image comment.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wappus_api_comment_post( $request ) {

	$user    = wp_get_current_user();
	$comment = sanitize_textarea_field( $request['comment'] );
	$post_id = sanitize_key( $request['id'] );

	$new_wp_comment = array(
		'user_id'         => $user->ID,
		'comment_author'  => $user->user_login,
		'comment_content' => $comment,
		'comment_post_ID' => $post_id,
	);

	$comment_id = wp_insert_comment( $new_wp_comment );
	$comment    = get_comment( $comment_id );

	$response = wappus_api_get_comment_data( $comment );

	return rest_ensure_response( $response );
}

/**
 * The permission callback to post an image comment.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wappus_api_comment_post_permissions_check( $request ) {

	$user = wp_get_current_user();

	/**
	 * To better understand the "client error responses", check the link below:
	 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 */
	if ( is_user_logged_in() ) {
		$no_permission_status = 403;
		$no_permission_code   = 'Forbidden';
	} else {
		$no_permission_status = 401;
		$no_permission_code   = 'Unauthorized';
	}

	$incomplete_data_status = 422;
	$incomplete_data_code   = 'Unprocessable Entity';

	if ( 0 === $user->ID ) {
		$response = new WP_Error( $no_permission_code, __( 'User does not have permission.', 'wapuus-api' ), array( 'status' => $no_permission_status ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new WP_Error( $no_permission_code, __( 'Demo user does not have permission.', 'wapuus-api' ), array( 'status' => $no_permission_status ) );
		return rest_ensure_response( $response );
	}

	$comment = sanitize_textarea_field( $request['comment'] );

	if ( empty( $comment ) ) {
		$response = new WP_Error( $incomplete_data_code, __( 'The comment is required.', 'wapuus-api' ), array( 'status' => $incomplete_data_status ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Get the expected arguments for the REST API endpoint.
 *
 * @return array Arguments.
 */
function wappus_api_comment_post_args() {

	$args = array(
		'id'      => array(
			'description' => __( 'The ID of the image object that will receive the comment.', 'wapuus-api' ),
			'type'        => 'integer',
			'required'    => true,
		),
		'comment' => array(
			'description' => __( 'The content of the comment.', 'wapuus-api' ),
			'type'        => 'string',
			'required'    => true,
		),
	);

	return $args;
}
