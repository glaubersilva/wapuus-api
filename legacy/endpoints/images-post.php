<?php
/**
 * The legacy API V1 endpoint for "image post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

/**
 * Register the "image post" endpoint.
 */
function wappus_register_api_image_post() {

	register_rest_route(
		'wapuus-api/v1',
		'/images',
		array( // The callback to the endpoint resource schema - note that the resource schema is the same for all methods that the endpoint accepts.
			'schema' => array( \Wapuus_API\Src\Classes\Schemas\Images_Resource::get_instance(), 'schema' ),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'wappus_api_image_post',
				'permission_callback' => 'wappus_api_image_post_permissions_check',
				'args'                => wappus_api_image_post_args(),
			),
			// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
		)
	);

}
add_action( 'rest_api_init', 'wappus_register_api_image_post' );

/**
 * The callback to post an image.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return WP_REST_Response|WP_Error If response generated an error, WP_Error, if response
 *                                   is already an instance, WP_REST_Response, otherwise
 *                                   returns a new WP_REST_Response instance.
 */
function wappus_api_image_post( $request ) {

	$user  = wp_get_current_user();
	$files = $request->get_file_params();
	$name  = sanitize_text_field( $request['name'] );

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
		$image_id = media_handle_upload( 'img', $post_id ); // Should be used for file uploads (input file field).

	} else {

		/**
		 * Handle sideloads, which is the process of retrieving a media item from another server instead of a traditional media upload.
		 *
		 * Definition of sideload: (2) Copying a file from a site on the Internet to the user's account in an online storage service, rather than downloading it directly to the user's computer.
		 * More details here: https://www.pcmag.com/encyclopedia/term/sideload
		 *
		 * This is necessary to get the upload done - escaping the is_uploaded_file() verification - in cases where we are testing our endpoint via PHPUnit.
		 */
		$image_id = media_handle_sideload( $files['img'], $post_id ); // Should be used for remote file uploads (input text field).

	}

	update_post_meta( $post_id, 'img', $image_id );
	set_post_thumbnail( $post_id, $image_id );

	$response = wappus_api_get_post_data( $post_id );

	return rest_ensure_response( $response );
}

/**
 * The permission callback to post an image.
 *
 * @param WP_REST_Request $request The current request object.
 *
 * @return true|WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
 */
function wappus_api_image_post_permissions_check( $request ) {

	$user  = wp_get_current_user();
	$files = $request->get_file_params();
	$name  = sanitize_text_field( $request['name'] );

	/**
	 * To better understand the "client error responses", check the link below:
	 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
	 */
	if ( is_user_logged_in() ) {
		$no_permission_status = 403;
		$no_permission_code   = 'Forbidden';
	} else {
		$no_permission_status = 401;
		$no_permission_code   = 'Unauthorized';
	}

	$not_acceptable_status = 406;
	$not_acceptable_code   = 'Not Acceptable';

	$incomplete_data_status = 422;
	$incomplete_data_code   = 'Unprocessable Entity';

	$unsupported_media_type_status = 415;
	$unsupported_media_type_code   = 'Unsupported Media Type';

	if ( 0 === $user->ID ) {
		$response = new WP_Error( $no_permission_code, __( 'User does not have permission.', 'wapuus-api' ), array( 'status' => $no_permission_status ) );
		return rest_ensure_response( $response );
	}

	if ( wapuus_api_is_demo_user( $user ) ) {
		$response = new WP_Error( $no_permission_code, __( 'Demo user does not have permission.', 'wapuus-api' ), array( 'status' => $no_permission_status ) );
		return rest_ensure_response( $response );
	}

	if ( empty( $name ) || empty( $files ) ) {
		$response = new WP_Error( $incomplete_data_code, __( 'Image and name are required.', 'wapuus-api' ), array( 'status' => $incomplete_data_status ) );
		return rest_ensure_response( $response );
	}

	$allowed_image_types = array(
		'jpg'  => 'image/jpg',
		'jpeg' => 'image/jpeg',
		'png'  => 'image/png',
	);

	if ( ! in_array( strtolower( $files['img']['type'] ), $allowed_image_types, true ) ) {
		$response = new WP_Error( $unsupported_media_type_code, __( 'Invalide Image.', 'wapuus-api' ), array( 'status' => $unsupported_media_type_status ) );
		return rest_ensure_response( $response );
	}

	$file_size = $files['img']['size']; // In bytes.

	/**
	 * Convert Bytes to Megabytes
	 * https://www.php.net/manual/pt_BR/function.filesize.php#112996
	 */
	$file_size = round( $file_size / pow( 1024, 2 ), 2 );

	if ( $file_size > 1 ) {
		$response = new WP_Error( $not_acceptable_code, __( 'The image is greater than 1MB - the maximum size allowed.', 'wapuus-api' ), array( 'status' => $not_acceptable_status ) );
		return rest_ensure_response( $response );
	}

	$img_size   = getimagesize( $files['img']['tmp_name'] );
	$img_width  = $img_size[0];
	$img_height = $img_size[1];

	if ( $img_width < 1000 || $img_height < 1000 ) {
		$response = new WP_Error( $not_acceptable_code, __( 'The image should have at least 1000px X 1000px of dimensions.', 'wapuus-api' ), array( 'status' => $not_acceptable_status ) );
		return rest_ensure_response( $response );
	}

	return true;
}

/**
 * Get the expected arguments for the REST API endpoint.
 *
 * @return array Arguments.
 */
function wappus_api_image_post_args() {
	$args = array(
		'name'     => array(
			'description' => __( 'The name of the image.', 'wapuus-api' ),
			'type'        => 'string',
			'required'    => true,
		),
		'img'      => array(
			'description' => __( 'The image file - it should be sent to the API through an input file field in your form', 'wapuus-api' ),
			'type'        => 'string',
			'media'       => array( // https://datatracker.ietf.org/doc/html/draft-luff-json-hyper-schema-00#section-4.3 <<< Reference.
				'required' => true,
			),
			'required'    => false,
		),
		'from'     => array(
			'description' => __( 'The source of the image.', 'wapuus-api' ),
			'type'        => 'string',
		),
		'from_url' => array(
			'description' => __( 'URL to the source of the image.', 'wapuus-api' ),
			'type'        => 'string',
			'format'      => 'uri',
		),
		'caption'  => array(
			'description' => __( 'The caption of the image.', 'wapuus-api' ),
			'type'        => 'string',
		),
	);

	return $args;
}
