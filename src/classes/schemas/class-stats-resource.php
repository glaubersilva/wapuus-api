<?php
/**
 * Schema for the stats resource.
 *
 * @package \Wapuus_API\Src\Classes\Schemas\Stats_Resource
 */

namespace Wapuus_API\Src\Classes\Schemas;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Stats_Resource' ) ) {

	class Stats_Resource extends Abstract_Resource {

		use Singleton;

		protected function init() {

			$this->name = 'stats';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'stats',
				'type'       => 'object',
				'properties' => array(
					'id'    => array(
						'description' => __( 'Unique identifier for the photo object.' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'title'  => array(
						'description' => __( 'The name of the photo object.' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'views' => array(
						'description' => __( 'The views counter of the photo object.' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
				),
			);

			return $this->schema;
		}
	}
}
