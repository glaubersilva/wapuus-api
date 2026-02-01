<?php
/**
 * The API V2 endpoint for "stats get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core\Endpoints;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Core\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Core\Schemas\Stats_Resource;

if ( ! class_exists( 'Stats_Get' ) ) {

	/**
	 * The "stats get" endpoint class.
	 */
	class Stats_Get extends Abstract_Endpoint {

		/**
		 * Route for the "stats get" endpoint.
		 */
		public function get_path() {
			return '/' . Stats_Resource::get_instance()->name();
		}

		/**
		 * Resource schema callback for the "stats get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resource_schema() {
			return Stats_Resource::get_instance()->schema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "stats get" endpoint.
		 */
		public function get_methods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "stats get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function get_arguments() {

			$args = array();

			return $args;
		}

		/**
		 * Permission callback for the "stats get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function check_permissions( \WP_REST_Request $request ) {

			if ( ! is_user_logged_in() ) {
				$response = new \Wapuus_API\Src\Core\Responses\Error\No_Permission( __( 'User does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			return true;
		}

		/**
		 * Callback for the "stats get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$user = wp_get_current_user();

			$args = array(
				'post_type'     => 'wapuu',
				'author'        => $user->ID,
				'post_per_page' => -1,
			);

			$query = new \WP_Query( $args );
			$posts = $query->posts;

			$stats = array();

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$stats[] = array(
						'id'    => $post->ID,
						'title' => $post->post_title,
						'views' => get_post_meta( $post->ID, 'views', true ),
					);
				}
			}

			$response = new \Wapuus_API\Src\Core\Responses\Valid\OK( $stats );

			return rest_ensure_response( $response );
		}
	}
}
