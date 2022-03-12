<?php

namespace Wapuus_API\Src\Classes;

use Wapuus_API\Src\Classes\Endpoints\My_REST_Posts_Controller;

if ( ! class_exists( 'Load_Endpoints_V2' ) ) {

	class Load_Endpoints_V2 {

		// Here initialize all Rest controller classes.
		public function __construct() {

			new My_REST_Posts_Controller();

			add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );

		}

		/**
		 * Register all our endpoints with the WordPress REST API.
		 */
		public function register_endpoints() {

			$endpoints = array(
				new \Wapuus_API\Src\Classes\Endpoints\Stats_Get(),
			);

			foreach ( $endpoints as $endpoint ) {
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
