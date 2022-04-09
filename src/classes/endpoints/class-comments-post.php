<?php
/**
 * The API V2 endpoint for "comment post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Classes\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Classes\Schemas\Comments_Resource;

if ( ! class_exists( 'Comments_Post' ) ) {

	/**
	 * The "comment post" endpoint class.
	 */
	class Comments_Post extends Abstract_Endpoint {

		/**
		 * Route for the "comment post" endpoint.
		 */
		public function get_path() {
			return '/' . Comments_Resource::get_instance()->name();
		}

		/**
		 * Resource schema callback for the "comment post" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return Comments_Resource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "comment post" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "comment post" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array(
				'id'      => array(
					'description' => __( 'The ID of the image object that will receive the comment.', 'wapuus-api' ),
					'type'        => 'integer',
					'required'    => true,
				),
				'comment' => array(
					'description' => __( 'The content of the comment.', 'wapuus-api' ),
					'type'        => 'string',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "comment post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			if ( ! is_user_logged_in() ) {
				$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'User does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( wapuus_api_is_demo_user( get_current_user_id() ) ) {
				$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			return true;
		}

		/**
		 * Callback for the "comment post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

				$comment = sanitize_textarea_field( $request['comment'] );

			if ( empty( $comment ) ) {
				$response = new \Wapuus_API\Src\Classes\Responses\Error\Incomplete_Data( __( 'The comment is required.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$user    = wp_get_current_user();
			$post_id = sanitize_key( $request['id'] );

			$new_wp_comment = array(
				'user_id'         => $user->ID,
				'comment_author'  => $user->user_login,
				'comment_content' => $comment,
				'comment_post_ID' => $post_id,
			);

			$comment_id = wp_insert_comment( $new_wp_comment );
			$comment    = get_comment( $comment_id );
			$comment    = wapuus_api_get_comment_data( $comment );

			$response = new \Wapuus_API\Src\Classes\Responses\Valid\Created( $comment );

			return rest_ensure_response( $response );
		}
	}
}
