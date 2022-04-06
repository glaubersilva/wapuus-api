<?php
/**
 * Implements the callbacks that our "Endpoint" interface defines, but as we can't explicitly set
 * these methods as "callable" values on the interface, we need to use this base class for that.
 *
 * The WordPress REST API expects a callback to be a callable value, that means:
 * >>> a string with the function name <<< that we want the WordPress REST API to call.
 *
 * @link https://www.php.net/manual/en/language.types.callable.php
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Interfaces\Endpoint;

if ( ! class_exists( 'Abstract_Endpoint' ) ) {

	/**
	 * Base class for WordPress REST API endpoints.
	 */
	abstract class Abstract_Endpoint implements Endpoint {

		/**
		 * Defines the schema for the resource - comment, image, user, etc. - that the endpoint handle.
		 *
		 * The abstract keyword force extending classes to define this method that
		 * is used as a callback on the get_schema() method from this base class.
		 *
		 * @return array
		 */
		abstract public function resource_schema();

		/**
		 * Get the schema callback used by the REST API endpoint.
		 *
		 * @return callable
		 */
		final public function get_schema() {
			return array( $this, 'resource_schema' );
		}

		/**
		 * Check the permissions to the REST API endpoint.
		 *
		 * The abstract keyword force extending classes to define this method that
		 * is used as a callback on the get_permission_callback() method from this base class.
		 *
		 *  @param \WP_REST_Request $request The current request object.
		 *
		 * @return mixed
		 */
		abstract public function check_permissions( \WP_REST_Request $request );

		/**
		 * Get the permission callback used by the REST API endpoint.
		 *
		 * @return callable
		 */
		final public function get_permission_callback() {
			return array( $this, 'check_permissions' );
		}

		/**
		 * Respond to a request to the REST API endpoint.
		 *
		 * The abstract keyword force extending classes to define this method that
		 * is used as a callback on the get_callback() method from this base class.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return mixed
		 */
		abstract public function respond( \WP_REST_Request $request );

		/**
		 * Get the callback used by the REST API endpoint.
		 *
		 * @return callable
		 */
		final public function get_callback() {
			return array( $this, 'respond' );
		}
	}
}
