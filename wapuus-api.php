<?php
/**
 * Plugin Name: Wapuus API
 * Plugin URI: https://api.wapuus.org/
 * Description: A simple plugin that implements the API used by the wapuus.org project.
 * Version: 1.0.0
 * Author: Glauber Silva
 * Author URI: https://glaubersilva.me/
 * License: GPLv2 or later
 * Text Domain: wapuus-api
 * Domain Path: /languages
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

use WapuusApi\Container\Container;
use WapuusApi\Interfaces\ServiceProviderInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Constants
 */
define( 'WAPUUS_API_DIR', dirname( __FILE__ ) );

/**
 * Main Wapuus API Class
 *
 * @mixin Container
 */
final class Wapuus_Api {

	/**
	 * Container instance.
	 *
	 * @var Container
	 */
	private $container;

	/**
	 * Array of Service Providers to load.
	 *
	 * @var array
	 */
	private $service_providers = array(
		\WapuusApi\Core\CoreServiceProvider::class,
		\WapuusApi\Core\Endpoints\EndpointsServiceProvider::class,
	);

	/**
	 * Whether providers have been loaded.
	 *
	 * @var bool
	 */
	private $providers_loaded = false;

	/**
	 * Constructor. Sets up the Container.
	 */
	public function __construct() {
		$this->container = new Container();
	}

	/**
	 * Init when WordPress initializes.
	 *
	 * @return void
	 */
	public function init() {
		/**
		 * Fires before the Wapuus API core is initialized.
		 */
		do_action( 'before_wapuus_api_init' );

		$this->load_service_providers();

		/**
		 * Fires after the Wapuus API core loads.
		 *
		 * @param Wapuus_Api $this Class instance.
		 */
		do_action( 'wapuus_api_init', $this );
	}

	/**
	 * Load all service providers.
	 *
	 * @return void
	 */
	public function load_service_providers() {
		if ( $this->providers_loaded ) {
			return;
		}

		$providers = array();

		foreach ( $this->service_providers as $service_provider ) {
			if ( ! is_subclass_of( $service_provider, ServiceProviderInterface::class ) ) {
				$name = is_string( $service_provider ) ? $service_provider : get_class( $service_provider );
				throw new InvalidArgumentException(
					esc_html( $name . ' must implement the Service_Provider_Interface' )
				);
			}

			/** @var ServiceProviderInterface $provider */
			$provider = new $service_provider();
			$provider->register();
			$providers[] = $provider;
		}

		foreach ( $providers as $provider ) {
			$provider->boot();
		}

		$this->providers_loaded = true;
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @return void
	 */
	public function boot() {
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );

		do_action( 'wapuus_api_loaded' );
	}

	/**
	 * Register a Service Provider.
	 *
	 * @param string $service_provider Fully qualified class name.
	 * @return void
	 */
	public function register_service_provider( $service_provider ) {
		$this->service_providers[] = $service_provider;
	}

	/**
	 * Magic getter - delegate to container.
	 *
	 * @param string $property_name Property name.
	 * @return mixed
	 */
	public function __get( $property_name ) {
		return $this->container->get( $property_name );
	}

	/**
	 * Magic call - delegate to container.
	 *
	 * @param string $name      Method name.
	 * @param array  $arguments Method arguments.
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		return call_user_func_array( array( $this->container, $name ), $arguments );
	}

	/**
	 * Get the underlying container instance.
	 *
	 * @return Container
	 */
	public function getContainer() {
		return $this->container;
	}
}

/**
 * Return the Wapuus API instance.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php $wapuus_api = wapuus_api(); ?>
 *
 * @param string|null $abstract Optional. Key to resolve from the container.
 * @return Wapuus_Api|mixed The plugin instance or a resolved binding when $abstract is passed.
 */
function wapuus_api( $abstract = null ) {
	static $instance = null;

	if ( null === $instance ) {
		$instance = new Wapuus_Api();
	}

	if ( null !== $abstract ) {
		return $instance->make( $abstract );
	}

	return $instance;
}

/**
 * Initial Setup
 */
require_once WAPUUS_API_DIR . '/vendor/autoload.php';

/**
 * API V1 files - Legacy code left to demonstrate extending the WP API without using classes.
 */
require_once WAPUUS_API_DIR . '/legacy/load-endpoints-v1.php';

wapuus_api()->boot();
