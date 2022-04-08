<?php
/**
 * The legacy API V1 endpoint for "image delete".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "image delete" endpoint.
 */
function wapuus_api_register_image_delete() {

	register_rest_route(
		'wapuus-api/v1',
		'/images/(?P<id>[0-9]+)',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'args'                => wapuus_api_image_delete_args(),
				'permission_callback' => 'wapuus_api_image_delete_permissions_check',
				'callback'            => 'wapuus_api_image_delete',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wapuus_api_register_image_delete' );

/**
 * Schema of the expected arguments for the "image delete" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_image_delete_args() {
	$args = array(
		'id' => array(
			'description' => __( 'The ID of the image to delete.', 'wapuus-api' ),
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}

/**
 * Permission callback for the "image delete" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_image_delete_permissions_check( $request ) {

	$user    = wp_get_current_user();
	$post_id = sanitize_key( $request['id'] );
	$post    = get_post( $post_id );

	if ( (int) $user->ID !== (int) $post->post_author || ! isset( $post ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'User does not have permission.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Callback for the "image delete" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_image_delete( $request ) {

	$post_id = sanitize_key( $request['id'] );

	$attachment_id = get_post_meta( $post_id, 'img', true );
	wp_delete_attachment( $attachment_id, true );
	wp_delete_post( $post_id, true );

	$response = __( 'Deleted.', 'wapuus-api' );
	return rest_ensure_response( $response );
}
