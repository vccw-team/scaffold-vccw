<?php

/**
 * Generate a new VCCW environment.
 *
 * @subpackage commands/community
 * @maintainer Takayuki Miyauchi
 */
class Scaffold_VCCW_Command extends WP_CLI_Command
{
	/**
	 * Generate a new VCCW environment. bHHHH
	 *
	 * ## OPTIONS
	 *
	 * <directory>
	 * : The directory of the new VCCW based guest machine.
	 *
	 * [--host=<hostname>]
	 * : Hostname of the guest machine. Default is `vccw.test`.
	 *
	 * [--ip=<ip-address>]
	 * : IP address of the guest machine. Default is `192.168.33.10`.
	 *
	 * [--lang=<language>]
	 * : Language of the WordPress. Default is `en_US`.
	 *
	 * [--update]
	 * : Update files of the VCCW to latest version.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp scaffold vccw wordpress.dev
	 *     Generating:   100% [===========================] 0:03 / 0:06
	 *     Success: Generated.
	 *
	 *     $ wp scaffold vccw wordpress.dev --lang=ja
	 *     Generating:   100% [===========================] 0:03 / 0:06
	 *     Success: Generated.
	 *
	 * @when before_wp_load
	 */
	public function __invoke( $args, $assoc_args )
	{
		if ( empty( $assoc_args["host"] ) ) {
			$assoc_args["host"] = "vccw.test";
		}

		if ( empty( $assoc_args["ip"] ) ) {
			$assoc_args["ip"] = "192.168.33.10";
		}

		if ( empty( $assoc_args["lang"] ) ) {
			$assoc_args["lang"] = "en_US";
		}

		$update = \WP_CLI\Utils\get_flag_value( $assoc_args, 'update' );

		$path = preg_replace( "#/$#", "", $args[0] );
		if ( is_file( $path . '/site.yml' ) && true !== $update ) {
			WP_CLI::error( "`site.yml` already exists." );
		}

		$url = Scaffold_VCCW_Functions::get_latest_vccw_url();
		if ( ! $url ) {
			WP_CLI::error( "Can't connect GitHub's API. Please try later." );
		}

		$zip = Scaffold_VCCW_Functions::download( $url );
		if ( ! $zip ) {
			WP_CLI::error( "Can't download zip. Please try later." );
		}

		$file = tempnam( sys_get_temp_dir(), "" );
		file_put_contents( $file, $zip );

		try {
			$dir = Scaffold_VCCW_Functions::tempdir();
			Scaffold_VCCW_Functions::unzip( $file, $dir );
		} catch (Exception $e) {
			Scaffold_VCCW_Functions::rrmdir( $dir );
			WP_CLI::error( $e->getMessage() );
		}

		if ( is_dir( $dir ) ) {
			if ( $dh = opendir( $dir ) ) {
				while ( ( $file = readdir( $dh ) ) !== false ) {
					$src = $dir . "/" . $file;
					if ( preg_match( "/^vccw/", $file ) && is_dir( $src ) ) {
						Scaffold_VCCW_Functions::rcopy( $src, $path );
						break;
					}
				}
				closedir( $dh );
			}
		}

		Scaffold_VCCW_Functions::rrmdir( $dir );

		if ( true === $update ) {
			WP_CLI::success( "Updated. Run `vagrant up`." );
		} else {
			$sitefile = WP_CLI\Utils\mustache_render(
				Scaffold_VCCW_Functions::get_yml_template(),
				array(
					'host' => $assoc_args["host"],
					'ip' => $assoc_args["ip"],
					'lang' => $assoc_args["lang"],
				)
			);
			file_put_contents( $path . '/site.yml', $sitefile );
			WP_CLI::success( "Generated. Run `vagrant up`." );
		}
	}
}
