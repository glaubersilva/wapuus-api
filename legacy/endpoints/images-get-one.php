<?php
/**
 * The legacy API V1 endpoints for "image get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "image get" endpoint.
 */
function wapuus_api_register_image_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/images/(?P<id>[0-9]+)',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::READABLE,
				'args'                => wapuus_api_image_get_args(),
				'permission_callback' => 'wapuus_api_image_get_permissions_check',
				'callback'            => 'wapuus_api_image_get',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);

}
add_action( 'rest_api_init', 'wapuus_api_register_image_get' );

/**
 * Schema of the expected arguments for the "image get" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_image_get_args() {

	$args = array(
		'id' => array(
			'description' => __( 'The ID of the image to retrieve.', 'wapuus-api' ),
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}

/**
 * Permission callback for the "image get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_image_get_permissions_check( $request ) {

	return true;
}

/**
 * Callback for the "image get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_image_get( $request ) {

	$post_id = absint( $request['id'] );
	$post    = get_post( $post_id );

	if ( ! isset( $post ) || empty( $post_id ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\Not_Found( __( 'Image not found.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	$image          = wapuus_api_get_post_data( $post );
	$image['views'] = (int) $image['views'] + 1;

	update_post_meta( $post_id, 'views', $image['views'] );

	$comments = get_comments(
		array(
			'post_id' => $post_id,
			'order'   => 'ASC',
		)
	);

	foreach ( $comments as $key => $comment ) {
		$comments[ $key ] = wapuus_api_get_comment_data( $comment );
	}

	$image = array(
		'image'    => $image,
		'comments' => $comments,
	);

	$response = new \Wapuus_API\Src\Classes\Responses\Valid\OK( $image );

	return rest_ensure_response( $response );
}
