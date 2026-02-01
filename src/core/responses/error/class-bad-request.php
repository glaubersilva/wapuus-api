<?php
/**
 * Implement a HTTP client error response for the "400 Bad Request" status code.
 *
 * The server cannot or will not process the request due to something that is perceived
 * to be a client error (e.g., malformed request syntax, invalid request message framing,
 * or deceptive request routing).
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Responses\Error;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Bad_Request' ) ) {
	/**
	 * A WordPress error representing a "400 Bad Request" HTTP response.
	 */
	class Bad_Request extends \WP_Error {

		/**
		 * The HyperText Transfer Protocol (HTTP) 400 Bad Request response status code indicates
		 * that the server cannot or will not process the request due to something that is perceived
		 * to be a client error (for example, malformed request syntax, invalid request message
		 * framing, or deceptive request routing).
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/400
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = 'Bad Request', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$data = array_merge( $data, array( 'status' => 400 ) );

			parent::__construct( $code, $message, $data );
		}
	}
}
