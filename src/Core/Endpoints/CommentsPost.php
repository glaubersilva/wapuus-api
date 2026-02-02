<?php
/**
 * The API V2 endpoint for "comment post".
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Endpoints;

use WapuusApi\Core\Endpoints\AbstractEndpoint;
use WapuusApi\Core\Schemas\CommentsResource;
use WapuusApi\Core\Helpers;
	/**
	 * The "comment post" endpoint class.
	 */
	class CommentsPost extends AbstractEndpoint {

		/**
		 * Route for the "comment post" endpoint.
		 */
		public function getPath() {
			return '/' . CommentsResource::getInstance()->getName() . '/(?P<id>[0-9]+)';
		}

		/**
		 * Resource schema callback for the "comment post" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resourceSchema() {
			return CommentsResource::getInstance()->getSchema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "comment post" endpoint.
		 */
		public function getMethods() {
			return \WP_REST_Server::CREATABLE;
		}

		/**
		 * Schema of the expected arguments for the "comment post" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function getArguments() {

			$args = array(
				'id'      => array(
					'description' => __( 'The ID of the image object that will receive the comment.', 'wapuus-api' ),
					'type'        => 'integer',
					'required'    => true,
				),
				'comment' => array(
					'description' => __( 'The content of the comment.', 'wapuus-api' ),
					'type'        => 'string',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "comment post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function checkPermissions( \WP_REST_Request $request ) {

			if ( ! is_user_logged_in() ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'User does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( Helpers::isDemoUser( get_current_user_id() ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			return true;
		}

		/**
		 * Callback for the "comment post" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$comment = sanitize_textarea_field( $request['comment'] );

			if ( empty( $comment ) ) {
				$response = new \WapuusApi\Core\Responses\Error\IncompleteData( __( 'The comment is required.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			$user   = wp_get_current_user();
			$postId = absint( $request['id'] );

			$newWpComment = array(
				'user_id'         => $user->ID,
				'comment_author'  => $user->user_login,
				'comment_content' => $comment,
				'comment_post_ID' => $postId,
			);

			$commentId = wp_insert_comment( $newWpComment );
			$comment   = get_comment( $commentId );
			$comment    = Helpers::getCommentData( $comment );

			$response = new \WapuusApi\Core\Responses\Valid\Created( $comment );

			return rest_ensure_response( $response );
		}
	}
