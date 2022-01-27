<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function gs_photo_data( $post ){
	
	$post_meta = get_post_meta( $post->ID );
	$src = wp_get_attachment_image_src( $post_meta['img'][0], 'large' )[0];
	$user = get_userdata( $post->post_author );
	$total_comments = get_comments_number( $post->ID );

	$r = array(
		'id' => $post->ID,
		'author' => $user->user_login,
		'title' => $post->post_title,
		'date' => $post->post_date,
		'src' => $src,
		'weight' => $post_meta['weight'][0],
		'age' => $post_meta['age'][0],
		'views' => $post_meta['views'][0],
		'total_comments' => $total_comments,

	);

	return $r;
}

function gs_api_photo_get( $request ) {

	$post_id = sanitize_key( $request['id'] );
	$post = get_post( $post_id );

	if ( ! isset( $post ) || empty( $post_id ) ) {
		$response = new WP_Error( 'error', 'Post não encontrado.', array( 'status' => 404 ) );
		return rest_ensure_response( $response );
	}

	$photo = gs_photo_data( $post );

	$photo['views'] = (int) $photo['views'] + 1;
	update_post_meta( $post_id, 'views', $photo['views'] );

	$comments = get_comments( 
		array(
			'post_id' => $post_id,
			'order'  => 'ASC',
		)
	);
	
	$response = array(
		'photo'    => $photo,
		'comments' => $comments,
	);

	return rest_ensure_response( $response );
}

function gs_register_api_photo_get() {

	register_rest_route(
		'gs-wapuus-api/v1',
		'/photo/(?P<id>[0-9]+)',
		array(
			'methods'  => WP_REST_Server::READABLE, // GET
			'callback' => 'gs_api_photo_get',
		)
	);

}
add_action( 'rest_api_init', 'gs_register_api_photo_get' );








function gs_api_photos_get( $request ) {

	$_total = sanitize_text_field( $request['_total'] ) ?: 6;
	$_page = sanitize_text_field( $request['_page'] ) ?: 1;
	$_user = sanitize_text_field( $request['_user'] ) ?: 0;

	if ( ! is_numeric( $_user ) ){
		$user = get_user_by( 'login', $_user );
		
		if ( ! $user ){
			$response = new WP_Error( 'error', 'Usuário não encontrado.', array( 'status' => 404 ) );
			return rest_ensure_response( $response );
		}
		
		$_user = $user->ID;
	}

	$args = array(
		'post_type' => 'post',
		'author' => $_user,
		'posts_per_page' => $_total,
		'paged' => $_page,
	);

	$query = new WP_Query( $args );
	$posts = $query->posts;
	
	$photos = array();

	if ( $posts ) {
		foreach ( $posts as $post ) {
			$photos[] = gs_photo_data( $post );
		}
	}

	return rest_ensure_response( $photos );
}

function gs_register_api_photos_get() {

	register_rest_route(
		'gs-wapuus-api/v1',
		'/photo',
		array(
			'methods'  => WP_REST_Server::READABLE, // GET
			'callback' => 'gs_api_photos_get',
		)
	);

}
add_action( 'rest_api_init', 'gs_register_api_photos_get' );
