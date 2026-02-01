<?php
/**
 * Extends the default post types of WordPress registering the "Wapuus" CPT.
 *
 * @link https://developer.wordpress.org/plugins/post-types/
 *
 * @package Wapuus_API
 * @author Glauber Silva <info@glaubersilva.me>
 * @link https://glaubersilva.me/
 */

namespace Wapuus_API\Src\Core;

defined( 'ABSPATH' ) || exit;

use Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Wapuus_Custom_Post_Type' ) ) {

	/**
	 * This class registers the post type "wapuus" and its metadata
	 */
	class Wapuus_Custom_Post_Type {

		use Singleton;

		/**
		 * The name/slug of our Custom Post Type.
		 *
		 * @var string
		 */
		public $cpt_name = 'wapuu';

		/**
		 * Register our Custom Post Type in the "init" hook.
		 */
		protected function init() {
			add_action( 'init', array( $this, 'register_post_type' ) );
		}

		/**
		 * Callback to register our Custom Post Type.
		 */
		public function register_post_type() {

			$labels = array(
				'name'                     => __( 'Wapuus', 'wapuus-api' ),
				'singular_name'            => __( 'Wapuu', 'wapuus-api' ),
				'add_new'                  => __( 'Add New Wapuu', 'wapuus-api' ),
				'add_new_item'             => __( 'Add New Wapuu', 'wapuus-api' ),
				'edit_item'                => __( 'Edit Wapuu', 'wapuus-api' ),
				'new_item'                 => __( 'New Wapuu', 'wapuus-api' ),
				'all_items'                => __( 'All Wapuus', 'wapuus-api' ),
				'view_item'                => __( 'View Wapuu', 'wapuus-api' ),
				'search_items'             => __( 'Search Wapuus', 'wapuus-api' ),
				'not_found'                => __( 'No Wapuu Found', 'wapuus-api' ),
				'not_found_in_trash'       => __( 'No Wapuu found in the trash', 'wapuus-api' ),
				'menu_name'                => __( 'Wapuus', 'wapuus-api' ),
				'item_published'           => __( 'Wapuu published.', 'wapuus-api' ),
				'item_published_privately' => __( 'Wapuu published privately.', 'wapuus-api' ),
				'item_reverted_to_draft'   => __( 'Wapuu reverted to draft.', 'wapuus-api' ),
				'item_scheduled'           => __( 'Wapuu scheduled.', 'wapuus-api' ),
				'item_updated'             => __( 'Wapuu updated.', 'wapuus-api' ),
			);

			$args = array(
				'labels'              => $labels,
				'hierarchical'        => false,
				'description'         => __( 'Wapuus', 'wapuus-api' ),
				'supports'            => array( 'title', 'thumbnail', 'custom-fields' ),
				'rewrite'             => array( 'slug' => 'wapuus' ),
				'public'              => true,
				'show_in_menu'        => true,
				'show_in_rest'        => false,
				'rest_base'           => 'Wapuus',
				'has_archive'         => true,
				'exclude_from_search' => true,
			);

			register_post_type( $this->cpt_name, $args );
		}
	}
}
