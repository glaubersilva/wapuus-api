<?php
/**
 * The API V2 endpoint for "password reset".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Core\Endpoints\AbstractEndpoint;
use WapuusApi\Core\Helpers;
	/**
	 * The "password reset" endpoint class.
	 */
	class PasswordReset extends AbstractEndpoint {

		/**
		 * Route for the "password reset" endpoint.
		 */
		public function getPath() {
			return '/password/reset';
		}

		/**
		 * Resource schema callback for the "password reset" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resourceSchema() {
			return array();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "password reset" endpoint.
		 */
		public function getMethods() {
			return \WP_REST_Server::CREATABLE;
		}

		/**
		 * Schema of the expected arguments for the "password reset" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function getArguments() {

			$args = array(
				'login'    => array(
					'description' => __( 'The username of the user object to reset the password.', 'wapuus-api' ),
					'type'        => 'string',
					'required'    => true,
				),
				'password' => array(
					'description' => __( 'The new password of the user object.', 'wapuus-api' ),
					'type'        => 'string',
					'required'    => true,
				),
				'key'      => array(
					'description' => __( 'The password reset key for the user object.', 'wapuus-api' ),
					'type'        => 'string',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "password reset" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function checkPermissions( \WP_REST_Request $request ) {

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

			$login = sanitize_user( $request['login'] );
			$key   = sanitize_text_field( $request['key'] );
			$user  = get_user_by( 'login', $login );

			if ( empty( $user ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NotFound( __( 'User does not exist.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( Helpers::isDemoUser( $user ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$checkKey = check_password_reset_key( $key, $login );

			if ( is_wp_error( $checkKey ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NotAcceptable( __( 'Expired token.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$password = sanitize_text_field( $request['password'] );
			$user     = get_user_by( 'login', $login );

			reset_password( $user, $password );

			$response = new \WapuusApi\Core\Responses\Valid\Ok( __( 'Password has been changed.', 'wapuus-api' ) );

			return rest_ensure_response( $response );
		}
	}
