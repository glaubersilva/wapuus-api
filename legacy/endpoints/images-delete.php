<?php
/**
 * The legacy API V1 endpoint for "image delete".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

/**
 * Register the "image delete" endpoint.
 */
function wappus_register_api_image_delete() {

	register_rest_route(
		'wapuus-api/v1',
		'/images/(?P<id>[0-9]+)',
		array( // The callback to the endpoint resource schema - note that the resource schema is the same for all methods that the endpoint accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'wappus_api_image_delete',
				'permission_callback' => 'wappus_api_image_delete_permissions_check',
				'args'                => wappus_api_image_delete_args(),
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_image_delete' );

/**
 * The callback to delete an image.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wappus_api_image_delete( $request ) {

	$post_id = sanitize_key( $request['id'] );

	$attachment_id = get_post_meta( $post_id, 'img', true );
	wp_delete_attachment( $attachment_id, true );
	wp_delete_post( $post_id, true );

	$response = 'Deleted.';
	return rest_ensure_response( $response );
}

/**
 * The permission callback to delete an image comment.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wappus_api_image_delete_permissions_check( $request ) {

	$user    = wp_get_current_user();
	$post_id = sanitize_key( $request['id'] );
	$post    = get_post( $post_id );

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

	if ( (int) $user->ID !== (int) $post->post_author || ! isset( $post ) ) {
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
 * Get the expected arguments for the REST API endpoint.
 *
 * @return array Arguments.
 */
function wappus_api_image_delete_args() {
	$args = array(
		'id' => array(
			'description' => __( 'The ID of the image to delete.', 'wapuus-api' ),
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}
