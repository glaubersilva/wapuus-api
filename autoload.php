<?php
/**
 * The PSR-4 autoloader for the Wapuus API plugin.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register autoload.
 *
 * Based in the PSR-4 autoloader example found here:
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(
	function ( $class ) {

		// The project-specific namespace prefix.
		$prefix = 'Wapuus_API\\';

		// Base directory for the namespace prefix.
		$base_dir = trailingslashit( dirname( __FILE__ ) );

		// Does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			// No, move to the next registered autoloader.
			return;
		}

		$relative_class = substr( $class, $len ) . '.php';

		$path = explode( '\\', strtolower( str_replace( '_', '-', $relative_class ) ) );
		$file = array_pop( $path );

		if ( strpos( strtolower( $relative_class ), 'trait' ) !== false ) {
			$file = 'trait-' . $file;
		} elseif ( strpos( strtolower( $relative_class ), 'interface' ) !== false ) {
			$file = 'interface-' . $file;
		} else {
			$file = 'class-' . $file;
		}

		$file = $base_dir . implode( '/', $path ) . '/' . $file;

		// if the file exists, require it.
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
