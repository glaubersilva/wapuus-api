<?php

namespace Wapuus_API\Src\Classes\Responses\Valid;

/**
 * A REST API response for a "201 Created" response.
 */
class Created extends WP_REST_Response {

	/**
	 * Constructor.
	 *
	 * @param mixed $data
	 * @param array $headers
	 */
	public function __construct( $data = null, array $headers = array() ) {
		parent::__construct( $data, 201, $headers );
	}
}
