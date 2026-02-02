<?php
/**
 * Service Provider interface for the Wapuus API plugin.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Interfaces;

/**
 * Interface for service providers.
 */
interface ServiceProviderInterface {

	/**
	 * Registers the Service Provider within the application.
	 *
	 * @return void
	 */
	public function register();

	/**
	 * Bootstraps the service after all services have been registered.
	 *
	 * @return void
	 */
	public function boot();
}
