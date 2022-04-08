<?php
/**
 * Implement a HTTP client error response for the "415 Unsupported Media Type" status code.
 *
 * The media format of the requested data is not supported by the server, so the server is rejecting the request.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Responses\Error;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Unsupported_Media_Type' ) ) {
	/**
	 * A WordPress error representing a "415 Unsupported Media Type" HTTP response.
	 */
	class Unsupported_Media_Type extends \WP_Error {

		/**
		 * The HTTP 415 Unsupported Media Type client error response code indicates that the server
		 * refuses to accept the request because the payload format is in an unsupported format. The
		 * format problem might be due to the request's indicated Content-Type or Content-Encoding,
		 * or as a result of inspecting the data directly.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/415
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = 'Unsupported Media Type', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$data = array_merge( $data, array( 'status' => 415 ) );

			parent::__construct( $code, $message, $data );
		}
	}
}
