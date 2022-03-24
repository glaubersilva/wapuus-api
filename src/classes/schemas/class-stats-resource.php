<?php
/**
 * Schema for the stats resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Stats_Resource
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Stats_Resource' ) ) {

	class Stats_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$this->name = 'stats';

			$this->schema = array();

			return $this->schema;
		}
	}
}
