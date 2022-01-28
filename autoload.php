<?php
/**
 * Autoloader for the plugin: GS Wapuus API
 *
 * Based in the PSR-4 autoloader example found here:
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */

spl_autoload_register(
    function ( $class ) {

        // project-specific namespace prefix
        $prefix = 'Wapuus_API\\';

        // base directory for the namespace prefix
        $base_dir = trailingslashit( dirname( __FILE__ ) );

        // does the class use the namespace prefix?
        $len = strlen( $prefix );
        if ( strncmp( $prefix, $class, $len ) !== 0 ) {
            // no, move to the next registered autoloader
            return;
        }

        $relative_class = substr( $class, $len ) . '.php';

        $path = explode( '\\', strtolower( str_replace( '_', '-', $relative_class ) ) );
        $file = array_pop( $path );

        if ( strpos( strtolower($relative_class), 'trait') == false ) {
            $file = 'class-' . $file;
        } else {
			$file = 'trait-' . $file;
		}

        /*$reflection = new ReflectionClass( $class );

        if ( $reflection->isInterface() ) {
            $file = 'interface-' . $file;
        } elseif ( $reflection->isTrait() ) {
            $file = 'trait-' . $file;
        } else {
            $file = 'class-' . $file;
        }*/

        $file = $base_dir . implode( '/', $path ) . '/' . $file;

        // if the file exists, require it
        if ( file_exists( $file ) ) {
            require $file;
        }
    }
);
