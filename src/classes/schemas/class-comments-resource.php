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

			return $this->schema;
		}
	}
}
