<?php
/**
 * Implement a HTTP client error response for the "406 Not Acceptable" status code.
 *
 * This response is sent when the web server, after performing server-driven content negotiation,
 * doesn't find any content that conforms to the criteria given by the user agent.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Responses\Error;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Not_Acceptable' ) ) {
	/**
	 * A WordPress error representing a "406 Not Acceptable" HTTP response.
	 */
	class Not_Acceptable extends \WP_Error {

		/**
		 * The HyperText Transfer Protocol (HTTP) 406 Not Acceptable client error response
		 * code indicates that the server cannot produce a response matching the list of
		 * acceptable values defined in the request's proactive content negotiation headers,
		 * and that the server is unwilling to supply a default representation.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = 'Not Acceptable', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$data = array_merge( $data, array( 'status' => 406 ) );

			parent::__construct( $code, $message, $data );
		}
	}
}
