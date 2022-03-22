<?php

namespace Wapuus_API\Tests;

use Wapuus_API\Src\Classes\Wapuus_Custom_Post_Type;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;

/**
 * Classe base para nossos testes.
 */
class Unit_Test_Case extends \WP_UnitTestCase {

	use AssertIsType;

	protected $user_id;

	protected $user_login = 'admin38974238473824';

	protected $user_pass = 'admin38974238473824';

	public function set_up() {

		parent::set_up();

		// Antes de cada teste, cria um novo admin e faz login com ele.
		$new_admin_user = $this->factory()->user->create(
			array(
				'role'       => 'administrator',
				'user_login' => $this->user_login,
				'user_pass'  => $this->user_pass,
			)
		);
		wp_set_current_user( $new_admin_user );
		$this->user_id = $new_admin_user;

		// Isso aqui contorna um bug no esquema de testes do WordPress que não persiste os metadados registrados.
		// Então temos que re-registrar tudo a cada teste.
		// workaround for https://core.trac.wordpress.org/ticket/48300
		// General_Tweaks::get_instance();
		 Wapuus_Custom_Post_Type::get_instance();
	}

	public function tear_down() {
		parent::tear_down();
	}

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}
}
