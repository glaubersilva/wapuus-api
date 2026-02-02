<?php
/**
 * Implement a HTTP successful response for the "200 Ok" status code.
 *
 * The request succeeded. The result meaning of "success" depends on the HTTP method:
 * - GET: The resource has been fetched and transmitted in the message body.
 * - HEAD: The representation headers are included in the response without any message body.
 * - PUT or POST: The resource describing the result of the action is transmitted in the message body.
 * - TRACE: The message body contains the request message as received by the server.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#successful_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Responses\Valid;

	/**
	 * A WordPress error representing a "200 Ok" HTTP response.
	 */
	class Ok extends \WP_REST_Response {

		/**
		 * The HTTP 200 Ok success status response code indicates that the request
		 * has succeeded. A 200 response is cacheable by default.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200
		 *
		 * @param mixed $data    Response data. Default null.
		 * @param array $headers Optional. HTTP header map. Default empty array.
		 */
		public function __construct( $data = null, array $headers = array() ) {
			parent::__construct( $data, 200, $headers );
		}
	}
