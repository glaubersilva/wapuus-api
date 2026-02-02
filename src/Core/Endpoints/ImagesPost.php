<?php
/**
 * The API V2 endpoint for "image post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Core\Endpoints\AbstractEndpoint;
use WapuusApi\Core\Schemas\ImagesResource;
use WapuusApi\Helpers;
	/**
	 * The "image post" endpoint class.
	 */
	class ImagesPost extends AbstractEndpoint {

		/**
		 * Route for the "image post" endpoint.
		 */
		public function get_path() {
			return '/' . ImagesResource::get_instance()->name();
		}

		/**
		 * Resource schema callback for the "image post" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return ImagesResource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "image post" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::CREATABLE;
		}

		/**
		 * Schema of the expected arguments for the "image post" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

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

		/**
		 * Permission callback for the "image post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			if ( ! is_user_logged_in() ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'User does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( Helpers::isDemoUser( get_current_user_id() ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			return true;
		}

		/**
		 * Callback for the "image post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$files = $request->get_file_params();
			$name  = sanitize_text_field( $request['name'] );

			if ( empty( $name ) || ! isset( $files['img'] ) ) {
				$response = new \WapuusApi\Core\Responses\Error\IncompleteData( __( 'Image and name are required.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$files['img']['name'] = sanitize_file_name( $files['img']['name'] );
			$files['img']['type'] = sanitize_mime_type( $files['img']['type'] );

			$allowed_image_types = array(
				'jpg'  => 'image/jpg',
				'jpeg' => 'image/jpeg',
				'png'  => 'image/png',
			);

			if ( ! in_array( strtolower( $files['img']['type'] ), $allowed_image_types, true ) ) {
				$response = new \WapuusApi\Core\Responses\Error\UnsupportedMediaType( __( 'Invalide Image.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$file_size = $files['img']['size']; // In bytes.

			/**
			 * Convert Bytes to Megabytes
			 * https://www.php.net/manual/pt_BR/function.filesize.php#112996
			 */
			$file_size = round( $file_size / pow( 1024, 2 ), 2 );

			if ( $file_size > 1 ) {
				$response = new \WapuusApi\Core\Responses\Error\NotAcceptable( __( 'The image is greater than 1MB - the maximum size allowed.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$img_size   = getimagesize( $files['img']['tmp_name'] );
			$img_width  = $img_size[0];
			$img_height = $img_size[1];

			if ( $img_width < 1000 || $img_height < 1000 ) {
				$response = new \WapuusApi\Core\Responses\Error\NotAcceptable( __( 'The image should have at least 1000px X 1000px of dimensions.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$user     = wp_get_current_user();
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
				 * This is necessary to get the upload done - skipping the is_uploaded_file() verification - in cases where we are testing our endpoint via PHPUnit.
				 */
				$image_id = media_handle_sideload( $files['img'], $post_id ); // Should be used for remote file uploads (input text field).

			}

			update_post_meta( $post_id, 'img', $image_id );
			set_post_thumbnail( $post_id, $image_id );

			$wapuu = Helpers::getPostData( $post_id );

			$response = new \WapuusApi\Core\Responses\Valid\Created( $wapuu );

			return rest_ensure_response( $response );
		}
	}
