<?php

namespace Wapuus_API\Src\Classes;

use Wapuus_API\Src\Classes\Endpoints\My_REST_Posts_Controller;

if ( ! class_exists( 'Load_Endpoints' ) ) {

	class Load_Endpoints {

		// Here initialize all Rest controller classes
		public function __construct() {
			new My_REST_Posts_Controller();
		}
	}
}
