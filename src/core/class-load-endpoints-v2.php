<?php
/**
 * Extends the default WordPress REST API endpoints through the use of classes.
 *
 * @link https://developer.wordpress.org/rest-api/
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core;

use Wapuus_API\Src\Core\Endpoints\Comments_Delete;
use Wapuus_API\Src\Core\Endpoints\Comments_Get;
use Wapuus_API\Src\Core\Endpoints\Comments_Post;
use Wapuus_API\Src\Core\Endpoints\Images_Delete;
use Wapuus_API\Src\Core\Endpoints\Images_Get;
use Wapuus_API\Src\Core\Endpoints\Images_Get_One;
use Wapuus_API\Src\Core\Endpoints\Images_Post;
use Wapuus_API\Src\Core\Endpoints\Password_Lost;
use Wapuus_API\Src\Core\Endpoints\Password_Reset;
use Wapuus_API\Src\Core\Endpoints\Sample_Rest_Posts_Controller;
use Wapuus_API\Src\Core\Endpoints\Stats_Get;
use Wapuus_API\Src\Core\Endpoints\Users_Get;
use Wapuus_API\Src\Core\Endpoints\Users_Post;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Load_Endpoints_V2' ) ) {

	/**
	 * This class loads the API V2 endpoints.
	 *
	 * Endpoints are registered explicitly to avoid accidentally loading
	 * non-endpoint classes (e.g. service providers) from the same directory.
	 */
	class Load_Endpoints_V2 {

		/**
		 * Endpoint classes (implement Endpoint interface).
		 * "Endpoint Wrap" approach: https://carlalexander.ca/designing-system-wordpress-rest-api-endpoints/
		 *
		 * @var string[]
		 */
		private $endpoint_classes = array(
			Comments_Delete::class,
			Comments_Get::class,
			Comments_Post::class,
			Images_Delete::class,
			Images_Get::class,
			Images_Get_One::class,
			Images_Post::class,
			Password_Lost::class,
			Password_Reset::class,
			Stats_Get::class,
			Users_Get::class,
			Users_Post::class,
		);

		/**
		 * REST Controller classes (WP_REST_Controller approach).
		 * https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/
		 *
		 * @var string[]
		 */
		private $controller_classes = array(
			Sample_Rest_Posts_Controller::class,
		);

		/**
		 * Instances of endpoint classes to register.
		 *
		 * @var \Wapuus_API\Src\Interfaces\Endpoint[]
		 */
		private $endpoints = array();

		/**
		 * Initializes and registers endpoints explicitly.
		 */
		public function __construct() {
			foreach ( $this->controller_classes as $controller_class ) {
				new $controller_class();
			}

			foreach ( $this->endpoint_classes as $endpoint_class ) {
				$this->endpoints[] = new $endpoint_class();
			}

			add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
		}

		/**
		 * Register all our endpoints with the WordPress REST API.
		 */
		public function register_endpoints() {
			foreach ( $this->endpoints as $endpoint ) {
				$this->register_endpoint( $endpoint );
			}
		}

		/**
		 * Register an endpoint with the WordPress REST API.
		 *
		 * @param \Wapuus_API\Src\Interfaces\Endpoint $endpoint Accept a concrete class based on the "Endpoint" interface.
		 */
		private function register_endpoint( \Wapuus_API\Src\Interfaces\Endpoint $endpoint ) {
			register_rest_route(
				'wapuus-api/v2',
				$endpoint->get_path(),
				array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the route accepts.
					'schema' => $endpoint->get_schema(), // https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema <<< Reference.
					array(
						'methods'             => $endpoint->get_methods(),
						'args'                => $endpoint->get_arguments(), // Schema of the expected arguments for the endpoint - https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema <<< Reference.
						'permission_callback' => $endpoint->get_permission_callback(),
						'callback'            => $endpoint->get_callback(),
					),
					// Here we could have another array with a declaration of another method - POST, GET, DELETE etc.
				)
			);
		}
	}
}
