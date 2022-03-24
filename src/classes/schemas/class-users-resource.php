<?php
/**
 * Schema for the users resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Users_Resource
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Users_Resource' ) ) {

	class Users_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$this->name = 'users';

			$this->schema = array();

			return $this->schema;
		}
	}
}
