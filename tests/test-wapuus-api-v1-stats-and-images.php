<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
/**
 * Tests for the Wapuus API V1 "stats" and "images" resources.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Tests;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wapuus_API_V1_Stats_And_Images_Tests' ) ) {

	/**
	 * Tests Class.
	 */
	class Wapuus_API_V1_Stats_And_Images_Tests extends Unit_API_Test_Case {

		/**
		 * Run before each test in a class.
		 */
		public function set_up() {
			parent::set_up();

			$this->image_sample_view = random_int( 100, 999 );
			$this->image_sample_id   = $this->factory->post->create(
				array(
					'post_title'  => 'My Wapuu',
					'post_type'   => 'wapuu',
					'post_status' => 'publish',
					'meta_input'  => array(
						'name'     => 'The Original Wapuu',
						'from'     => 'WordPress Japan',
						'from_url' => 'https://ja.wordpress.org/',
						'caption'  => 'This is the first one Wappu from the world!',
						'views'    => $this->image_sample_view,
					),
				)
			);

			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

			$files = array(
				'img' => $this->temp_file_data(),
			);

			$this->media_sample_id = media_handle_sideload( $files['img'], $this->image_sample_id );

			update_post_meta( $this->image_sample_id, 'img', $this->media_sample_id );
			set_post_thumbnail( $this->image_sample_id, $this->media_sample_id );
		}

		/**
		 * Test the "stats get" endpoint.
		 */
		public function test_stats_get() {

			$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/stats' );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data     = $response->get_data();
			$expected = $this->image_sample_view;
			$result   = $data[0]['views'];
			$this->assertEquals( $expected, $result );

			$headers  = $response->get_headers();
			$expected = $headers;
			$result   = array();
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Sample temp file data for using on the tests.
		 */
		public function temp_file_data() {

			$file_path = WAPUUS_API_DIR . '/tests/images/original-wapuu.png';
			$file_name = basename( $file_path );

			$tmpfname = wp_tempnam( $file_name );

			$fp = fopen( $tmpfname, 'w+' ); //phpcs:ignore

			if ( ! $fp ) {
				return new \WP_Error(
					'rest_upload_file_error',
					__( 'Could not open file handle.' ),
					array( 'status' => 500 )
				);
			}

			fwrite( $fp, file_get_contents( $file_path ) ); //phpcs:ignore
			fclose( $fp ); //phpcs:ignore

			$file_data = array(
				'name'     => $file_name,
				'type'     => 'image/png',
				'tmp_name' => $tmpfname,
				'error'    => 0,
				'size'     => filesize( $tmpfname ),
			);

			return $file_data;
		}

		/**
		 * Test the "image post" endpoint.
		 */
		public function test_image_post() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v1/images' );

			$img = array(
				'img' => $this->temp_file_data(),
			);

			$body_params = array(
				'name'     => 'The Original Wapuu',
				'from'     => 'WordPress Japan',
				'from_url' => 'https://ja.wordpress.org/',
				'caption'  => 'This is the first one Wappu from the world!',
			);

			$request->set_body_params( $body_params );
			$request->set_file_params( $img );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test the "images get" endpoint.
		 */
		public function test_images_get() {

			$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/images' );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$headers  = $response->get_headers();
			$expected = $headers;
			$result   = array();
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test the "image get" endpoint.
		 */
		public function test_image_get() {

			$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/images/' . $this->image_sample_id );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$headers  = $response->get_headers();
			$expected = $headers;
			$result   = array();
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test the "image delete" endpoint.
		 */
		public function test_image_delete() {

			$request = new \WP_REST_Request( 'DELETE', '/wapuus-api/v1/images/' . $this->image_sample_id );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data     = $response->get_data();
			$expected = 'Deleted.';
			$result   = $data;
			$this->assertEquals( $expected, $result );

			$headers  = $response->get_headers();
			$expected = $headers;
			$result   = array();
			$this->assertEquals( $expected, $result );
		}
	}
}
