<?php
/**
 * Schema for the Stats resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Stats
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Stats_Resource' ) ) {

	class Stats_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$schema = array();

			return $schema;
		}
	}
}
