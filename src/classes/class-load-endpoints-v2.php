<?php

namespace Wapuus_API\Src\Classes;

if ( ! class_exists( 'Load_Endpoints_V2' ) ) {

	class Load_Endpoints_V2 {

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
				array(
					'args'                => $endpoint->get_arguments(),
					'callback'            => $endpoint->get_callback(),
					'methods'             => $endpoint->get_methods(),
					'permission_callback' => $endpoint->get_permission_callback(),
				)
			);
		}
	}
}
