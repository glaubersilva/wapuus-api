<?php
/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

/**
 * Load all endpoints from the API V1 one by one.
 */
require_once WAPUUS_API_DIR . '/legacy/endpoints/users-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/users-post.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/images-post.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/images-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/images-get-one.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/images-delete.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/comments-post.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/comments-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/comments-delete.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/stats-get.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/password-lost.php';
require_once WAPUUS_API_DIR . '/legacy/endpoints/password-reset.php';
