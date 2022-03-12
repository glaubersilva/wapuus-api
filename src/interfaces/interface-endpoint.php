<?php

namespace Wapuus_API\Src\Interfaces;

/**
 * A WordPress REST API endpoint.
 */
interface Endpoint {

	/**
	 * Get the path pattern of the REST API endpoint.
	 *
	 * @return string
	 */
	public function get_path();

	/**
	 * Get the expected arguments for the REST API endpoint.
	 *
	 * @return array
	 */
	public function get_arguments();

	/**
	 * Get the callback used by the REST API endpoint.
	 *
	 * @return callable
	 */
	public function get_callback();

	/**
	 * Get the callback used to validate a request to the REST API endpoint.
	 *
	 * @return callable
	 */
	public function get_permission_callback();

	/**
	 * Get the HTTP methods that the REST API endpoint responds to.
	 *
	 * @return mixed
	 */
	public function get_methods();
}
