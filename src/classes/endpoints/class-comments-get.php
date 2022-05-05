<?php
/**
 * The API V2 endpoint for "comment get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Classes\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Classes\Schemas\Comments_Resource;

if ( ! class_exists( 'Comments_Get' ) ) {

	/**
	 * The "comment get" endpoint class.
	 */
	class Comments_Get extends Abstract_Endpoint {

		/**
		 * Route for the "comment get" endpoint.
		 */
		public function get_path() {
			return '/' . Comments_Resource::get_instance()->name() . '/(?P<id>[0-9]+)';
		}

		/**
		 * Resource schema callback for the "comment get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return Comments_Resource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "comment get" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "comment get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array(
				'id' => array(
					'description' => __( 'The ID of the image object to retrieve the comments.', 'wapuus-api' ),
					'type'        => 'integer',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "comment get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "comment get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$post_id = absint( $request['id'] );

			$comments = get_comments(
				array(
					'post_id' => $post_id,
				)
			);

			foreach ( $comments as $key => $comment ) {
				$comments[ $key ] = wapuus_api_get_comment_data( $comment );
			}

			$response = new \Wapuus_API\Src\Classes\Responses\Valid\OK( $comments );

			return rest_ensure_response( $response );
		}
	}
}
