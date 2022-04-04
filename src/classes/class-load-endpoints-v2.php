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

namespace Wapuus_API\Src\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Load_Endpoints_V2' ) ) {

	/**
	 * This class loads the API V2 endpoints.
	 */
	class Load_Endpoints_V2 {

		/**
		 * The list of implemented endpoint classes.
		 *
		 * @var array
		 */
		private $endpoints = array();

		/**
		 * Initializes all "Rest controller" and "Endpoint" classes automatically.
		 */
		public function __construct() {

			$dir = new \DirectoryIterator( WAPUUS_API_DIR . '/src/classes/endpoints/' );

			foreach ( $dir as $file_info ) {

				if ( ! $file_info->isDot() && false === strpos( strtolower( $file_info ), 'abstract' ) ) {

					$class_name = $file_info->getFilename();
					$class_name = str_replace( 'class-', '', $class_name );
					$class_name = str_replace( '.php', '', $class_name );
					$class_name = str_replace( '-', '_', $class_name );
					$class_name = '\Wapuus_API\Src\Classes\Endpoints\\' . $class_name;

					if ( false !== strpos( strtolower( $file_info ), 'controller' ) ) {

						/**
						 * The "Rest Controller" class approach.
						 * https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/
						 */
						new $class_name();

					} else {

						/**
						 * The "Endpoint Wrap" class approach.
						 * https://carlalexander.ca/designing-system-wordpress-rest-api-endpoints/
						 */
						array_push( $this->endpoints, new $class_name() );

					}
				}
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
		 * @param \Wapuus_API\Src\Interfaces\Endpoint $endpoint  Accept an implementation class of the "Endpoint" interface.
		 */
		private function register_endpoint( \Wapuus_API\Src\Interfaces\Endpoint $endpoint ) {
			register_rest_route(
				'wapuus-api/v2',
				$endpoint->get_path(),
				array( // The callback to the "resource schema" which is the same for all methods (POST, GET, DELETE etc.) that the endpoint accepts.
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
