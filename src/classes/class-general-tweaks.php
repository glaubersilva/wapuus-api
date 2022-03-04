<?php

namespace Wapuus_API\Src\Classes;

if ( ! class_exists( 'General_Tweaks' ) ) {

	class General_Tweaks {

		use \Wapuus_API\Src\Traits\Singleton;

		protected function init() {
			add_action( 'init', array( $this, 'images_size' ) );
			add_filter( 'rest_endpoints', array( $this, 'unset_endpoints' ) );
			add_filter( 'rest_url_prefix', array( $this, 'change_api_prefix' ) );
			add_filter( 'jwt_auth_expire', array( $this, 'change_auth_expire_token' ) );
			add_action( 'rest_api_init', array( $this, 'maybe_change_cors_headers' ), 15 );
		}

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
						} elseif ( ! headers_sent() && 'GET' === $_SERVER['REQUEST_METHOD'] && ! is_user_logged_in() ) {
							header( 'Vary: Origin', false );
						}

						return $value;
					}
				);
			}
		}

		public function images_size() {
			update_option( 'large_size_w', 1000 );
			update_option( 'large_size_h', 1000 );
			update_option( 'large_crop', 1 );
			set_post_thumbnail_size( 300, 300, 1 );
		}

		public function unset_endpoints( $endpoints ) {
			unset( $endpoints['/wp/v2/users'] );
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
			return $endpoints;
		}

		/**
		 * Changes the default url prefix (wp-json) for the WP REST API with a custom string.
		 */
		public function change_api_prefix( $prefix ) {
			$prefix = 'json';
			return $prefix;
		}

		public function change_auth_expire_token( $expire ) {
			$expire = time() + ( 60 * 60 * 2400 );
			return $expire;
		}
	}
}
