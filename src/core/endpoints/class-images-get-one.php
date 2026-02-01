<?php
/**
 * The API V2 endpoint for "image get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Core\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Core\Schemas\Images_Resource;

if ( ! class_exists( 'Images_Get_One' ) ) {

	/**
	 * The "image get" endpoint class.
	 */
	class Images_Get_One extends Abstract_Endpoint {

		/**
		 * Route for the "image get" endpoint.
		 */
		public function get_path() {
			return '/' . Images_Resource::get_instance()->name() . '/(?P<id>[0-9]+)';
		}

		/**
		 * Resource schema callback for the "image get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return Images_Resource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "image get" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "image get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array(
				'id' => array(
					'description' => __( 'The ID of the image to retrieve.', 'wapuus-api' ),
					'type'        => 'integer',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "image get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "image get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$post_id = absint( $request['id'] );
			$post    = get_post( $post_id );

			if ( ! isset( $post ) || empty( $post_id ) ) {
				$response = new \Wapuus_API\Src\Core\Responses\Error\Not_Found( __( 'Image not found.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$image          = wapuus_api_get_post_data( $post );
			$image['views'] = (int) $image['views'] + 1;

			update_post_meta( $post_id, 'views', $image['views'] );

			$comments = get_comments(
				array(
					'post_id' => $post_id,
					'order'   => 'ASC',
				)
			);

			foreach ( $comments as $key => $comment ) {
				$comments[ $key ] = wapuus_api_get_comment_data( $comment );
			}

			$image = array(
				'image'    => $image,
				'comments' => $comments,
			);

			$response = new \Wapuus_API\Src\Core\Responses\Valid\OK( $image );

			return rest_ensure_response( $response );
		}
	}
}
