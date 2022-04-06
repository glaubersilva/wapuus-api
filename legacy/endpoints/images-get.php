<?php
/**
 * The legacy API V1 endpoints for "image get" and "images get".
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

	$post_id = sanitize_key( $request['id'] );
	$post    = get_post( $post_id );

	/**
	 * To better understand the "client error responses", check the link below:
	 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 */
	$not_fond_status = 404;
	$not_fond_code   = 'Not Found';

	if ( ! isset( $post ) || empty( $post_id ) ) {
		$response = new WP_Error( $not_fond_code, __( 'Image not found.', 'wapuus-api' ), array( 'status' => $not_fond_status ) );
		return rest_ensure_response( $response );
	}

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

	$post_id        = sanitize_key( $request['id'] );
	$post           = get_post( $post_id );
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

	$response = array(
		'image'    => $image,
		'comments' => $comments,
	);

	return rest_ensure_response( $response );
}

/**
 * Register the "images get" endpoint.
 */
function wapuus_api_register_images_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/images',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
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

	if ( isset( $request['_user'] ) && ! is_numeric( $request['_user'] ) ) {

		$user = get_user_by( 'login', sanitize_text_field( $request['_user'] ) );

		if ( ! $user ) {

			/**
			 * To better understand the "client error responses", check the link below:
			 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
			 */
			$not_fond_status = 404;
			$not_fond_code   = 'Not Found';

			$response = new WP_Error( $not_fond_code, __( 'User not found.', 'wapuus-api' ), array( 'status' => $not_fond_status ) );
			return rest_ensure_response( $response );
		}
	}

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

	$_total = isset( $request['_total'] ) ? sanitize_text_field( $request['_total'] ) : 6;
	$_page  = isset( $request['_page'] ) ? sanitize_text_field( $request['_page'] ) : 1;
	$_user  = isset( $request['_user'] ) ? sanitize_text_field( $request['_user'] ) : 0;

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

	return rest_ensure_response( $images );
}
