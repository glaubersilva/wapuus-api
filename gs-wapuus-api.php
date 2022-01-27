<?php
/**
 * Plugin Name: [GS] Wapuus API
 * Plugin URI: https://glaubersilva.me/
 * Description: A simple plugin that implements the API used by the https://github.com/glaubersilva/wapuus project - Just an app built for study purposes with React as frontend and WordPress as backend.
 * Version: 1.0.0
 * Author: Glauber Silva
 * Author URI: https://glaubersilva.me/
 * License: GPLv2 or later
 * Text Domain: gswapuus
 * Domain Path: /languages
 *
 * @package GS_Wapuus_API
 */

defined( 'ABSPATH' ) || exit;

define( 'GS_WAPUUS', dirname( __FILE__ ) );

require_once GS_WAPUUS . '/classes/class-my-rest-post-controller.php';

/**
 * API V1 files - Legacy Code was left in the project just to demonstrate how to extend the WP API without using classes.
 */
require_once GS_WAPUUS . '/legacy/user-post-endpoint.php';
require_once GS_WAPUUS . '/legacy/user-get-endpoint.php';
require_once GS_WAPUUS . '/legacy/photo-post-endpoint.php';
require_once GS_WAPUUS . '/legacy/photo-get-endpoint.php';
require_once GS_WAPUUS . '/legacy/photo-delete-endpoint.php';
require_once GS_WAPUUS . '/legacy/comment-post-endpoint.php';
require_once GS_WAPUUS . '/legacy/comment-get-endpoint.php';
require_once GS_WAPUUS . '/legacy/stats-get-endpoint.php';
require_once GS_WAPUUS . '/legacy/password-lost-reset-endpoints.php';

update_option( 'large_size_w', 1000 );
update_option( 'large_size_h', 1000 );
update_option( 'large_crop', 1 );

// remove_action( 'rest_api_init', 'create_initial_rest_routes', 99 );

add_filter(
	'rest_endpoints',
	function( $endpoints ) {
		unset( $endpoints['/wp/v2/users'] );
		unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		return $endpoints;
	}
);

/**
 * Changes the default url prefix (wp-json) for the WP REST API with a custom string.
 */
function gs_change_api_prefix() {
	return 'json';
}
add_filter( 'rest_url_prefix', 'gs_change_api_prefix' );

function gs_expire_token() {
	return time() + ( 60 * 60 * 2400 );
}
add_filter( 'jwt_auth_expire', 'gs_expire_token' );

function gs_footer_tests() {
	?>
		<script>

			//const body = {
			//	"username": "cat",
			//	"password": "catps",
				//"email": "cat@wapuus-api.local"
			//}

			// Token			
			// eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd2FwdXVzLWFwaS5sb2NhbCIsImlhdCI6MTYyMDE3NzAzMiwibmJmIjoxNjIwMTc3MDMyLCJleHAiOjE2MjA3ODE4MzIsImRhdGEiOnsidXNlciI6eyJpZCI6IjIifX19.J1B0Vuthbkpgzd1sk-Cl0Zo6gNOzrdy5BWrv6qQkve8

			fetch( 'https://wapuus-api.local/json/jwt-auth/v1/token/validate', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Authorization: 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd2FwdXVzLWFwaS5sb2NhbCIsImlhdCI6MTYyMDE3NzAzMiwibmJmIjoxNjIwMTc3MDMyLCJleHAiOjE2MjA3ODE4MzIsImRhdGEiOnsidXNlciI6eyJpZCI6IjIifX19.J1B0Vuthbkpgzd1sk-Cl0Zo6gNOzrdy5BWrv6qQkve8'
				},
				//body: JSON.stringify(body)
			}).then( response => {
				console.log( response );
				return response.json()
			} ).then(json => console.log(json))


		</script>
	<?php
}
// add_action( 'wp_footer', 'gs_footer_tests' );
