<?php

class Scaffold_VCCW_Test extends WP_UnitTestCase
{
	/**
	 * @test
	 */
	public function get_latest_vccw_url()
	{
		$url = Scaffold_VCCW::get_latest_vccw_url();
		$this->assertFalse( is_wp_error( $url ) );
	}
}
