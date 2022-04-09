<?php
/**
 * The API V2 endpoint for "password lost".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Classes\Endpoints\Abstract_Endpoint;

if ( ! class_exists( 'Password_Lost' ) ) {

	/**
	 * The "password lost" endpoint class.
	 */
	class Password_Lost extends Abstract_Endpoint {

		/**
		 * Route for the "password lost" endpoint.
		 */
		public function get_path() {
			return '/password/lost';
		}

		/**
		 * Resource schema callback for the "password lost" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return array();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "password lost" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::CREATABLE;
		}

		/**
		 * Schema of the expected arguments for the "password lost" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array(
				'login' => array(
					'description' => __( 'The username of the user object to reset the password.', 'wapuus-api' ),
					'type'        => 'string',
					'required'    => true,
				),
				'url'   => array(
					'description' => __( 'Base URL used to create the "reset password" link that is sent by email.', 'wapuus-api' ),
					'type'        => 'string',
					'format'      => 'uri',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "password lost" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "password lost" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$login = $request['login'];

			if ( empty( $login ) ) {
				$response = new \Wapuus_API\Src\Classes\Responses\Error\Incomplete_Data( __( 'Email or username are required.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$user = get_user_by( 'email', $login );

			if ( empty( $user ) ) {
				$user = get_user_by( 'login', $login );
			}

			if ( empty( $user ) ) {
				$response = new \Wapuus_API\Src\Classes\Responses\Error\Not_Found( __( 'User does not exist.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( wapuus_api_is_demo_user( $user ) ) {
				$response = new \Wapuus_API\Src\Classes\Responses\Error\No_Permission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$url  = $request['url'];
			$user = get_user_by( 'email', $login );

			if ( empty( $user ) ) {
				$user = get_user_by( 'login', $login );
			}

			$user_login = $user->user_login;
			$user_email = $user->user_email;
			$key        = get_password_reset_key( $user );
			$message    = __( 'Use the link below to reset your password:', 'wapuus-api' ) . "\r\n";
			$url        = esc_url_raw( $url . "/?key=$key&login=" . rawurlencode( $user_login ) . "\r\n" );
			$body       = $message . $url;

			wp_mail( $user_email, __( 'Password Reset', 'wapuus-api' ), $body );

			$response = new \Wapuus_API\Src\Classes\Responses\Valid\OK( __( 'Email sent.', 'wapuus-api' ) );

			return rest_ensure_response( $response );
		}
	}
}
