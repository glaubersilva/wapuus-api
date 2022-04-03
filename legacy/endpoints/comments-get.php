<?php
/**
 * The legacy API V1 endpoint for "comments get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "comments get" endpoint.
 */
function wappus_register_api_comments_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/comments/(?P<id>[0-9]+)',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the endpoint accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comments_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::READABLE,
				'args'                => wappus_api_comments_get_args(),
				'permission_callback' => 'wappus_api_comments_get_permissions_check',
				'callback'            => 'wappus_api_comments_get',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wappus_register_api_comments_get' );

/**
 * Schema of the expected arguments for the "comments get" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wappus_api_comments_get_args() {

	$args = array(
		'id' => array(
			'description' => __( 'The ID of the image object to retrieve the comments.', 'wapuus-api' ),
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}

/**
 * Permission callback for the "comments get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wappus_api_comments_get_permissions_check( $request ) {

	return true;
}

/**
 * Callback for the "comments get" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wappus_api_comments_get( $request ) {

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
