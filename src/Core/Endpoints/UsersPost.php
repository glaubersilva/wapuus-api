<?php
/**
 * The API V2 endpoint for "user post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Core\Endpoints\AbstractEndpoint;
use WapuusApi\Core\Schemas\UsersResource;
	/**
	 * The "user post" endpoint class.
	 */
	class UsersPost extends AbstractEndpoint {

		/**
		 * Route for the "user post" endpoint.
		 */
		public function getPath() {
			return '/' . UsersResource::getInstance()->getName();
		}

		/**
		 * Resource schema callback for the "user post" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resourceSchema() {
			return UsersResource::getInstance()->getSchema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "user post" endpoint.
		 */
		public function getMethods() {
			return \WP_REST_Server::CREATABLE;
		}

		/**
		 * Schema of the expected arguments for the "user post" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function getArguments() {

			$args = array(
				'username' => array(
					'description' => __( 'Login name for the user.', 'wapuus-api' ),
					'type'        => 'string',
					'required'    => true,
				),
				'email'    => array(
					'description' => __( 'The email address for the user.', 'wapuus-api' ),
					'type'        => 'string',
					'format'      => 'email',
					'required'    => true,
				),
				'url'      => array(
					'description' => __( 'Base URL used to create the "password creation" link that is sent by email.', 'wapuus-api' ),
					'type'        => 'string',
					'format'      => 'uri',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "user post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function checkPermissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "user post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$email    = sanitize_email( $request['email'] );
			$username = sanitize_user( $request['username'] );

			if ( empty( $email ) || empty( $username ) ) {
				$response = new \WapuusApi\Core\Responses\Error\IncompleteData( __( 'Email and username are required.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( username_exists( $username ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NotAcceptable( __( 'Username already in use.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( email_exists( $email ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NotAcceptable( __( 'Email already in use.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$url = $request['url'];

			$userId = wp_insert_user(
				array(
					'user_login' => $username,
					'user_email' => $email,
					'user_pass'  => wp_generate_password(),
					'role'       => 'subscriber',
				)
			);

			if ( $userId && ! is_wp_error( $userId ) ) {

				$user    = get_user_by( 'ID', $userId );
				$key     = get_password_reset_key( $user );
				$message = __( 'Use the link below to create your password:', 'wapuus-api' ) . "\r\n";
				$url     = esc_url_raw( $url . "/?key=$key&login=" . rawurlencode( $username ) . "\r\n" );
				$body    = $message . $url;

				wp_mail( $email, __( 'Password Creation', 'wapuus-api' ), $body );
			}

			$user = array(
				'id'       => $userId,
				'username' => $username,
				'email'    => $email,
			);

			$response = new \WapuusApi\Core\Responses\Valid\Created( $user );

			return rest_ensure_response( $response );
		}
	}
