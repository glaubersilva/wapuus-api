<?php
/**
 * Tests for the Wapuus API V2 "users" and "password" resources.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Tests\Core\Endpoints;

use Wapuus_API\Tests\Unit_API_Test_Case;

/**
 * Tests for users and password endpoints.
 */
class UsersAndPasswordTest extends Unit_API_Test_Case {

		/**
		 * Test the "user post" endpoint.
		 */
		public function testUserPost() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v2/users' );

			$bodyParams = array(
				'username' => 'usertest',
				'email'    => 'usertest@localhost.com',
				'url'      => 'http://localhost:3000',
			);

			$request->set_body_params( $bodyParams );

			$response = $this->server->dispatch( $request );

			$expected = 201;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data     = $response->get_data();
			$expected = $bodyParams['username'];
			$result   = $data['username'];
			$this->assertEquals( $expected, $result );
		}

		/**
		 * Test the "user get" endpoint.
		 */
		public function testUsersGet() {

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
		public function testPasswordLost() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v2/password/lost' );

			$bodyParams = array(
				'login' => $this->user_login,
				'url'   => 'http://localhost:3000',
			);

			$request->set_body_params( $bodyParams );

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
		public function testPasswordReset() {

			$request = new \WP_REST_Request( 'POST', '/wapuus-api/v2/password/reset' );

			$user = get_user_by( 'email', $this->user_email );

			$bodyParams = array(
				'login'    => $this->user_login,
				'password' => 'newpasswordtest4003403',
				'key'      => get_password_reset_key( $user ),
			);

			$request->set_body_params( $bodyParams );

			$response = $this->server->dispatch( $request );

			$expected = 200;
			$result   = $response->get_status();
			$this->assertEquals( $expected, $result );

			$data = $response->get_data(); // Should return the message "Password has been changed" that shoulb be a string value.
			$this->assertIsString( $data );
		}
	}
