<?php
/**
 * Schema for the images resource.
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Classes\Schemas;

defined( 'ABSPATH' ) || exit;

use \Wapuus_API\Src\Classes\Schemas\Abstract_Resource;
use \Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Images_Resource' ) ) {

	/**
	 * Schema class for the images resource.
	 */
	class Images_Resource extends Abstract_Resource {

		use Singleton;

		/**
		 * Define the name and schema for the images resource.
		 */
		protected function init() {

			$this->name = 'images';

			$this->schema = array(
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				'title'      => 'image',
				'type'       => 'object',
				'properties' => array(
					'id'             => array(
						'description' => __( 'Unique identifier for the image.', 'wapuus-api' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'author'         => array(
						'description' => __( 'The username of the user object.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'title'          => array(
						'description' => __( 'The name of the image object.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'date'           => array(
						'description' => __( "The date the image was published, in the site's timezone.", 'wapuus-api' ),
						'type'        => array( 'string', 'null' ),
						'format'      => 'date-time',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'src'            => array(
						'description' => __( 'URL to the image file.', 'wapuus-api' ),
						'type'        => 'string',
						'format'      => 'uri',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'from'           => array(
						'description' => __( 'The source of the image.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'from_url'       => array(
						'description' => __( 'URL to the source of the image.', 'wapuus-api' ),
						'type'        => 'string',
						'format'      => 'uri',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'caption'        => array(
						'description' => __( 'The caption of the image.', 'wapuus-api' ),
						'type'        => 'string',
						'context'     => array( 'view', 'edit', 'embed' ),
					),
					'views'          => array(
						'description' => __( 'The views counter of the image.', 'wapuus-api' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'total_comments' => array(
						'description' => __( 'The comments counter of the image.', 'wapuus-api' ),
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
