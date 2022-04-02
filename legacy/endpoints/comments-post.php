<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_comment_post( $request ) {


	$user = wp_get_current_user();

	if ( $user->ID === 0 ) {
		$response = new WP_Error( 'error', 'User does not have permission.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new WP_Error( 'error', 'Demo user does not have permission.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

    $comment = sanitize_textarea_field( $request['comment'] );

	if ( empty( $comment) ) {
		$response = new WP_Error( 'error', 'The comment is required.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	$post_id = sanitize_key( $request['id'] );

	$response = array(
		'user_id'         => $user->ID,
		'comment_author'  => $user->user_login,
		'comment_content' => $comment,
		'comment_post_ID' => $post_id,
	);

	$comment_id = wp_insert_comment( $response );
	$comment = get_comment( $comment_id );

	return rest_ensure_response( $comment );
}

function wappus_api_comment_post_permission_callback(){
	
	return true;
}

function wappus_register_api_comment_post() {

	register_rest_route(
		'wapuus-api/v1',
		'/comments/(?P<id>[0-9]+)',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comments_Resource::get_instance(), 'schema' ),
			array(
				'methods'  => WP_REST_Server::CREATABLE, // POST
				'callback' => 'wappus_api_comment_post',
				'permission_callback' => 'wappus_api_comment_post_permission_callback',
				'args'                => wappus_api_comment_post_args(),
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_comment_post' );


/**
 * Get the argument schema for this example endpoint.
 *
 * You use it to configure what arguments (thus the name args) the WordPress REST API expects to receive for the
 * endpoint that you’re registering. You also use it to tell the WordPress REST API how to process these arguments
 * when it receives them. And, like its parent array, the args array is also an associative array. Each key of the
 * array is a parameter that your endpoint can accept.
 */
function wappus_api_comment_post_args() {

	$args = array( // A declaração dos argumentos que esse endpoint aceita.
		'id' => array( // Cada argumento descrito em JSON Schema.
			'description' => 'The ID of the image object that will receive the comment.',
			'type'        => 'integer',
			//'default'     => 0,
			'required'    => true,
			// 'validate_callback' => 'my_custom_validate_callback', // IMPORTANT: if you specify a custom validate_callback for your argument definition, the built-in JSON Schema validation will not apply.
			// 'sanitize_callback' => 'my_custom_sanitize_callback', // IMPORTANT: if you specify a custom sanitize_callback for your argument definition, the built-in JSON Schema validation will not apply.
		),
		'comment' => array( // Cada argumento descrito em JSON Schema.
			'description' => 'The content of the comment.',
			'type'        => 'string',
			'required'    => true,
		),
	);

	return $args;
}
