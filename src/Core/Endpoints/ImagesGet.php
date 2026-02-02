<?php
/**
 * The API V2 endpoint for "images get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Core\Endpoints\AbstractEndpoint;
use WapuusApi\Core\Schemas\ImagesResource;
use WapuusApi\Core\Helpers;
	/**
	 * The "images get" endpoint class.
	 */
	class ImagesGet extends AbstractEndpoint {

		/**
		 * Route for the "images get" endpoint.
		 */
		public function getPath() {
			return '/' . ImagesResource::getInstance()->getName();
		}

		/**
		 * Resource schema callback for the "images get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resourceSchema() {
			return ImagesResource::getInstance()->getSchema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "images get" endpoint.
		 */
		public function getMethods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "images get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function getArguments() {

			$args = array(
				'_total' => array(
					'description' => __( 'Total number of images per page - if not set, the default value is 6.', 'wapuus-api' ),
					'type'        => 'integer',
				),
				'_page'  => array(
					'description' => __( 'The number of the page to retrieve - if not set, the default value is 1.', 'wapuus-api' ),
					'type'        => 'integer',
				),
				'_user'  => array(
					'description' => __( 'The ID or username of the user object to retrieve the images - if not set, returns images from all users.', 'wapuus-api' ),
					'type'        => 'string',
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "images get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function checkPermissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "images get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			if ( isset( $request['_user'] ) && ! is_numeric( $request['_user'] ) ) {

				$user = get_user_by( 'login', sanitize_user( $request['_user'] ) );

				if ( ! $user ) {

					$response = new \WapuusApi\Core\Responses\Error\NotFound( __( 'User not found.', 'wapuus-api' ) );
					return rest_ensure_response( $response );
				}
			}

			$total = isset( $request['_total'] ) ? sanitize_text_field( $request['_total'] ) : 6;
			$page  = isset( $request['_page'] ) ? sanitize_text_field( $request['_page'] ) : 1;
			$userFilter = isset( $request['_user'] ) ? sanitize_user( $request['_user'] ) : 0;

			if ( ! is_numeric( $userFilter ) ) {
				$user       = get_user_by( 'login', $userFilter );
				$userFilter = $user->ID;
			}

			$args = array(
				'post_type'      => 'wapuu',
				'author'         => $userFilter,
				'posts_per_page' => $total,
				'paged'          => $page,
			);

			$query = new \WP_Query( $args );
			$posts = $query->posts;

			$images = array();

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$images[] = Helpers::getPostData( $post );
				}
			}

			$response = new \WapuusApi\Core\Responses\Valid\Ok( $images );

			return rest_ensure_response( $response );
		}
	}
