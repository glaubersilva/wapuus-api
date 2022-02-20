<?php

namespace Wapuus_API\Src\Classes;

use Wapuus_API\Src\Classes\Endpoints\My_REST_Posts_Controller;

if ( ! class_exists( 'Load_Endpoints_V2' ) ) {

	class Load_Endpoints_V2 {

		// Here initialize all Rest controller classes
		public function __construct() {
			new My_REST_Posts_Controller();
		}
	}
}
