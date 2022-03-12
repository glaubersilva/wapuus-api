<?php

namespace Wapuus_API\Src\Classes\Reponses\Error;

/**
 * A WordPress error representing a "404 Not Found" REST API response.
 */
class Not_Found extends WP_Error {

	/**
	 * Constructor.
	 *
	 * @param string $code
	 * @param string $message
	 * @param mixed  $data
	 */
	public function __construct( $code = '', $message = '', $data = '' ) {

		if ( ! is_array( $data ) ) {
			$data = (array) $data;
		}

		$data = array_merge( $data, array( 'status' => 404 ) );

		parent::__construct( $code, $message, $data );
	}
}
