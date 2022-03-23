<?php
/**
 * The schema for a resource indicates what fields are present for a particular object.
 * When we register our routes we can also specify the resource schema for the route.
 * More details here: https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/#resource-schema
 *
 * @package \Wapuus_API\Src\Classes\Resource_Schema
 */

namespace Wapuus_API\Src\Classes;

if ( ! class_exists( 'Resource_Schema' ) ) {

	class Resource_Schema {

		use \Wapuus_API\Src\Traits\Singleton;

		public $comment = array();
		public $stats   = array();

		protected function init() {
			$this->comment = $this->get_comment_schema();
		}

		public function get_comment_schema() {
			$schema = array(
				// This tells the spec of JSON Schema we are using which is draft 4.
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				// The title property marks the identity of the resource.
				'title'      => 'comment',
				'type'       => 'object',
				// In JSON Schema you can specify object properties in the properties attribute.
				'properties' => array(
					'id'      => array(
						'description' => esc_html__( 'Unique identifier for the object.', 'my-textdomain' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'author'  => array(
						'description' => esc_html__( 'The id of the user object, if author was a user.', 'my-textdomain' ),
						'type'        => 'integer',
					),
					'content' => array(
						'description' => esc_html__( 'The content for the object.', 'my-textdomain' ),
						'type'        => 'string',
					),
				),
			);

			return $schema;
		}
	}
}
