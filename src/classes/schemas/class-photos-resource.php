<?php
/**
 * Schema for the photos resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Photos_Resource
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Photos_Resource' ) ) {

	class Photos_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$this->name = 'photos';

			$this->schema = array();

			return $this->schema;
		}
	}
}
