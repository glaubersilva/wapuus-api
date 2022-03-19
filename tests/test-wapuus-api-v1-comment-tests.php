<?php

namespace Wapuus_API\Tests;

class Wapuus_API_V1_Comment_Tests extends Unit_API_Test_Case {

	public $comment_sample = 'This is a sample comment! =)';
	public $post_sample_id;

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

		$this->post_sample_id = $this->factory->post->create(
			array(
				'post_title'  => 'My Wapuu',
				'post_type'   => 'wapuu',
				'post_status' => 'publish',
			)
		);
	}

	public function tear_down() {
		parent::tear_down();
	}

	public function test_comment_post() {

		$request = new \WP_REST_Request( 'POST', '/wapuus-api/v1/comment/' . $this->post_sample_id );

		$request_query = array(
			'comment' => $this->comment_sample,
		);

		$request->set_query_params( $request_query );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$data     = $response->get_data();
		$expected = $this->comment_sample;
		$result   = $data->comment_content;
		$this->assertEquals( $expected, $result );

		$headers  = $response->get_headers();
		$expected = $headers;
		$result   = array();
		$this->assertEquals( $expected, $result );
	}

	public function test_comment_get() {

		$user = wp_get_current_user();

		$response = array(
			'user_id'         => $user->ID,
			'comment_author'  => $user->user_login,
			'comment_content' => $this->comment_sample,
			'comment_post_ID' => $this->post_sample_id,
		);

		$comment_id = wp_insert_comment( $response );

		$request = new \WP_REST_Request( 'GET', '/wapuus-api/v1/comment/' . $this->post_sample_id );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$data     = $response->get_data();
		$expected = $this->comment_sample;
		$result   = $data[0]->comment_content;
		$this->assertEquals( $expected, $result );

		$expected = $comment_id;
		$result   = $data[0]->comment_ID;
		$this->assertEquals( $expected, $result );

		$headers  = $response->get_headers();
		$expected = $headers;
		$result   = array();
		$this->assertEquals( $expected, $result );
	}
}
