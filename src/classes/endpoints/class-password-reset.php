<?php
/**
 * The API V2 endpoint for "password reset".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Classes\Endpoints\Abstract_Endpoint;

if ( ! class_exists( 'Password_Reset' ) ) {

	/**
	 * The "password reset" endpoint class.
	 */
	class Password_Reset extends Abstract_Endpoint {

		/**
		 * Route for the "password reset" endpoint.
		 */
		public function get_path() {
			return '/password/reset';
		}

		/**
		 * Resource schema callback for the "password reset" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return array();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "password reset" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "password reset" endpoint.
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
		 * Permission callback for the "password reset" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "password reset" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$response = new \Wapuus_API\Src\Classes\Responses\Valid\OK();

			return rest_ensure_response( $response );
		}
	}
}
