<?php
/**
 * The legacy API V1 endpoint for "comment post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the "comment post" endpoint.
 */
function wapuus_api_register_comment_post() {

	register_rest_route(
		'wapuus-api/v1',
		'/comments/(?P<id>[0-9]+)',
		array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comments_Resource::get_instance(), 'schema' ), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'args'                => wapuus_api_comment_post_args(),
				'permission_callback' => 'wapuus_api_comment_post_permissions_check',
				'callback'            => 'wapuus_api_comment_post',
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);
}
add_action( 'rest_api_init', 'wapuus_api_register_comment_post' );

/**
 * Schema of the expected arguments for the "comment post" endpoint.
 *
 * Reference: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
 *
 * @return array Arguments.
 */
function wapuus_api_comment_post_args() {

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

/**
 * Permission callback for the "comment post" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wapuus_api_comment_post_permissions_check( $request ) {

	if ( ! is_user_logged_in() ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'User does not have permission.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( get_current_user_id() ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Callback for the "comment post" endpoint.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wapuus_api_comment_post( $request ) {

	$comment = sanitize_textarea_field( $request['comment'] );

	if ( empty( $comment ) ) {
		$response = new \Wapuus_API\Src\Classes\Responses\Error\Incomplete_Data( __( 'The comment is required.', 'wapuus-api' ) );
		return rest_ensure_response( $response );
	}

	$user    = wp_get_current_user();
	$post_id = absint( $request['id'] );

	$new_wp_comment = array(
		'user_id'         => $user->ID,
		'comment_author'  => $user->user_login,
		'comment_content' => $comment,
		'comment_post_ID' => $post_id,
	);

	$comment_id = wp_insert_comment( $new_wp_comment );
	$comment    = get_comment( $comment_id );
	$comment    = wapuus_api_get_comment_data( $comment );

	$response = new \Wapuus_API\Src\Classes\Responses\Valid\Created( $comment );

	return rest_ensure_response( $response );
}
