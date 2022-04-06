<?php
/**
 * Implement general tweaks on WordPress and on plugins used by the API.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'General_Tweaks' ) ) {

	/**
	 * This class implements the tweaks.
	 */
	class General_Tweaks {

		use \Wapuus_API\Src\Traits\Singleton;

		/**
		 * Set the function for each hook/tweak when instantiating the singleton object.
		 */
		protected function init() {
			add_action( 'init', array( $this, 'images_size' ) );
			add_filter( 'rest_endpoints', array( $this, 'unset_endpoints' ) );
			add_filter( 'rest_url_prefix', array( $this, 'change_api_prefix' ) );
			add_filter( 'jwt_auth_expire', array( $this, 'change_auth_expire_token' ) );
			add_action( 'rest_api_init', array( $this, 'maybe_change_cors_headers' ), 15 );
		}

		/**
		 * Unset some default WordPress endpoints.
		 *
		 * @param array $endpoints  The available REST API endpoints.
		 *
		 * @return array $endpoints The filtered endpoints.
		 */
		public function unset_endpoints( $endpoints ) {
			unset( $endpoints['/wp/v2/users'] );
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
			return $endpoints;
		}

		/**
		 * Changes the default url prefix (wp-json) for the WordPress REST API with a custom string.
		 *
		 * @param string $prefix The default prefix.
		 *
		 * @return string $prefix The new prefix.
		 */
		public function change_api_prefix( $prefix ) {
			$prefix = 'json';
			return $prefix;
		}

		/**
		 * Set some custom default image sizes.
		 */
		public function images_size() {
			update_option( 'large_size_w', 1000 );
			update_option( 'large_size_h', 1000 );
			update_option( 'large_crop', 1 );
			set_post_thumbnail_size( 300, 300, 1 );
		}

		/**
		 * Changes the default expiration time of the JWT token.
		 *
		 * @link https://wordpress.org/plugins/jwt-auth/
		 *
		 * @param int $expire The default expire timestamp.
		 *
		 * @return int $expire The filtered expire timestamp.
		 */
		public function change_auth_expire_token( $expire ) {
			$expire = time() + ( 60 * 60 * 2400 );
			return $expire;
		}

		/**
		 * If restricted mode is enabled, change the CORS headers to make the API available
		 * just for the wapuus.org and the other domain (usually a development localhost address)
		 * set on the WAPUUS_API_RESTRICTED_MODE const - if the const has just a true value,
		 * so just the wapuus.org will be allowed to use the API.
		 */
		public function maybe_change_cors_headers() {

			if ( defined( 'WAPUUS_API_RESTRICTED_MODE' ) && WAPUUS_API_RESTRICTED_MODE ) {

				remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

				add_filter(
					'rest_pre_serve_request',
					function( $value ) {

						$origin = get_http_origin();

						if ( $origin ) {

							// Requests from file:// and data: URLs send "Origin: null".
							if ( 'null' !== $origin ) {
								$origin = esc_url_raw( $origin );
							}

							$allowed_origins = array( 'wapuus.org', 'http://wapuus.org', 'https://wapuus.org', WAPUUS_API_RESTRICTED_MODE );

							if ( $origin && in_array( $origin, $allowed_origins, true ) ) {
								header( 'Access-Control-Allow-Origin: ' . $origin );
								header( 'Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, PATCH, DELETE' );
								header( 'Access-Control-Allow-Credentials: true' );
								header( 'Vary: Origin', false );
							}
						} elseif ( ! headers_sent() && isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' === $_SERVER['REQUEST_METHOD'] && ! is_user_logged_in() ) {
							header( 'Vary: Origin', false );
						}

						return $value;
					}
				);
			}
		}
	}
}
