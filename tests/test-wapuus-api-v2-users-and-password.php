<?php // phpcs:ignore Class file names should be based on the class name with "class-" prepended.
/**
 * Tests for the Wapuus API V2 "users" and "password" resources.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Tests;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wapuus_API_V2_Users_And_Password_Tests' ) ) {

	/**
	 * Tests Class.
	 */
	class Wapuus_API_V2_Users_And_Password_Tests extends Unit_API_Test_Case {

		/**
		 * Test the "user post" endpoint.
		 */
		public function test_user_post() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v2/users' );

			$body_params = array(
				'username' => 'usertest',
				'email'    => 'usertest@localhost.com',
				'url'      => 'http://localhost:3000',
			);

			$request->set_body_params( $body_params );

			$response = $this->server->dispatch( $request );

			$expected = 201;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data     = $response->get_data();
			$expected = $body_params['username'];
			$result   = $data['username'];
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test the "user get" endpoint.
		 */
		public function test_users_get() {

			$request = new \WP_REST_Request( 'GET', '/wapuus-api/v2/users' );

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

		/**
		 * Test the "password lost" endpoint.
		 */
		public function test_password_lost() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v2/password/lost' );

			$body_params = array(
				'login' => $this->user_login,
				'url'   => 'http://localhost:3000',
			);

			$request->set_body_params( $body_params );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data = $response->get_data(); // Should return the message "Email sent" that shoulb be a string value.
			$this->assertIsString( $data );
		}

		/**
		 * Test the "password reset" endpoint.
		 */
		public function test_password_reset() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v2/password/reset' );

			$user = get_user_by( 'email', $this->user_email );

			$body_params = array(
				'login'    => $this->user_login,
				'password' => 'newpasswordtest4003403',
				'key'      => get_password_reset_key( $user ),
			);

			$request->set_body_params( $body_params );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data = $response->get_data(); // Should return the message "Password has been changed" that shoulb be a string value.
			$this->assertIsString( $data );
		}

	}
}
