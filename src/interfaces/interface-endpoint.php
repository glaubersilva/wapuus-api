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
	 * Get the callback used to define the Resource Schema to the REST API endpoint.
	 *
	 * The schema for a resource indicates what fields are present for a particular object.
	 *
	 * Check this link for more details:
	 * https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema
	 *
	 * @return callable
	 */
	public function get_schema();

	/**
	 * Get the expected arguments for the REST API endpoint.
	 *
	 * Here we add our PHP representation of JSON Schema for the endpoint. When we register request arguments
	 * for an endpoint, we can also use JSON Schema to provide us data about what the arguments should be.
	 *
	 * Check this link for more details:
	 * https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
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
