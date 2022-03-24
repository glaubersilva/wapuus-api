<?php
/**
 * The schema for a resource indicates what fields are present for a particular object.
 * When we register our routes we can also specify the resource schema for the route.
 * More details here: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema
 *
 * @package \Wapuus_API\Src\Classes\Schemas
 */

namespace Wapuus_API\Src\Classes\Schemas;

/**
 * This class should be extended and used as the base to create a schema for new resources - comment, photo, user, etc.
 */
abstract class Abstract_Resource {

	/**
	 * The name of the resource.
	 *
	 * @var $name
	 */
	protected $name;

	/**
	 * Stores the schema of the resource.
	 *
	 * @var $schema
	 */
	protected $schema;

	/**
	 * Return the final schema for the resource - comment, photo, user etc.
	 */
	final public function schema() {

		if ( $this->schema ) {
			/**
			 * The introduction of schema caching in WordPress 5.3 increased the speed
			 * of some core REST API collection responses by up to 40%, so you should
			 * definitely consider following this pattern in your own controllers.
			 *
			 * More details here: https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/#benefits-of-classes
			 */
			return $this->schema;
		}

		$this->schema = $this->init();

		return $this->schema;
	}

	/**
	 * Return the name of the resource - comment, photo, user etc.
	 */
	final public function name() {

		if ( empty( $this->name ) ) {
			$this->name = 'Undefined';
		}

		return $this->name;
	}

	/**
	 * The abastract keyword force extending class to define this method.
	 * Source: https://www.php.net/manual/en/language.oop5.abstract.php
	 *
	 * @return array With the schema for a particular resource - comment, photo, user etc. - that extends this abstract class.
	 */
	abstract protected function init();
}
