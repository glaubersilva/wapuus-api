<?php
/**
 * Schema for the comments resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Comments_Resource
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Comments_Resource' ) ) {

	class Comments_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$this->name = 'comments';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'comment',
				'type'       => 'object',
				'properties' => array(
					'id'           => array(
						'description' => __( 'Unique identifier for the comment.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'author'       => array(
						'description' => __( 'The ID of the user object, if author was a user.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'author_email' => array(
						'description' => __( 'Email address for the comment author.' ),
						'type'        => 'string',
						'format'      => 'email',
						'context'     => array( 'edit' ),
						'arg_options' => array(
							'sanitize_callback' => array( $this, 'check_comment_author_email' ),
							'validate_callback' => null, // Skip built-in validation of 'email'.
						),
					),
				),
			);

			return $this->schema;
		}
	}
}
