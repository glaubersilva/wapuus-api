<?php

namespace Wapuus_API\Tests;

class Wapuus_API_V1_Photo_Tests extends Unit_API_Test_Case {

	public $test_file;


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

		$request = new \WP_REST_Request( 'POST', '/wapuus-api/v1/photo' );

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
}
