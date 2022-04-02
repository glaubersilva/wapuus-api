<?php

/**
 * Get data from Wapuus posts.
 *
 * @param object|WP_Post|int $post The post object or int.
 */
function wappus_api_get_post_data( $post ) {

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

	$return = array(
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

	return $return;
}

function wappus_api_get_comment_data( $comment ) {

	if ( ! $comment instanceof WP_Comment && ! is_numeric( $comment ) ) {
		return false;
	}

	if ( is_numeric( $comment ) ) {
		$comment = get_comment( $comment );
	}

	$return = array(
		'id' => $comment->comment_ID,
		'comment'    => $comment->comment_content,		
		'author'     => $comment->comment_author,
		//'parent_id'   => $comment->comment_post_ID,		
	);

	return $return;
}


function wapuus_api_is_demo_user( $user ) {

	$is_demo_user = false;

	if ( defined( 'WAPUUS_API_DEMO_USER_RESTRICTED' ) && WAPUUS_API_DEMO_USER_RESTRICTED ) {

		if ( is_numeric( $user ) ) {
			$user = get_userdata( $user );
		}

		if ( 'demo' == strtolower( $user->user_login ) ) {
			$is_demo_user = true;
		}
	}

	return $is_demo_user;
}
