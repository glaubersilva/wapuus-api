<?php
/**
 * The API V2 endpoint for "comment delete".
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
	 * The "comment delete" endpoint class.
	 */
	class CommentsDelete extends AbstractEndpoint {

		/**
		 * Route for the "comment delete" endpoint.
		 */
		public function getPath() {
			return '/' . CommentsResource::getInstance()->getName() . '/(?P<id>[0-9]+)';
		}

		/**
		 * Resource schema callback for the "comment delete" endpoint, which is the same
		 * for all methods (POST, GET, DELETE etc.) that the route accepts.
		 */
		public function resourceSchema() {
			return CommentsResource::getInstance()->getSchema();
		}

		/**
		 * Method (POST, GET, DELETE etc.) implemented for the "comment delete" endpoint.
		 */
		public function getMethods() {
			return \WP_REST_Server::DELETABLE;
		}

		/**
		 * Schema of the expected arguments for the "comment delete" endpoint.
		 *
		 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#argument-schema
		 *
		 * @return array Arguments.
		 */
		public function getArguments() {

			$args = array(
				'id' => array(
					'description' => __( 'The ID of the comment to delete.', 'wapuus-api' ),
					'type'        => 'integer',
					'required'    => true,
				),
			);

			return $args;
		}

		/**
		 * Permission callback for the "comment delete" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return true|\WP_Error Returns true on success or a WP_Error if it does not pass on the permissions check.
		 */
		public function checkPermissions( \WP_REST_Request $request ) {

			$user      = wp_get_current_user();
			$commentId = absint( $request['id'] );
			$comment   = get_comment( $commentId );

			if ( (int) $user->ID !== (int) $comment->user_id || ! isset( $comment ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'User does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			if ( Helpers::isDemoUser( $user ) ) {
				$response = new \WapuusApi\Core\Responses\Error\NoPermission( __( 'Demo user does not have permission.', 'wapuus-api' ) );
				return rest_ensure_response( $response );
			}

			return true;
		}

		/**
		 * Callback for the "comment delete" endpoint.
		 *
		 * @param \WP_REST_Request $request The current request object.
		 *
		 * @return \WP_REST_Response|\WP_Error If response generated an error, WP_Error, if response
		 *                                   is already an instance, WP_REST_Response, otherwise
		 *                                   returns a new WP_REST_Response instance.
		 */
		public function respond( \WP_REST_Request $request ) {

			$commentId = absint( $request['id'] );
			$deleted   = wp_delete_comment( $commentId, true );

			if ( $deleted ) {
				$response = new \WapuusApi\Core\Responses\Valid\Ok( true );
			} else {
				$response = new \WapuusApi\Core\Responses\Error\BadRequest( false );
			}

			return rest_ensure_response( $response );
		}
	}
