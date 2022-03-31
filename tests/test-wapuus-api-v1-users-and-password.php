<?php

namespace Wapuus_API\Tests;

class Wapuus_API_V1_Users_And_Password_Tests extends Unit_API_Test_Case {

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

	public function tear_down() {
		parent::tear_down();
	}

	public function test_user_post() {

		$request = new \WP_REST_Request( 'POST', '/wapuus-api/v1/users' );

		$body_params = array(
			'username' => 'usertest',
			'email'    => 'usertest@localhost.com',
			'url'      => 'http://localhost:3000',
		);

		$request->set_body_params( $body_params );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$data = $response->get_data(); // Should return the new user ID that shoulb be a int value.
		$this->assertIsInt( $data );
	}


	public function test_users_get() {

		$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/users' );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$data     = $response->get_data();
		$expected = $this->user_email;
		$result   = $data['email'];
		$this->assertEquals( $expected, $result );

		$headers  = $response->get_headers();
		$expected = $headers;
		$result   = array();
		$this->assertEquals( $expected, $result );
	}
}
