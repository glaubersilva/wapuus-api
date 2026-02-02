<?php
/**
 * Schema for the stats resource.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace WapuusApi\Core\Schemas;

use \WapuusApi\Core\Schemas\AbstractResource;
use \WapuusApi\Traits\Singleton;
	/**
	 * Schema stats for the images resource.
	 */
	class StatsResource extends AbstractResource {

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
