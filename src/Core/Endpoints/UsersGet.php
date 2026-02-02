<?php
/**
 * The API V2 endpoint for "user get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Core\Endpoints\AbstractEndpoint;
use WapuusApi\Core\Schemas\UsersResource;
	/**
	 * The "user get" endpoint class.
	 */
	class UsersGet extends AbstractEndpoint {

		/**
		 * Route for the "user get" endpoint.
		 */
		public function getPath() {
			return '/' . UsersResource::getInstance()->getName();
		}

		/**
		 * Resource schema callback for the "user get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resourceSchema() {
			return UsersResource::getInstance()->getSchema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "user get" endpoint.
		 */
		public function getMethods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "user get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function getArguments() {

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
		public function checkPermissions( \WP_REST_Request $request ) {

			if ( ! is_user_logged_in() ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'User does not have permission.', 'wapuus-api' ) );
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

			$currentUser = wp_get_current_user();

			$user = array(
				'id'       => $currentUser->ID,
				'username' => $currentUser->user_login,
				'email'    => $currentUser->user_email,
			);

			$response = new \WapuusApi\Core\Responses\Valid\Ok( $user );

			return rest_ensure_response( $response );
		}
	}
