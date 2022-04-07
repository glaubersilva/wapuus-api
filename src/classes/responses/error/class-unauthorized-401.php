<?php
/**
 * Implement a HTTP client error response for the "401 Unauthorized" status code.
 *
 * Although the HTTP standard specifies "unauthorized", semantically this response means
 * "unauthenticated". That is, the client must authenticate itself to get the requested response.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Responses\Error;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Unauthorized_401' ) ) {
	/**
	 * A WordPress error representing a "401 Unauthorized" HTTP response.
	 */
	class Unauthorized_401 extends \WP_Error {

		/**
		 * The HyperText Transfer Protocol (HTTP) 401 Unauthorized response status
		 * code indicates that the client request has not been completed because
		 * it lacks valid authentication credentials for the requested resource.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/401
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = 'Unauthorized', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$data = array_merge( $data, array( 'status' => 401 ) );

			parent::__construct( $code, $message, $data );
		}
	}
}
