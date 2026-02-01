<?php
/**
 * Service Provider interface for the Wapuus API plugin.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface for service providers.
 */
interface Service_Provider_Interface {

	/**
	 * Registers the Service Provider within the application.
	 * Use this to bind anything to the Service Container.
	 *
	 * @return void
	 */
	public function register();

	/**
	 * Bootstraps the service after all services have been registered.
	 * Cross-service dependencies should be resolved by this point.
	 *
	 * @return void
	 */
	public function boot();
}
