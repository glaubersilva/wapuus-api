<?php
/**
 * Core Service Provider for the Wapuus API plugin.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core;

use Wapuus_API\Src\Core\General_Tweaks;
use Wapuus_API\Src\Core\Wapuus_Custom_Post_Type;
use Wapuus_API\Src\Interfaces\Service_Provider_Interface;

use function wapuus_api;

defined( 'ABSPATH' ) || exit;

/**
 * Core Service Provider - registers and boots core plugin services.
 */
class Core_Service_Provider implements Service_Provider_Interface {

	/**
	 * Registers bindings in the container.
	 *
	 * @return void
	 */
	public function register() {
		// Singletons that use get_instance() pattern.
		wapuus_api()->singleton( General_Tweaks::class, function () {
			return General_Tweaks::get_instance();
		} );

		wapuus_api()->singleton( Wapuus_Custom_Post_Type::class, function () {
			return Wapuus_Custom_Post_Type::get_instance();
		} );
	}

	/**
	 * Boots the core services (triggers initialization).
	 *
	 * @return void
	 */
	public function boot() {
		wapuus_api( General_Tweaks::class );
		wapuus_api( Wapuus_Custom_Post_Type::class );
	}
}
