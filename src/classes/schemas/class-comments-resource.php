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
					'comment_post_ID' => array(
						'description' => __( 'Unique identifier for the photo object.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'comment_author'  => array(
						'description' => __( 'The username of the user object.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'comment_ID'      => array(
						'description' => __( 'Unique identifier for the comment.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'comment_content' => array(
						'description' => __( 'The content of the comment.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
				),
			);

			return $this->schema;
		}
	}
}
