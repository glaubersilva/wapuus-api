<?php
/**
 * Helper functions that are used on the whole code base.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get data from Wapuus posts.
 *
 * @param WP_Post|int $post The post object or int.
 *
 * @return array Post Data.
 */
function wapuus_api_get_post_data( $post ) {

	if ( ! $post instanceof WP_Post && ! is_numeric( $post ) ) {
		return false;
	}

	if ( is_numeric( $post ) ) {
		$post = get_post( $post );
	}

	$post_meta      = get_post_meta( $post->ID );
	$src            = wp_get_attachment_image_src( $post_meta['img'][0], 'large' )[0];
	$user           = get_userdata( $post->post_author );
	$total_comments = get_comments_number( $post->ID );

	$post_data = array(
		'id'             => $post->ID,
		'author'         => $user->user_login,
		'title'          => $post->post_title,
		'date'           => $post->post_date,
		'src'            => $src,
		'from'           => $post_meta['from'][0],
		'from_url'       => esc_url( $post_meta['from_url'][0] ),
		'caption'        => $post_meta['caption'][0],
		'views'          => $post_meta['views'][0],
		'total_comments' => $total_comments,
	);

	return $post_data;
}

/**
 * Get data from Wapuus comments.
 *
 * @param WP_Comment|int $comment The comment object or int.
 *
 * @return array Comment Data.
 */
function wapuus_api_get_comment_data( $comment ) {

	if ( ! $comment instanceof WP_Comment && ! is_numeric( $comment ) ) {
		return false;
	}

	if ( is_numeric( $comment ) ) {
		$comment = get_comment( $comment );
	}

	$comment_data = array(
		'id'        => $comment->comment_ID,
		'comment'   => $comment->comment_content,
		'author'    => $comment->comment_author,
		'parent_id' => $comment->comment_post_ID,
	);

	return $comment_data;
}

/**
 * Check if the user is a demo user.
 *
 * @param WP_User|int $user The user object or int.
 *
 * @return bool Returns true for a demo user or false for a not demo user.
 */
function wapuus_api_is_demo_user( $user ) {

	$is_demo_user = false;

	if ( defined( 'WAPUUS_API_DEMO_USER_RESTRICTED' ) && WAPUUS_API_DEMO_USER_RESTRICTED ) {

		if ( is_numeric( $user ) ) {
			$user = get_userdata( $user );
		}

		if ( 'demo' === strtolower( $user->user_login ) ) {
			$is_demo_user = true;
		}
	}

	return $is_demo_user;
}
