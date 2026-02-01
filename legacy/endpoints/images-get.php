<?php
/**
 * The legacy API V1 endpoints for "images get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "images get" endpoint.
 */
function wapuus_api_register_images_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/images',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Core\Schemas\Images_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::READABLE,
				'args'                => wapuus_api_images_get_args(),
				'permission_callback' => 'wapuus_api_images_get_permissions_check',
				'callback'            => 'wapuus_api_images_get',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);

}
add_action( 'rest_api_init', 'wapuus_api_register_images_get' );

/**
 * Schema of the expected arguments for the "images get" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_images_get_args() {

	$args = array(
		'_total' => array(
			'description' => __( 'Total number of images per page - if not set, the default value is 6.', 'wapuus-api' ),
			'type'        => 'integer',
		),
		'_page'  => array(
			'description' => __( 'The number of the page to retrieve - if not set, the default value is 1.', 'wapuus-api' ),
			'type'        => 'integer',
		),
		'_user'  => array(
			'description' => __( 'The ID or username of the user object to retrieve the images - if not set, returns images from all users.', 'wapuus-api' ),
			'type'        => 'string',
		),
	);

	return $args;
}

/**
 * Permission callback for the "images get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_images_get_permissions_check( $request ) {

	return true;
}

/**
 * Callback for the "images get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_images_get( $request ) {

	if ( isset( $request['_user'] ) && ! is_numeric( $request['_user'] ) ) {

		$user = get_user_by( 'login', sanitize_user( $request['_user'] ) );

		if ( ! $user ) {

			$response = new \Wapuus_API\Src\Core\Responses\Error\Not_Found( __( 'User not found.', 'wapuus-api' ) );
			return rest_ensure_response( $response );
		}
	}

	$_total = isset( $request['_total'] ) ? sanitize_text_field( $request['_total'] ) : 6;
	$_page  = isset( $request['_page'] ) ? sanitize_text_field( $request['_page'] ) : 1;
	$_user  = isset( $request['_user'] ) ? sanitize_user( $request['_user'] ) : 0;

	if ( ! is_numeric( $_user ) ) {
		$user  = get_user_by( 'login', $_user );
		$_user = $user->ID;
	}

	$args = array(
		'post_type'      => 'wapuu',
		'author'         => $_user,
		'posts_per_page' => $_total,
		'paged'          => $_page,
	);

	$query = new WP_Query( $args );
	$posts = $query->posts;

	$images = array();

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$images[] = wapuus_api_get_post_data( $post );
		}
	}

	$response = new \Wapuus_API\Src\Core\Responses\Valid\OK( $images );

	return rest_ensure_response( $response );
}
