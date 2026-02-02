<?php
/**
 * Automatically decides which "no permission" HTTP client error response (401 or 403) should be returned.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Responses\Error;

	/**
	 * A WordPress error representing a "401 Unauthorized" or a "403 Forbidden" HTTP response.
	 */
	class NoPermission extends \WP_Error {

		/**
		 * The 401 Unauthorized response status code indicates that the client
		 * request has not been completed because it lacks valid authentication
		 * credentials for the requested resource.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/401
		 *
		 * The HTTP 403 Forbidden response status code indicates that the server
		 * understands the request but refuses to authorize it. Unlike 401 Unauthorized,
		 * the client's identity is known to the server.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = '', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			if ( is_user_logged_in() ) {
				$data = array_merge( $data, array( 'status' => 403 ) );

				if ( empty( $code ) ) {
					$code = 'Forbidden';
				}
			} else {
				$data = array_merge( $data, array( 'status' => 401 ) );

				if ( empty( $code ) ) {
					$code = 'Unauthorized';
				}
			}

			parent::__construct( $code, $message, $data );
		}
	}
