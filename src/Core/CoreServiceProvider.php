<?php
/**
 * Core Service Provider for the Wapuus API plugin.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core;

use WapuusApi\Core\GeneralTweaks;
use WapuusApi\Core\WapuusCustomPostType;
use WapuusApi\Interfaces\ServiceProviderInterface;

use function wapuus_api;

/**
 * Core Service Provider - registers and boots core plugin services.
 */
class CoreServiceProvider implements ServiceProviderInterface  {

	/**
	 * Registers bindings in the container.
	 *
	 * @return void
	 */
	public function register() {
		// Singletons that use get_instance() pattern.
		wapuus_api()->singleton( GeneralTweaks::class, function () {
			return GeneralTweaks::get_instance();
		} );

		wapuus_api()->singleton( WapuusCustomPostType::class, function () {
			return WapuusCustomPostType::get_instance();
		} );
	}

	/**
	 * Boots the core services (triggers initialization).
	 *
	 * @return void
	 */
	public function boot() {
		wapuus_api( GeneralTweaks::class );
		wapuus_api( WapuusCustomPostType::class );
	}
}
