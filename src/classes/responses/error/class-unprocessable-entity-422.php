<?php
/**
 * Implement a HTTP client error response for the "422 Unprocessable Entity" status code.
 *
 * The request was well-formed but was unable to be followed due to semantic errors.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Responses\Error;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Unprocessable_Entity_422' ) ) {
	/**
	 * A WordPress error representing a "422 Unprocessable Entity" HTTP response.
	 */
	class Unprocessable_Entity_422 extends \WP_Error {

		/**
		 * The HyperText Transfer Protocol (HTTP) 422 Unprocessable Entity response status code
		 * indicates that the server understands the content type of the request entity, and the
		 * syntax of the request entity is correct, but it was unable to process the contained
		 * instructions. Warning: The client should not repeat this request without modification.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/422
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = 'Unprocessable Entity', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$data = array_merge( $data, array( 'status' => 422 ) );

			parent::__construct( $code, $message, $data );
		}
	}
}
