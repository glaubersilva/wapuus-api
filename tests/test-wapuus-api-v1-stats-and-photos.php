<?php

namespace Wapuus_API\Tests;

class Wapuus_API_V1_Stats_And_Photos_Tests extends Unit_API_Test_Case {

	/**
	 * A cada teste o WordPress é zerado e volta ao seu estado de logo após a instalação.
	 *
	 * O método SetUp vai rodar toda vez antes de cada teste (método que começa com test_).
	 *
	 * Vamos criar uns posts aqui pra não precisar ficar criando de novo a cada teste.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->photo_sample_view = random_int( 100, 999 );
		$this->photo_sample_id   = $this->factory->post->create(
			array(
				'post_title'  => 'My Wapuu',
				'post_type'   => 'wapuu',
				'post_status' => 'publish',
				'meta_input'  => array(
					'name'     => 'The Original Wapuu',
					'from'     => 'WordPress Japan',
					'from_url' => 'https://ja.wordpress.org/',
					'caption'  => 'This is the first one Wappu from the world!',
					'views'    => $this->photo_sample_view,
				),
			)
		);

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$files = array(
			'img' => $this->temp_file_data(),
		);

		$this->media_sample_id = media_handle_sideload( $files['img'], $this->photo_sample_id );

		update_post_meta( $this->photo_sample_id, 'img', $this->media_sample_id );
		set_post_thumbnail( $this->photo_sample_id, $this->media_sample_id );
	}

	public function test_stats_get() {

		$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/stats' );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$data     = $response->get_data();
		$expected = $this->photo_sample_view;
		$result   = $data[0]['views'];
		$this->assertEquals( $expected, $result );

		$headers  = $response->get_headers();
		$expected = $headers;
		$result   = array();
		$this->assertEquals( $expected, $result );
	}

	public function temp_file_data() {

		$file_path = WAPUUS_API_DIR . '/tests/images/original-wapuu.png';
		$file_name = basename( $file_path );

		$tmpfname = wp_tempnam( $file_name );

		$fp = fopen( $tmpfname, 'w+' );

		if ( ! $fp ) {
			return new WP_Error(
				'rest_upload_file_error',
				__( 'Could not open file handle.' ),
				array( 'status' => 500 )
			);
		}

		fwrite( $fp, file_get_contents( $file_path ) );
		fclose( $fp );

		$file_data = array(
			'name'     => $file_name,
			'type'     => 'image/png',
			'tmp_name' => $tmpfname,
			'error'    => 0,
			'size'     => filesize( $tmpfname ),
		);

		return $file_data;
	}

	public function tear_down() {
		parent::tear_down();
	}

	public function test_photo_post() {

		$request = new \WP_REST_Request( 'POST', '/wapuus-api/v1/photos' );

		$file_params = array(
			'img' => $this->temp_file_data(),
		);

		$body_params = array(
			'name'     => 'The Original Wapuu',
			'from'     => 'WordPress Japan',
			'from_url' => 'https://ja.wordpress.org/',
			'caption'  => 'This is the first one Wappu from the world!',
		);

		$request->set_body_params( $body_params );
		$request->set_file_params( $file_params );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );
	}

	public function test_photos_get() {

		$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/photos' );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$headers  = $response->get_headers();
		$expected = $headers;
		$result   = array();
		$this->assertEquals( $expected, $result );
	}

	public function test_photo_get() {

		$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/photos/' . $this->photo_sample_id );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$headers  = $response->get_headers();
		$expected = $headers;
		$result   = array();
		$this->assertEquals( $expected, $result );
	}

	public function test_photo_delete() {

		$request = new \WP_REST_Request( 'DELETE', '/wapuus-api/v1/photos/' . $this->photo_sample_id );

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
