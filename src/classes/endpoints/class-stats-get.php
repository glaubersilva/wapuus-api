<?php

namespace Wapuus_API\Src\Classes\Endpoints;

use Wapuus_API\Src\Classes\Endpoints\Abstract_Endpoint;
use Wapuus_API\Src\Classes\Responses\Error\Unauthorized;

class Stats_Get extends Abstract_Endpoint {

	public function get_path() {
		return '/stats';
	}

	public function get_methods() {
		return \WP_REST_Server::READABLE; //GET method.
	}

	public function get_arguments() {
		return array();
	}

	public function check_permissions() {

		if ( ! is_user_logged_in() ) {
			$response = new Unauthorized( 'rest_forbidden', 'User does not have permission.' );
			return rest_ensure_response( $response );
		}

		return true;
	}

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

		return rest_ensure_response( $stats );
	}
}
