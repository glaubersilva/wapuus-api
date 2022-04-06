<?php
/**
 * Schema for the stats resource.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Schemas;

defined( 'ABSPATH' ) || exit;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Stats_Resource' ) ) {

	/**
	 * Schema stats for the images resource.
	 */
	class Stats_Resource extends Abstract_Resource {

		use Singleton;

		/**
		 * Define the name and schema for the stats resource.
		 */
		protected function init() {

			$this->name = 'stats';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'stats',
				'type'       => 'object',
				'properties' => array(
					'id'    => array(
						'description' => __( 'Unique identifier for the image object.', 'wapuus-api' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'title' => array(
						'description' => __( 'The name of the image object.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'views' => array(
						'description' => __( 'The views counter of the image object.', 'wapuus-api' ),
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
