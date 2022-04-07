<?php
/**
 * Implement a HTTP client error response for the "403 Forbidden" status code.
 *
 * The client does not have access rights to the content; that is, it is unauthorized,so
 * the server is refusing to give the requested resource. Unlike 401 Unauthorized, the
 * client's identity is known to the server.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Responses\Error;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Forbidden_403' ) ) {
	/**
	 * A WordPress error representing a "403 Forbidden" HTTP response.
	 */
	class Forbidden_403 extends \WP_Error {

		/**
		 * The HTTP 403 Forbidden response status code indicates that the server
		 * understands the request but refuses to authorize it. This status is
		 * similar to 401, but for the 403 Forbidden status code re-authenticating
		 * makes no difference. The access is permanently forbidden and tied to
		 * the application logic, such as insufficient rights to a resource.
		 *
		 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403
		 *
		 * @param string     $message Error message.
		 * @param string|int $code Error code.
		 * @param mixed      $data Optional. Error data.
		 */
		public function __construct( $message = '', $code = 'Forbidden', $data = '' ) {

			if ( ! is_array( $data ) ) {
				$data = (array) $data;
			}

			$data = array_merge( $data, array( 'status' => 403 ) );

			parent::__construct( $code, $message, $data );
		}
	}
}
