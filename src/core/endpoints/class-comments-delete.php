<?php
/**
 * The API V2 endpoint for "comment delete".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Core\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Core\Schemas\Comments_Resource;

if ( ! class_exists( 'Comments_Delete' ) ) {

	/**
	 * The "comment delete" endpoint class.
	 */
	class Comments_Delete extends Abstract_Endpoint {

		/**
		 * Route for the "comment delete" endpoint.
		 */
		public function get_path() {
			return '/' . Comments_Resource::get_instance()->name() . '/(?P<id>[0-9]+)';
		}

		/**
		 * Resource schema callback for the "comment delete" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return Comments_Resource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "comment delete" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::DELETABLE;
		}

		/**
		 * Schema of the expected arguments for the "comment delete" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array(
				'id' => array(
					'description' => __( 'The ID of the comment to delete.', 'wapuus-api' ),
					'type'        => 'integer',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "comment delete" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			$user       = wp_get_current_user();
			$comment_id = absint( $request['id'] );
			$comment    = get_comment( $comment_id );

			if ( (int) $user->ID !== (int) $comment->user_id || ! isset( $comment ) ) {
				$response = new \Wapuus_API\Src\Core\Responses\Error\No_Permission( __( 'User does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( wapuus_api_is_demo_user( $user ) ) {
				$response = new \Wapuus_API\Src\Core\Responses\Error\No_Permission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			return true;
		}

		/**
		 * Callback for the "comment delete" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$comment_id = absint( $request['id'] );
			$deleted    = wp_delete_comment( $comment_id, true );

			if ( $deleted ) {
				$response = new \Wapuus_API\Src\Core\Responses\Valid\OK( true );
			} else {
				$response = new \Wapuus_API\Src\Core\Responses\Error\Bad_Request( false );
			}

			return rest_ensure_response( $response );
		}
	}
}
