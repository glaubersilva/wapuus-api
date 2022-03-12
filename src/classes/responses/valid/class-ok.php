<?php

namespace Wapuus_API\Src\Classes\Responses\Valid;

/**
 * A REST API response for a "200 Ok" response.
 */
class OK extends WP_REST_Response {

	/**
	 * Constructor.
	 *
	 * @param mixed $data
	 * @param array $headers
	 */
	public function __construct( $data = null, array $headers = array() ) {
		parent::__construct( $data, 200, $headers );
	}
}
