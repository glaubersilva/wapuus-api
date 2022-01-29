<?php

namespace Wapuus_API\Src\Classes;

if ( ! class_exists( 'General_Tweaks' ) ) {
	
	class General_Tweaks {

		use \Wapuus_API\Src\Traits\Singleton;

		protected function init() {
			add_action( 'init', array( $this, 'images_size' ) );			
			//remove_action( 'rest_api_init', 'create_initial_rest_routes', 99 );
			add_filter( 'rest_endpoints', array( $this, 'unset_endpoints' ) );
			add_filter( 'rest_url_prefix', array( $this, 'change_api_prefix' ) );
			add_filter( 'jwt_auth_expire', array( $this, 'change_auth_expire_token' ) );
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

		public function change_auth_expire_token( $expire ){
			$expire = time() + ( 60 * 60 * 2400 );
			return $expire;
		}
	}
}