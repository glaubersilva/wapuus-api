<?php
/**
 * Endpoints Service Provider for the Wapuus API plugin.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Endpoints;

use Wapuus_API\Src\Core\Load_Endpoints_V2;
use Wapuus_API\Src\Interfaces\Service_Provider_Interface;

use function wapuus_api;

defined( 'ABSPATH' ) || exit;

/**
 * Endpoints Service Provider - registers and boots API endpoints (V2).
 */
class Endpoints_Service_Provider implements Service_Provider_Interface {

	/**
	 * Registers bindings in the container.
	 *
	 * @return void
	 */
	public function register() {
		wapuus_api()->singleton( Load_Endpoints_V2::class );
	}

	/**
	 * Boots the endpoints (Load_Endpoints_V2 constructor registers routes).
	 *
	 * @return void
	 */
	public function boot() {
		wapuus_api( Load_Endpoints_V2::class );
	}
}
