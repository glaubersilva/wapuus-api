<?php
/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 *
 * @package Wapuus API
 * @author Glauber Silva
 * @link https://glaubersilva.me/
 */

function wappus_api_image_get( $request ) {

	$post_id = sanitize_key( $request['id'] );
	$post    = get_post( $post_id );

	if ( ! isset( $post ) || empty( $post_id ) ) {
		$response = new WP_Error( 'error', 'Post não encontrado.', array( 'status' => 404 ) );
		return rest_ensure_response( $response );
	}

	$image = wappus_api_get_post_data( $post );

	$image['views'] = (int) $image['views'] + 1;
	update_post_meta( $post_id, 'views', $image['views'] );

	$comments = get_comments(
		array(
			'post_id' => $post_id,
			'order'   => 'ASC',
		)
	);

	foreach ( $comments as $key => $comment ) {
		$comments[ $key ] = wappus_api_get_comment_data( $comment );
	}

	$response = array(
		'image'    => $image,
		'comments' => $comments,
	);

	return rest_ensure_response( $response );
}

function wappus_api_image_get_permission_callback() {

	return true;
}

function wappus_register_api_image_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/images/(?P<id>[0-9]+)',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ),			
			array(
				'methods'  => WP_REST_Server::READABLE, // GET
				'callback' => 'wappus_api_image_get',
				'permission_callback' => 'wappus_api_image_get_permission_callback',
				'args' => wappus_api_image_get_args(),
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_image_get' );

function wappus_api_image_get_args() {

	$args = array(
		'id' => array(
			'description' => 'The ID of the image to retrieve.',
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}

function wappus_api_images_get( $request ) {

	$_total = sanitize_text_field( $request['_total'] ) ?: 6;
	$_page  = sanitize_text_field( $request['_page'] ) ?: 1;
	$_user  = sanitize_text_field( $request['_user'] ) ?: 0;

	if ( ! is_numeric( $_user ) ) {
		$user = get_user_by( 'login', $_user );

		if ( ! $user ) {
			$response = new WP_Error( 'error', 'User not found.', array( 'status' => 404 ) );
			return rest_ensure_response( $response );
		}

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
			$images[] = wappus_api_get_post_data( $post );
		}
	}

	return rest_ensure_response( $images );
}

function wappus_api_images_get_permission_callback() {

	return true;
}

function wappus_register_api_images_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/images',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ),
			array(
				'methods'  => WP_REST_Server::READABLE, // GET
				'callback' => 'wappus_api_images_get',
				'permission_callback' => 'wappus_api_images_get_permission_callback',	
				'args' => wappus_api_images_get_args(),
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_images_get' );

function wappus_api_images_get_args() {

	$args = array(
		'_total' => array(
			'description' => 'Total number of images per page - if not set, the default value is 6.',
			'type'        => 'integer',
		),
		'_page' => array(
			'description' => 'The number of the page to retrieve - if not set, the default value is 1.',
			'type'        => 'integer',
		),
		'_user' => array(
			'description' => 'The ID or username of the user object to retrieve the images - if not set, returns images from all users.',
			'type'        => 'string',
		),
	);

	return $args;
}
