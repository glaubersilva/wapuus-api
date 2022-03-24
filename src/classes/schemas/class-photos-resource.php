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
					'id'           => array(
						'description' => __( 'Unique identifier for the photo.' ),
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
