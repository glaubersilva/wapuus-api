<?php
/**
 * Schema for the users resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Users_Resource
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Users_Resource' ) ) {

	class Users_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$this->name = 'users';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'user',
				'type'       => 'object',
				'properties' => array(
					'id'       => array(
						'description' => __( 'Unique identifier for the user.' ),
						'type'        => 'integer',
						'context'     => array( 'embed', 'view', 'edit' ),
						'readonly'    => true,
					),
					'username' => array(
						'description' => __( 'Login name for the user.' ),
						'type'        => 'string',
						'context'     => array( 'edit' ),
						'required'    => true,
						'arg_options' => array(
							'sanitize_callback' => array( $this, 'check_username' ),
						),
					),
					'name'     => array(
						'description' => __( 'Display name for the user.' ),
						'type'        => 'string',
						'context'     => array( 'embed', 'view', 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
					'email'    => array(
						'description' => __( 'The email address for the user.' ),
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
