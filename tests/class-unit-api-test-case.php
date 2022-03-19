<?php

namespace Wapuus_API\Tests;

/**
 * Basic test case for api calls
 *
 * @author medialab
 */
class Unit_API_Test_Case extends Unit_Test_Case {
	/**
	 * Test REST Server
	 *
	 * @var \WP_REST_Server
	 */
	protected $server;

	/**
	 * Initialize WP API rest.
	 */
	public function set_up() {
		parent::set_up();

		global $wp_rest_server;

		$wp_rest_server = new \WP_REST_Server();
		$this->server   = $wp_rest_server;

		do_action( 'rest_api_init' );
	}

	/**
	 * Destruction API WP rest server.
	 */
	public function tear_down() {
		parent::tear_down();

		global $wp_rest_server;
		$wp_rest_server = null;
	}
}
