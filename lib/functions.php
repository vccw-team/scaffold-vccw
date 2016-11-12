<?php

use WP_CLI\Utils;

class Scaffold_VCCW
{
	public static function get_latest_vccw_url()
	{
		$api = "https://api.github.com/repos/vccw-team/vccw/releases/latest";
		$res = wp_remote_get( $api );

		if ( is_wp_error( $res ) ) {
			return $res;
		}

		if ( 200 === $res['response']['code'] ) {
			$body = json_decode( $res['body'] );
			return $body->zipball_url;
		}
	}
}
