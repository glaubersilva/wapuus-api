<?php
/**
 * Plugin Name: Wapuus API
 * Plugin URI: https://glaubersilva.me/
 * Description: A simple plugin that implements the API used by the https://github.com/glaubersilva/wapuus project - Just an app built for study purposes with React as frontend and WordPress as backend.
 * Version: 1.0.0
 * Author: Glauber Silva
 * Author URI: https://glaubersilva.me/
 * License: GPLv2 or later
 * Text Domain: wapuus-api
 * Domain Path: /languages
 *
 * @package Wapuus_API
 */

defined( 'ABSPATH' ) || exit;

/**
 * Constants
 */
define( 'WAPUUS_API_DIR', dirname( __FILE__ ) );
define( 'WAPUUS_API_TEXT_DOMAIN', 'wapuus-api' );

/**
 * Initial Setup
 */
require_once WAPUUS_API_DIR . '/autoload.php';
\Wapuus_API\Src\Classes\General_Tweaks::get_instance();
\Wapuus_API\Src\Classes\Wapuus_Custom_Post_Type::get_instance();

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */
require_once WAPUUS_API_DIR . '/legacy/endpoints/user-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/user-post.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/photo-post.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/photo-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/photo-delete.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/comment-post.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/comment-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/stats-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/password-lost-reset.php';

/**
 * API V2 classes are loaded by this class.
 */
new \Wapuus_API\Src\Classes\Load_Endpoints();
