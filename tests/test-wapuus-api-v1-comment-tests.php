<?php

namespace Wapuus_API\Tests;

class Wapuus_API_V1_Comment_Tests extends Unit_API_Test_Case {

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

	public function test_comment_post() {

		$post_id = $this->factory->post->create(
			array(
				'post_title'  => 'My Wapuu',
				'post_type'   => 'wapuu',
				'post_status' => 'publish',
			)
		);

		$request = new \WP_REST_Request( 'POST', '/wapuus-api/v1/comment/' . $post_id );

		$comment = 'Beautiful colors! =)';

		$request_query = array(
			'comment' => $comment,
		);

		$request->set_query_params( $request_query );

		$response = $this->server->dispatch( $request );

		$expected = 200;
		$result   = $response->get_status();
		$this->assertEquals( $expected, $result );

		$data     = $response->get_data();
		$expected = $comment;
		$result   = $data->comment_content;
		$this->assertEquals( $expected, $result );

		$headers  = $response->get_headers();
		$expected = $headers;
		$result   = array();
		$this->assertEquals( $expected, $result );
	}
}
