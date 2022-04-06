<?php
/**
 * Schema for the comments resource.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Schemas;

defined( 'ABSPATH' ) || exit;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Comments_Resource' ) ) {

	/**
	 * Schema class for the comments resource.
	 */
	class Comments_Resource extends Abstract_Resource {

		use Singleton;

		/**
		 * Define the name and schema for the comments resource.
		 */
		protected function init() {

			$this->name = 'comments';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'comment',
				'type'       => 'object',
				'properties' => array(
					'id'        => array( // comment_ID property from the WP_Comment object.
						'description' => __( 'Unique identifier for the comment.', 'wapuus-api' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'comment'   => array( // comment_content property from the WP_Comment object.
						'description' => __( 'The content of the comment.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'author'    => array( // comment_author property from the WP_Comment object.
						'description' => __( 'The username of the user object.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'parent_id' => array( // comment_post_ID property from the WP_Comment object.
						'description' => __( 'Unique identifier for the parent image object of the comment.', 'wapuus-api' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
				),
			);

			return $this->schema;
		}
	}
}
