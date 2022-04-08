<?php
/**
 * Implement a HTTP successful response for the "201 Created" status code.
 *
 * The request succeeded, and a new resource was created as a result. This is typically
 * the response sent after POST requests, or some PUT requests.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#successful_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Responses\Valid;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Created' ) ) {

	/**
	 * A WordPress error representing a "201 Created" HTTP response.
	 */
	class Created extends \WP_REST_Response {

		/**
		 * The HTTP 201 Created success status response code indicates that the request
		 * has succeeded and has led to the creation of a resource. The new resource is
		 * effectively created before this response is sent back and the new resource is
		 * returned in the body of the message, its location being either the URL of the
		 * request, or the content of the Location header. The common use case of this
		 * status code is as the result of a POST request.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/201
		 *
		 * @param mixed $data    Response data. Default null.
		 * @param array $headers Optional. HTTP header map. Default empty array.
		 */
		public function __construct( $data = null, array $headers = array() ) {
			parent::__construct( $data, 201, $headers );
		}
	}
}
