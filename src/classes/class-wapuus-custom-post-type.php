<?php

namespace Wapuus_API\Src\Classes;

use Wapuus_API\Src\Traits\Singleton;

if ( ! class_exists( 'Wapuus_Custom_Post_Type' ) ) {

	/**
	 * Essa classe registra o post type wapuus e seus metadados
	 */
	class Wapuus_Custom_Post_Type {
		
		use Singleton;

		public $post_type = 'wapuu';

		protected function init() {
			add_action( 'init', array( $this, 'register_post_type' ) );
		}

		public function register_post_type() {

			$labels = array(
				'name' => __('Wapuus', WAPUUS_API_TEXT_DOMAIN),
				'singular_name' => __('Wapuu', WAPUUS_API_TEXT_DOMAIN),
				'add_new' => __('Add New Wapuu', WAPUUS_API_TEXT_DOMAIN),
				'add_new_item' => __('Add New Wapuu', WAPUUS_API_TEXT_DOMAIN),
				'edit_item' => __('Edit Wapuu', WAPUUS_API_TEXT_DOMAIN),
				'new_item' => __('New Wapuu', WAPUUS_API_TEXT_DOMAIN),
				'all_items' => __('All Wapuus', WAPUUS_API_TEXT_DOMAIN),				
				'view_item' => __('View Wapuu', WAPUUS_API_TEXT_DOMAIN),
				'search_items' => __('Search Wapuus', WAPUUS_API_TEXT_DOMAIN),
				'not_found' => __('No Wapuu Found', WAPUUS_API_TEXT_DOMAIN),
				'not_found_in_trash' => __('No Wapuu found in the trash', WAPUUS_API_TEXT_DOMAIN),
				'menu_name' => __('Wapuus', WAPUUS_API_TEXT_DOMAIN),
				'item_published' => __('Wapuu published.', WAPUUS_API_TEXT_DOMAIN),
				'item_published_privately' => __('Wapuu published privately.', WAPUUS_API_TEXT_DOMAIN),
				'item_reverted_to_draft' => __('Wapuu reverted to draft.', WAPUUS_API_TEXT_DOMAIN),
				'item_scheduled' => __('Wapuu scheduled.', WAPUUS_API_TEXT_DOMAIN),
				'item_updated' => __('Wapuu updated.', WAPUUS_API_TEXT_DOMAIN),
			);

			$args = array(
				'labels' => $labels,
				'hierarchical' => false,
				'description' => __('Wapuus', WAPUUS_API_TEXT_DOMAIN),
				'supports' => array( 'title', /*'editor', 'excerpt', 'page-attributes',*/ 'thumbnail', 'custom-fields'), // custom-fields é necessário pra habilitar os post_meta na API
				'rewrite' => array('slug' => 'wapuus'),
				'public' => true,
				'show_in_menu' => true, // Não vai aparecer no admin!
				'show_in_rest' => false, // Habilita esse post type na API.
				'rest_base' => 'Wapuus', // Faz com que o endpoint da API seja 'Wapuus' no plural, e não wapuu, no singular, como é o o slug do post type.
				'has_archive' => true,
				'exclude_from_search' => true,
			);

			register_post_type($this->post_type, $args);

			/*register_post_meta($this->post_type, 'idioma', [
				'show_in_rest' => true,
				'single' => true,
				'auth_callback' => '__return_true',
				'type' => 'string',
				'description' => __('The language spoken in the wapuu', WAPUUS_API_TEXT_DOMAIN)
			]);

			register_post_meta($this->post_type, 'ano', [
				'show_in_rest' => array(
					'schema' => array( 
						'type' => 'number',
						'minimum' => 1900,
					),
				),
				'single' => true,
				'auth_callback' => '__return_true',
				'description' => __('The year the wapuu was released', WAPUUS_API_TEXT_DOMAIN)
			]);

			register_post_meta($this->post_type, 'formato', [
				'show_in_rest' => array(
					'schema' => array(
						'type' => 'string',
						'enum' => array(
							'VHS',
							'DVD',
							'LaserDisc',
							'16mm',
							'35mm',
						),
					),
				), 
				'single' => true,
				'auth_callback' => '__return_true',
				'type' => 'string', 
				'description' => __('The format of the media', WAPUUS_API_TEXT_DOMAIN)
			]);

			register_post_meta($this->post_type, 'ficha', [
				'show_in_rest' => [
					'schema' => [
						'type' => 'object',
						'properties' => [
							'pais' => [
								'description' => 'País de produção (pode ser mais de um)', // se você vai usar só em português, não precisa internacionalizar.
								'type' => 'array', // vai ser um array de países
								'items' => [
									'type' => 'string' // cada item dentro do array vai ser uma string
								]
							],
							'diretor' => [
								'description' => 'Diretor (pode ser mais de um)',
								'type' => 'array',
								'items' => [
									'type' => 'string'
								]
							],
						]
					]

				],
				'single' => true,
				'auth_callback' => '__return_true',
				'type' => 'object',
				'description' => 'Países e diretores'
			]);

			register_post_meta($this->post_type, 'preco', [
				'show_in_rest' => true,
				'single' => true,
				'auth_callback' => [ $this, 'permission_check' ], // callback pra checar a permissão pra editar esse metadado
				'sanitize_callback' => [ $this, 'sanitize_preco' ], // callback pra filtrar o valor do metadado antes de inserir
				'type' => 'string',
				'description' => __('Price', WAPUUS_API_TEXT_DOMAIN)
			]);*/

		}

		/**
		 * Sanitizes the preco metadata value
		 * @param  mixed $value The value to be validated
		 * @return string
		 */
		public function sanitize_preco($value) {

			// Se não tiver R$ no começo da stirng, adiciona
			// Nota: isso é só um exemplo...
			if ( strpos( $value, 'R$ ' ) !== 0 ) {
				$value = 'R$ ' . $value;
			}

			return $value;

		}

		/**
		 * Só admins podem editar o preco
		 */
		public function permission_check() {
			return current_user_can('manage_options'); // uma permissão que só admins tem
		}

	}
}

//Wapuus::get_instance();

//add_action( 'init', array( 'Wapuus', 'get_instance' ), 999 );

/*add_action(
	'plugins_loaded',
	function() {
		\GS_Wapuus_API\Src\Classes\Wapuus::get_instance();
	}
);*/