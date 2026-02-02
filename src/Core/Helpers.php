<?php
/**
 * Helper methods used across the Core API.
 *
 * @package WapuusApi
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core;

/**
 * Helper methods for Wapuus API.
 */
final class Helpers {

	/**
	 * Get data from Wapuus posts.
	 *
	 * @param \WP_Post|int $post The post object or int.
	 * @return array|false Post data array or false on invalid input.
	 */
	public static function getPostData( $post ) {
		if ( ! $post instanceof \WP_Post && ! is_numeric( $post ) ) {
			return false;
		}

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		$postMeta      = get_post_meta( $post->ID );
		$src           = wp_get_attachment_image_src( $postMeta['img'][0], 'large' )[0];
		$user          = get_userdata( $post->post_author );
		$totalComments = get_comments_number( $post->ID );

		return array(
			'id'             => $post->ID,
			'author'         => $user->user_login,
			'title'          => $post->post_title,
			'date'           => $post->post_date,
			'src'            => $src,
			'from'           => $postMeta['from'][0],
			'from_url'       => esc_url( $postMeta['from_url'][0] ),
			'caption'        => $postMeta['caption'][0],
			'views'          => $postMeta['views'][0],
			'total_comments' => $totalComments,
		);
	}

	/**
	 * Get data from Wapuus comments.
	 *
	 * @param \WP_Comment|int $comment The comment object or int.
	 * @return array|false Comment data array or false on invalid input.
	 */
	public static function getCommentData( $comment ) {
		if ( ! $comment instanceof \WP_Comment && ! is_numeric( $comment ) ) {
			return false;
		}

		if ( is_numeric( $comment ) ) {
			$comment = get_comment( $comment );
		}

		return array(
			'id'        => $comment->comment_ID,
			'comment'   => $comment->comment_content,
			'author'    => $comment->comment_author,
			'parent_id' => $comment->comment_post_ID,
		);
	}

	/**
	 * Check if the user is a demo user.
	 *
	 * @param \WP_User|int $user The user object or int.
	 * @return bool True for a demo user, false otherwise.
	 */
	public static function isDemoUser( $user ) {
		if ( ! defined( 'WAPUUS_API_DEMO_USER_RESTRICTED' ) || ! WAPUUS_API_DEMO_USER_RESTRICTED ) {
			return false;
		}

		if ( is_numeric( $user ) ) {
			$user = get_userdata( $user );
		}

		return $user && 'demo' === strtolower( $user->user_login );
	}
}
