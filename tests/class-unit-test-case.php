<?php
/**
 * Base class for all tests.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Tests;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Classes\Wapuus_Custom_Post_Type;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;

if ( ! class_exists( 'Unit_Test_Case' ) ) {

	/**
	 * Test Case Class.
	 */
	class Unit_Test_Case extends \WP_UnitTestCase {

		use AssertIsType;

		/**
		 * Sample user ID.
		 *
		 * @var int
		 */
		protected $user_id;

		/**
		 * Sample user login.
		 *
		 * @var string
		 */
		protected $user_login = 'admin38974238473824';

		/**
		 * Sample user password.
		 *
		 * @var string
		 */
		protected $user_pass = 'admin38974238473824';

		/**
		 * Sample user email.
		 *
		 * @var string
		 */
		protected $user_email = 'admin38974238473824@test.com';

		/**
		 * Run before each test in a class.
		 */
		public function set_up() {
			parent::set_up();

			$new_admin_user = $this->factory()->user->create(
				array(
					'role'       => 'administrator',
					'user_login' => $this->user_login,
					'user_pass'  => $this->user_pass,
					'user_email' => $this->user_email,
				)
			);
			wp_set_current_user( $new_admin_user );
			$this->user_id = $new_admin_user;

			// Workaround for https://core.trac.wordpress.org/ticket/48300 bug.
			Wapuus_Custom_Post_Type::get_instance();
		}

		/**
		 * Run immediately after each test in the class.
		 */
		public function tear_down() { // phpcs:ignore
			parent::tear_down();
		}

		/**
		 * A single sample test.
		 */
		public function test_sample() {
			// Replace this with some actual testing code.
			$this->assertTrue( true );
		}
	}
}
