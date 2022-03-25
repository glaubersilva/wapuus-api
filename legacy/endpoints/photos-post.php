<?php

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */

function wappus_api_photo_post( $request ) {

	$user = wp_get_current_user();

	if ( 0 === $user->ID ) {
		$response = new WP_Error( 'error', 'User does not have permission.', array( 'status' => 401 ) );
		return rest_ensure_response( $response );
	}

	$files = $request->get_file_params();

	$name  = sanitize_text_field( $request['name'] );

	if ( empty( $name ) || empty( $files ) ) {
		$response = new WP_Error( 'error', 'Image and name are required.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	$allowed_image_types = array(
		'jpg'  => 'image/jpg',
		'jpeg' => 'image/jpeg',
		'png'  => 'image/png',
	);

	if ( ! in_array( strtolower( $files['img']['type'] ), $allowed_image_types, true ) ) {
		$response = new WP_Error( 'error', 'Invalide Image.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	$file_size = $files['img']['size']; // In bytes.

	/**
	 * Convert Bytes to Megabytes
	 * https://www.php.net/manual/pt_BR/function.filesize.php#112996
	 */
	$file_size = round( $file_size / pow( 1024, 2 ), 2 );

	if ( $file_size > 1 ) {
		$response = new WP_Error( 'error', 'The image is greater than 1MB - the maximum size allowed.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	$img_size   = getimagesize( $files['img']['tmp_name'] );
	$img_width  = $img_size[0];
	$img_height = $img_size[1];

	if ( $img_width < 1000 || $img_height < 1000 ) {
		$response = new WP_Error( 'error', 'The image should have at least 1000px X 1000px of dimensions.', array( 'status' => 422 ) );
		return rest_ensure_response( $response );
	}

	$from     = sanitize_text_field( $request['from'] );
	$from_url = esc_url_raw( $request['from_url'] );
	$caption  = sanitize_textarea_field( $request['caption'] );

	if ( empty( $from ) ) {
		$from = 'Unknown';
	}

	if ( empty( $from_url ) ) {
		$from_url = '#';
	}

	$post = array(
		'post_author' => $user->ID,
		'post_type'   => 'wapuu',
		'post_status' => 'publish',
		'post_title'  => $name,
		'files'       => $files,
		'meta_input'  => array(
			'from'     => $from,
			'from_url' => $from_url,
			'caption'  => substr( $caption, 0, 150 ),
			'views'    => 0,
		),
	);

	$post_id = wp_insert_post( $post );

	// These files need to be included as dependencies when on the front end.
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	if ( ! empty( $_FILES ) ) {
		/**
		 * If $_FILES is not empty it means that the data from $files['img'] come by an "input file field" from a form.
		 *
		 * So we need to use the media_handle_upload() function because it will use the PHP is_uploaded_file() method to check if the file on the $_FILES is valid.
		 */
		$photo_id = media_handle_upload( 'img', $post_id ); // Should be used for file uploads (input file field).
	} else {
		/**
		 * Handle sideloads, which is the process of retrieving a media item from another server instead of a traditional media upload.
		 *
		 * Definition of sideload: (2) Copying a file from a site on the Internet to the user's account in an online storage service, rather than downloading it directly to the user's computer.
		 * More details here: https://www.pcmag.com/encyclopedia/term/sideload
		 *
		 * This is necessary to get the upload done - escaping the is_uploaded_file() verification - in cases where we are testing our endpoint via PHPUnit.
		 */
		$photo_id = media_handle_sideload( $files['img'], $post_id ); // Should be used for remote file uploads (input text field).
	}

	update_post_meta( $post_id, 'img', $photo_id );
	set_post_thumbnail( $post_id, $photo_id );

	$response = $post;

	return rest_ensure_response( $response );
}

function wappus_register_api_photo_post_permission_callback() {

	return true;
}

function wappus_register_api_photo_post() {

	register_rest_route(
		'wapuus-api/v1',
		'/photos',
		array( // Isso declara o Schema do endpoint. Note que o schema é o mesmo para todos os métodos que o endpoint aceita.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Photos_Resource::get_instance(), 'schema' ),
			array(
				'methods'             => WP_REST_Server::CREATABLE, // POST
				'callback'            => 'wappus_api_photo_post',
				'permission_callback' => 'wappus_register_api_photo_post_permission_callback',
			),
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_photo_post' );
