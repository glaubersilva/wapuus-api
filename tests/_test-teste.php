<?php
/**
 * Class Date Helpers Test
 *
 * @package Mensure
 */

/**
 * Sample test case.
 */
class wapuus_api_test_teste extends WP_UnitTestCase {

	/**
	 * me_get_days_interval_between_two_dates($date1, $date2) test.
	 */
	public function test_teste() {
		// Replace this with some actual testing code.
		//$this->assertTrue( true );

		$date1    = 1634515200; //  18/10/2021
		$date2    = 1634947200; // 23/10/2021
		$expected = true;
		$result   = true;		
		$this->assertEquals($expected, $result);
	}
}
