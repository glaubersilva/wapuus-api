<?php

namespace Wapuus_API\Src\Traits;

/**
 * Singleton is a creational design pattern that lets you ensure that a class has only one instance, while providing a global access point to this instance.
 * Source: https://refactoring.guru/design-patterns/singleton
 */
trait Singleton {

	/**
	 * The visibility of a property, a method or (as of PHP 7.1.0) a constant can be defined by prefixing the declaration with the keywords public, protected or private.
	 * Class members declared public can be accessed everywhere.
	 * Members declared protected can be accessed only within the class itself and by inheriting and parent classes.
	 * Members declared as private may only be accessed by the class that defines the member.
	 * Source: https://www.php.net/manual/en/language.oop5.visibility.php
	 *
	 * The Singleton's constructor should always be private to prevent direct construction calls with the "new" operator.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Once the cloning is complete, if a __clone() method is defined, then the newly created object's __clone() method will be called, to allow any necessary properties that need to be changed.
	 * Source: https://www.php.net/manual/en/language.oop5.cloning.php#object.clone
	 *
	 * Singletons should not be cloneable.
	 */
	private function __clone() {

	}

	/**
	 * The intended use of __wakeup() is to reestablish any database connections that may have been lost during serialization and perform other reinitialization tasks.
	 * Source: https://www.php.net/manual/en/language.oop5.magic.php#object.wakeup
	 *
	 * Singletons should not be restorable from strings.
	 */
	private function __wakeup() {
		throw new \Exception( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Declaring class properties or methods as static makes them accessible without needing an instantiation of the class.
	 * These can also be accessed statically within an instantiated class object.
	 * Source: https://www.php.net/manual/en/language.oop5.static.php
	 *
	 * The Singleton's instance is stored in a static field.
	 */
	protected static $instance;

	/**
	 * The final keyword prevents child classes from overriding a method or constant by prefixing the definition with final.
	 * If the class itself is being defined final then it cannot be extended.
	 * Source: https://www.php.net/manual/en/language.oop5.final.php
	 *
	 * This is the static method that controls the access to the singleton
	 * instance. On the first run, it creates a singleton object and places it
	 * into the static field. On subsequent runs, it returns the client existing
	 * object stored in the static field.
	 */
	final public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * The abastract keyword force extending class to define this method.
	 * Source: https://www.php.net/manual/en/language.oop5.abstract.php
	 */
	abstract protected function init();
}
