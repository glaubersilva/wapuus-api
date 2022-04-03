<?php
/**
 * The legacy API V1 endpoint for "comment delete".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "comment delete" endpoint.
 */
function wapuus_api_register_comment_delete() {

	register_rest_route(
		'wapuus-api/v1',
		'/comments/(?P<id>[0-9]+)',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the endpoint accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comments_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'args'                => wapuus_api_comment_delete_args(),
				'permission_callback' => 'wapuus_api_comment_delete_permissions_check',
				'callback'            => 'wapuus_api_comment_delete',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wapuus_api_register_comment_delete' );

/**
 * Schema of the expected arguments for the "comment delete" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_comment_delete_args() {

	$args = array(
		'id' => array(
			'description' => __( 'The ID of the comment to delete.', 'wapuus-api' ),
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}

/**
 * Permission callback for the "comment delete" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_comment_delete_permissions_check( $request ) {

	$user       = wp_get_current_user();
	$comment_id = sanitize_key( $request['id'] );
	$comment    = get_comment( $comment_id );

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

	if ( (int) $user->ID !== (int) $comment->user_id || ! isset( $comment ) ) {
		$response = new WP_Error( $no_permission_code, __( 'User does not have permission.', 'wapuus-api' ), array( 'status' => $no_permission_status ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new WP_Error( $no_permission_code, __( 'Demo user does not have permission.', 'wapuus-api' ), array( 'status' => $no_permission_status ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Callback for the "comment delete" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_comment_delete( $request ) {

	$comment_id = sanitize_key( $request['id'] );
	$response   = wp_delete_comment( $comment_id, true );

	return rest_ensure_response( $response );
}
