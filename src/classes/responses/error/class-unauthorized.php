<?php

namespace Wapuus_API\Src\Classes\Responses\Error;

/**
 * A WordPress error representing a "401 Unauthorized" REST API response.
 */
class Unauthorized extends \WP_Error {

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

		$data = array_merge( $data, array( 'status' => 401 ) );

		parent::__construct( $code, $message, $data );
	}
}
