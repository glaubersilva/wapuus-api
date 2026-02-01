<?php
/**
 * The API V2 endpoint for "images get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Core\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Core\Schemas\Images_Resource;

if ( ! class_exists( 'Images_Get' ) ) {

	/**
	 * The "images get" endpoint class.
	 */
	class Images_Get extends Abstract_Endpoint {

		/**
		 * Route for the "images get" endpoint.
		 */
		public function get_path() {
			return '/' . Images_Resource::get_instance()->name();
		}

		/**
		 * Resource schema callback for the "images get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return Images_Resource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "images get" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "images get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array(
				'_total' => array(
					'description' => __( 'Total number of images per page - if not set, the default value is 6.', 'wapuus-api' ),
					'type'        => 'integer',
				),
				'_page'  => array(
					'description' => __( 'The number of the page to retrieve - if not set, the default value is 1.', 'wapuus-api' ),
					'type'        => 'integer',
				),
				'_user'  => array(
					'description' => __( 'The ID or username of the user object to retrieve the images - if not set, returns images from all users.', 'wapuus-api' ),
					'type'        => 'string',
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "images get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "images get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			if ( isset( $request['_user'] ) && ! is_numeric( $request['_user'] ) ) {

				$user = get_user_by( 'login', sanitize_user( $request['_user'] ) );

				if ( ! $user ) {

					$response = new \Wapuus_API\Src\Core\Responses\Error\Not_Found( __( 'User not found.', 'wapuus-api' ) );
					return rest_ensure_response( $response );
				}
			}

			$_total = isset( $request['_total'] ) ? sanitize_text_field( $request['_total'] ) : 6;
			$_page  = isset( $request['_page'] ) ? sanitize_text_field( $request['_page'] ) : 1;
			$_user  = isset( $request['_user'] ) ? sanitize_user( $request['_user'] ) : 0;

			if ( ! is_numeric( $_user ) ) {
				$user  = get_user_by( 'login', $_user );
				$_user = $user->ID;
			}

			$args = array(
				'post_type'      => 'wapuu',
				'author'         => $_user,
				'posts_per_page' => $_total,
				'paged'          => $_page,
			);

			$query = new \WP_Query( $args );
			$posts = $query->posts;

			$images = array();

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$images[] = wapuus_api_get_post_data( $post );
				}
			}

			$response = new \Wapuus_API\Src\Core\Responses\Valid\OK( $images );

			return rest_ensure_response( $response );
		}
	}
}
