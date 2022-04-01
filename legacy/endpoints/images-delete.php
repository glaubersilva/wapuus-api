<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_image_delete( $request ) {

	$post_id = sanitize_key( $request['id'] );
	$post    = get_post( $post_id );
	$user    = wp_get_current_user();

	if ( (int) $user->ID !== (int) $post->post_author || ! isset( $post ) ) {
		$response = new WP_Error( 'error', 'Sem permissão.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	$attachment_id = get_post_meta( $post_id, 'img', true );
	wp_delete_attachment( $attachment_id, true );
	wp_delete_post( $post_id, true );

	$response = 'Deleted.';
	return rest_ensure_response( $response );
}

function wappus_api_image_delete_permission_callback(){

	return true;
}

function wappus_register_api_image_delete() {

	register_rest_route(
		'wapuus-api/v1',
		'/images/(?P<id>[0-9]+)',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ),			
			array(
				'methods'  => WP_REST_Server::DELETABLE, // DELETE
				'callback' => 'wappus_api_image_delete',
				'permission_callback' => 'wappus_api_image_delete_permission_callback',
				'args' => wappus_api_image_delete_args(),
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_image_delete' );

function wappus_api_image_delete_args(){
	$args = array( 
		'id' => array( 
			'description' => 'The ID of the image to delete.',
			'type'        => 'integer',
			'required'    => true,
		),
	);

	return $args;
}
