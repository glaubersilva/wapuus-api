<?php
/**
 * The API V2 endpoint for "user get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Core\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Core\Schemas\Users_Resource;

if ( ! class_exists( 'Users_Get' ) ) {

	/**
	 * The "user get" endpoint class.
	 */
	class Users_Get extends Abstract_Endpoint {

		/**
		 * Route for the "user get" endpoint.
		 */
		public function get_path() {
			return '/' . Users_Resource::get_instance()->name();
		}

		/**
		 * Resource schema callback for the "user get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return Users_Resource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "user get" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "user get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array();

			return $args;
		}

		/**
		 * Permission callback for the "user get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			if ( ! is_user_logged_in() ) {
				$response = new \Wapuus_API\Src\Core\Responses\Error\No_Permission( __( 'User does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			return true;
		}

		/**
		 * Callback for the "user get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$user = wp_get_current_user();

			$user = array(
				'id'       => $user->ID,
				'username' => $user->user_login,
				'email'    => $user->user_email,
			);

			$response = new \Wapuus_API\Src\Core\Responses\Valid\OK( $user );

			return rest_ensure_response( $response );
		}
	}
}
