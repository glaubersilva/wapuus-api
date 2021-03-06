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

defined( 'ABSPATH' ) || exit;

/**
 * Constants
 */
define( 'WAPUUS_API_DIR', dirname( __FILE__ ) );

/**
 * Initial Setup
 */
require_once WAPUUS_API_DIR . '/autoload.php';
require_once WAPUUS_API_DIR . '/src/helpers.php';
\Wapuus_API\Src\Classes\General_Tweaks::get_instance();
\Wapuus_API\Src\Classes\Wapuus_Custom_Post_Type::get_instance();

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */
require_once WAPUUS_API_DIR . '/legacy/load-endpoints-v1.php';

/**
 * API V2 classes are loaded by this class.
 */
new \Wapuus_API\Src\Classes\Load_Endpoints_V2();
