<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_comment_get( $request ) {

	$post_id = sanitize_key( $request['id'] );

	$comments = get_comments(
		array(
			'post_id' => $post_id,
		)
	);

	return rest_ensure_response( $comments );
}

function wappus_api_comment_get_permission_callback() {

	return true;
}

function wappus_register_api_comment_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/comment/(?P<id>[0-9]+)',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Comment_Resource::get_instance(), 'schema' ),
			array(
				'methods'             => WP_REST_Server::READABLE, // GET
				'callback'            => 'wappus_api_comment_get',
				'permission_callback' => 'wappus_api_comment_get_permission_callback',
				'args'                => wappus_api_comment_get_args(),
			),
			// Aqui poderia ter outro array com a declaração do método POST por exemplo.
		)
	);
}
add_action( 'rest_api_init', 'wappus_register_api_comment_get' );

/**
 * Get the argument schema for this example endpoint.
 *
 * You use it to configure what arguments (thus the name args) the WordPress REST API expects to receive for the
 * endpoint that you’re registering. You also use it to tell the WordPress REST API how to process these arguments
 * when it receives them. And, like its parent array, the args array is also an associative array. Each key of the
 * array is a parameter that your endpoint can accept.
 */
function wappus_api_comment_get_args() {

	$args = array( // A declaração dos argumentos que esse endpoint aceita.
		'id' => array( // Cada argumento descrito em JSON Schema.
			'description' => 'The ID of the photo (Wapuu) to retrieve the comments.',
			'type'        => 'integer',
			//'default'     => 0,
			'required'    => true,
			// 'validate_callback' => 'my_custom_validate_callback', // IMPORTANT: if you specify a custom validate_callback for your argument definition, the built-in JSON Schema validation will not apply.
			// 'sanitize_callback' => 'my_custom_sanitize_callback', // IMPORTANT: if you specify a custom sanitize_callback for your argument definition, the built-in JSON Schema validation will not apply.
		),
	);

	return $args;
}
