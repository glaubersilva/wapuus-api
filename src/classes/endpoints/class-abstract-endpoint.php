<?php

namespace Wapuus_API\Src\Classes\Endpoints;

use Wapuus_API\Src\Interfaces\Endpoint;

/**
 * Base class for WordPress REST API endpoints.
 */
abstract class Abstract_Endpoint implements Endpoint {

	/**
	 * Get the callback used by the REST API endpoint.
	 *
	 * @return callable
	 */
	final public function get_schema() {
		return array( $this, 'resource_schema' );
	}

	/**
	 * Get the callback used by the REST API endpoint.
	 *
	 * @return callable
	 */
	final public function get_callback() {
		return array( $this, 'respond' );
	}

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
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed
	 */
	abstract public function respond( \WP_REST_Request $request );

	/**
	 * Desc.
	 *
	 * @return mixed
	 */
	abstract public function check_permissions();
}
