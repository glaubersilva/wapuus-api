<?php
/**
 * Implements defaults values of the "singleton" design pattern.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Traits;

/**
 * Singleton design pattern trait.
 *
 * @link https://refactoring.guru/design-patterns/singleton
 */
trait Singleton {

	/**
	 * The Singleton's instance is stored in a static field.
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Singletons should not be cloneable.
	 */
	private function __clone() {

	}

	/**
	 * Singletons should not be restorable from strings.
	 *
	 * @throws \Exception Cannot unserialize a singleton.
	 */
	private function __wakeup() {
		throw new \Exception( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Get the singleton instance.
	 *
	 * @return object
	 */
	final public static function getInstance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Alias for getInstance() for legacy code outside src/.
	 *
	 * @return object
	 */
	final public static function get_instance() {
		return self::getInstance();
	}

	/**
	 * Initialize. Must be implemented by the using class.
	 */
	abstract protected function init();
}
