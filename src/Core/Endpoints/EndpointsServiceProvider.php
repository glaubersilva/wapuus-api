<?php
/**
 * Endpoints Service Provider for the Wapuus API plugin.
 *
 * Registers and boots API V2 endpoints. Endpoints are registered explicitly
 * to avoid accidentally loading non-endpoint classes from the same directory.
 *
 * @link https://developer.wordpress.org/rest-api/
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Interfaces\Endpoint;
use WapuusApi\Interfaces\ServiceProviderInterface;

/**
 * Endpoints Service Provider - registers and boots API endpoints (V2).
 */
class EndpointsServiceProvider implements ServiceProviderInterface  {

	/**
	 * Endpoint classes (implement Endpoint interface).
	 * "Endpoint Wrap" approach: https://carlalexander.ca/designing-system-wordpress-rest-api-endpoints/
	 *
	 * @var string[]
	 */
	private $endpoint_classes = array(
		CommentsDelete::class,
		CommentsGet::class,
		CommentsPost::class,
		ImagesDelete::class,
		ImagesGet::class,
		ImagesGetOne::class,
		ImagesPost::class,
		PasswordLost::class,
		PasswordReset::class,
		StatsGet::class,
		UsersGet::class,
		UsersPost::class,
	);

	/**
	 * Instances of endpoint classes to register.
	 *
	 * @var Endpoint[]
	 */
	private $endpoints = array();

	/**
	 * Registers bindings in the container.
	 *
	 * @return void
	 */
	public function register() {
		// No container bindings for endpoints.
	}

	/**
	 * Boots the endpoints: instantiates endpoints and registers routes on rest_api_init.
	 *
	 * @return void
	 */
	public function boot() {
		foreach ( $this->endpoint_classes as $endpoint_class ) {
			$this->endpoints[] = new $endpoint_class();
		}

		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Register all endpoints with the WordPress REST API.
	 *
	 * @return void
	 */
	public function register_endpoints() {
		foreach ( $this->endpoints as $endpoint ) {
			$this->register_endpoint( $endpoint );
		}
	}

	/**
	 * Register a single endpoint with the WordPress REST API.
	 *
	 * @param Endpoint $endpoint Concrete class implementing the Endpoint interface.
	 * @return void
	 */
	private function register_endpoint( Endpoint $endpoint ) {
		register_rest_route(
			'wapuus-api/v2',
			$endpoint->get_path(),
			array(
				'schema' => $endpoint->get_schema(),
				array(
					'methods'             => $endpoint->get_methods(),
					'args'                => $endpoint->get_arguments(),
					'permission_callback' => $endpoint->get_permission_callback(),
					'callback'            => $endpoint->get_callback(),
				),
			)
		);
	}
}
