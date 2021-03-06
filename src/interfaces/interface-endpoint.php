<?php
/**
 * Contract used to implement new endpoints.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Interfaces;

defined( 'ABSPATH' ) || exit;

if ( ! interface_exists( 'Endpoint' ) ) {

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
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema
		 *
		 * @return callable
		 */
		public function get_schema();

		/**
		 * Get the HTTP methods that the REST API endpoint responds to.
		 *
		 * @return mixed
		 */
		public function get_methods();

		/**
		 * Get the expected arguments for the REST API endpoint.
		 *
		 * Here we add our PHP representation of JSON Schema for the endpoint. When we register request arguments
		 * for an endpoint, we can also use JSON Schema to provide us data about what the arguments should be.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array
		 */
		public function get_arguments();

		/**
		 * Get the callback used to validate a request to the REST API endpoint.
		 *
		 * @return callable
		 */
		public function get_permission_callback();

		/**
		 * Get the callback used by the REST API endpoint.
		 *
		 * @return callable
		 */
		public function get_callback();
	}
}
