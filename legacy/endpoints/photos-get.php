<?php
/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 *
 * @package Wapuus API
 * @author Glauber Silva
 * @link https://glaubersilva.me/
 */

/**
 * Get data from Wapuus posts.
 *
 * @param object|WP_Post|int $post The post object or int.
 */
function wappus_get_post_data( $post ) {

	if ( ! $post instanceof WP_Post && ! is_numeric( $post ) ) {
		return false;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	$post_meta      = get_post_meta( $post->ID );
	$src            = wp_get_attachment_image_src( $post_meta['img'][0], 'large' )[0];
	$user           = get_userdata( $post->post_author );
	$total_comments = get_comments_number( $post->ID );

	$return = array(
		'id'             => $post->ID,
		'author'         => $user->user_login,
		'title'          => $post->post_title,
		'date'           => $post->post_date,
		'src'            => $src,
		'from'           => $post_meta['from'][0],
		'from_url'       => esc_url( $post_meta['from_url'][0] ),
		'caption'        => $post_meta['caption'][0],
		'views'          => $post_meta['views'][0],
		'total_comments' => $total_comments,
	);

	return $return;
}

function wappus_api_photo_get( $request ) {

	$post_id = sanitize_key( $request['id'] );
	$post    = get_post( $post_id );

	if ( ! isset( $post ) || empty( $post_id ) ) {
		$response = new WP_Error( 'error', 'Post não encontrado.', array( 'status' => 404 ) );
		return rest_ensure_response( $response );
	}

	$photo = wappus_get_post_data( $post );

	$photo['views'] = (int) $photo['views'] + 1;
	update_post_meta( $post_id, 'views', $photo['views'] );

	$comments = get_comments(
		array(
			'post_id' => $post_id,
			'order'   => 'ASC',
		)
	);

	$response = array(
		'photo'    => $photo,
		'comments' => $comments,
	);

	return rest_ensure_response( $response );
}

function wappus_api_photo_get_permission_callback() {

	return true;
}

function wappus_register_api_photo_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/photos/(?P<id>[0-9]+)',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Photos_Resource::get_instance(), 'schema' ),			
			array(
				'methods'  => WP_REST_Server::READABLE, // GET
				'callback' => 'wappus_api_photo_get',
				'permission_callback' => 'wappus_api_photo_get_permission_callback',
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_photo_get' );

function wappus_api_photos_get( $request ) {

	$_total = sanitize_text_field( $request['_total'] ) ?: 6;
	$_page  = sanitize_text_field( $request['_page'] ) ?: 1;
	$_user  = sanitize_text_field( $request['_user'] ) ?: 0;

	if ( ! is_numeric( $_user ) ) {
		$user = get_user_by( 'login', $_user );

		if ( ! $user ) {
			$response = new WP_Error( 'error', 'Usuário não encontrado.', array( 'status' => 404 ) );
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

	$photos = array();

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$photos[] = wappus_get_post_data( $post );
		}
	}

	return rest_ensure_response( $photos );
}

function wappus_api_photos_get_permission_callback() {

	return true;
}

function wappus_register_api_photos_get() {

	register_rest_route(
		'wapuus-api/v1',
		'/photos',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Photos_Resource::get_instance(), 'schema' ),			
			array(
				'methods'  => WP_REST_Server::READABLE, // GET
				'callback' => 'wappus_api_photos_get',
				'permission_callback' => 'wappus_api_photos_get_permission_callback',
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_photos_get' );
