<?php
/**
 * Implement a HTTP client error response for the "404 Not Found" status code.
 *
 * The server can not find the requested resource. In the browser, this means the URL is
 * not recognized. In an API, this can also mean that the endpoint is valid but the resource
 * itself does not exist. Servers may also send this response instead of 403 Forbidden to hide
 * the existence of a resource from an unauthorized client. This response code is probably the
 * most well known due to its frequent occurrence on the web.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Responses\Error;

	/**
	 * A WordPress error representing a "404 Not Found" HTTP response.
	 */
	class NotFound extends \WP_Error {

		/**
		 * The HTTP 404 Not Found response status code indicates that the server
		 * cannot find the requested resource. Links that lead to a 404 page are
		 * often called broken or dead links.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = 'Not Found', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$data = array_merge( $data, array( 'status' => 404 ) );

			parent::__construct( $code, $message, $data );
		}
	}
