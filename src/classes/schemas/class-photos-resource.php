<?php
/**
 * Schema for the photos resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Photos_Resource
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Photos_Resource' ) ) {

	class Photos_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$this->name = 'photos';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'photo',
				'type'       => 'object',
				'properties' => array(
					'id'     => array(
						'description' => __( 'Unique identifier for the photo.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'author' => array(
						'description' => __( 'The username of the user object.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'title'  => array(
						'description' => __( 'The name of the photo object.' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'date'   => array(
						'description' => __( "The date the photo was published, in the site's timezone." ),
						'type'        => array( 'string', 'null' ),
						'format'      => 'date-time',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'src'    => array(
						'description' => __( 'URL to the photo file.' ),
						'type'        => 'string',
						'format'      => 'uri',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'from' => array(
						'description' => __( 'The source of the photo.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'from_url'    => array(
						'description' => __( 'URL to the source of the photo.' ),
						'type'        => 'string',
						'format'      => 'uri',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'caption' => array(
						'description' => __( 'The caption of the photo.' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'views'     => array(
						'description' => __( 'The views counter of the photo.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'total_comments'     => array(
						'description' => __( 'The comments counter of the photo.' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
				),
			);

			return $this->schema;
		}
	}
}
