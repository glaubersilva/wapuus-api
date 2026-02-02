<?php
/**
 * The schema for a resource (or object) indicates what fields are present
 * for a particular object - comment, image, user, etc. When we register our
 * endpoints we can specify these resource schemas, which is the samefor all
 * methods (POST, GET, DELETE etc.) that the route accepts.
 *
 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Schemas;

	/**
	 * This class should be extended and used as the base to create
	 * a schema for new resources - comment, image, user, etc.
	 */
	abstract class AbstractResource {

		/**
		 * Resource name.
		 *
		 * @var string
		 */
		protected $name;

		/**
		 * Resource schema.
		 *
		 * @var array
		 */
		protected $schema;

		/**
		 * Callback for the resource schema.
		 *
		 * @return array
		 */
		final public function schema() {

			if ( $this->schema ) {
				/**
				 * The introduction of schema caching in WordPress 5.3 increased the speed
				 * of some core REST API collection responses by up to 40%, so you should
				 * definitely consider following this pattern in your own classes.
				 *
				 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/#benefits-of-classes
				 */
				return $this->schema;
			}

			$this->schema = $this->init();

			return $this->schema;
		}

		/**
		 * Return the name of the resource - comment, image, user etc.
		 */
		final public function name() {

			if ( empty( $this->name ) ) {
				$this->name = __( 'Undefined', 'wapuus-api' );
			}

			return $this->name;
		}

		/**
		 * The abastract keyword force extending class to define this method.
		 *
		 * @link https://www.php.net/manual/en/language.oop5.abstract.php
		 *
		 * @return array With the schema for a particular resource class that extends this base class.
		 */
		abstract protected function init();
	}
