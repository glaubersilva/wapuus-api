<?php
/**
 * The API V2 endpoint for "image get".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Core\Endpoints\AbstractEndpoint;
use WapuusApi\Core\Schemas\ImagesResource;
use WapuusApi\Helpers;
	/**
	 * The "image get" endpoint class.
	 */
	class ImagesGetOne extends AbstractEndpoint {

		/**
		 * Route for the "image get" endpoint.
		 */
		public function getPath() {
			return '/' . ImagesResource::getInstance()->getName() . '/(?P<id>[0-9]+)';
		}

		/**
		 * Resource schema callback for the "image get" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resourceSchema() {
			return ImagesResource::getInstance()->getSchema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "image get" endpoint.
		 */
		public function getMethods() {
			return \WP_REST_Server::READABLE;
		}

		/**
		 * Schema of the expected arguments for the "image get" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function getArguments() {

			$args = array(
				'id' => array(
					'description' => __( 'The ID of the image to retrieve.', 'wapuus-api' ),
					'type'        => 'integer',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "image get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function checkPermissions( \WP_REST_Request $request ) {

			return true;
		}

		/**
		 * Callback for the "image get" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$postId = absint( $request['id'] );
			$post   = get_post( $postId );

			if ( ! isset( $post ) || empty( $postId ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NotFound( __( 'Image not found.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$image          = Helpers::getPostData( $post );
			$image['views'] = (int) $image['views'] + 1;

			update_post_meta( $postId, 'views', $image['views'] );

			$comments = get_comments(
				array(
					'post_id' => $postId,
					'order'   => 'ASC',
				)
			);

			foreach ( $comments as $key => $comment ) {
				$comments[ $key ] = Helpers::getCommentData( $comment );
			}

			$image = array(
				'image'    => $image,
				'comments' => $comments,
			);

			$response = new \WapuusApi\Core\Responses\Valid\Ok( $image );

			return rest_ensure_response( $response );
		}
	}
