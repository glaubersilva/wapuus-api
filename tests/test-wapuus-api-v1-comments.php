<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
/**
 * Tests for the Wapuus API V1 "comments" resource.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Tests;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wapuus_API_V1_Comments_Tests' ) ) {

	/**
	 * Tests Class.
	 */
	class Wapuus_API_V1_Comments_Tests extends Unit_API_Test_Case {

		/**
		 * Sample comment.
		 *
		 * @var string
		 */
		public $comment_sample = 'This is a sample comment! =)';

		/**
		 * Sample comment ID.
		 *
		 * @var int
		 */
		public $comment_sample_id;

		/**
		 * Sample comment parent ID.
		 *
		 * @var string
		 */
		public $post_sample_id;

		/**
		 * Run before each test in a class.
		 */
		public function set_up() {
			parent::set_up();

			$this->photo_sample_id = $this->factory->post->create(
				array(
					'post_title'  => 'My Wapuu',
					'post_type'   => 'wapuu',
					'post_status' => 'publish',
				)
			);

			$user = wp_get_current_user();

			$response = array(
				'user_id'         => $user->ID,
				'comment_author'  => $user->user_login,
				'comment_content' => $this->comment_sample,
				'comment_post_ID' => $this->photo_sample_id,
			);

			$this->comment_sample_id = wp_insert_comment( $response );
		}

		/**
		 * Test the "coment post" endpoint.
		 */
		public function test_comments_post() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v1/comments/' . $this->photo_sample_id );

			$request_query = array(
				'comment' => $this->comment_sample,
			);

			$request->set_query_params( $request_query );

			$response = $this->server->dispatch( $request );

			$expected = 201;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data     = $response->get_data();
			$expected = $this->comment_sample;
			$result   = $data['comment'];
			$this->assertEquals( $expected, $result );

			$headers  = $response->get_headers();
			$expected = $headers;
			$result   = array();
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test the "coments get" endpoint.
		 */
		public function test_comments_get() {

			$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/comments/' . $this->photo_sample_id );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data     = $response->get_data();
			$expected = $this->comment_sample;
			$result   = $data[0]['comment'];
			$this->assertEquals( $expected, $result );

			$expected = $this->comment_sample_id;
			$result   = $data[0]['id'];
			$this->assertEquals( $expected, $result );

			$headers  = $response->get_headers();
			$expected = $headers;
			$result   = array();
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test the "coment delete" endpoint.
		 */
		public function test_comment_delete() {

			$request = new \WP_REST_Request( 'DELETE', '/wapuus-api/v1/comments/' . $this->comment_sample_id );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data     = $response->get_data();
			$this->assertIsBool( $data );

			$headers  = $response->get_headers();
			$expected = $headers;
			$result   = array();
			$this->assertEquals( $expected, $result );
		}
	}
}
