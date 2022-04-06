<?php
/**
 * Schema for the users resource.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Schemas;

defined( 'ABSPATH' ) || exit;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Users_Resource' ) ) {

	/**
	 * Schema stats for the users resource.
	 */
	class Users_Resource extends Abstract_Resource {

		use Singleton;

		/**
		 * Define the name and schema for the users resource.
		 */
		protected function init() {

			$this->name = 'users';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'user',
				'type'       => 'object',
				'properties' => array(
					'id'       => array(
						'description' => __( 'Unique identifier for the user.', 'wapuus-api' ),
						'type'        => 'integer',
						'context'     => array( 'embed', 'view', 'edit' ),
						'readonly'    => true,
					),
					'username' => array(
						'description' => __( 'Login name for the user.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'edit' ),
						'required'    => true,
					),
					'email'    => array(
						'description' => __( 'The email address for the user.', 'wapuus-api' ),
						'type'        => 'string',
						'format'      => 'email',
						'context'     => array( 'edit' ),
						'required'    => true,
					),
				),
			);

			return $this->schema;
		}
	}
}
