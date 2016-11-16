<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

require_once( dirname( __FILE__ ) . "/lib/functions.php" );

/**
 * Generate a new VCCW environment.
 *
 * @subpackage commands/community
 * @maintainer Takayuki Miyauchi
 */
class WP_CLI_Scaffold_VCCW extends WP_CLI_Command
{
	/**
	 * Generate a new VCCW environment.
	 *
	 * ## OPTIONS
	 *
	 * <directory>
	 * : The directory of the new VCCW based guest machine.
	 *
	 * [--host=<hostname>]
	 * : Hostname of the guest machine. Default is `vccw.dev`.
	 *
	 * [--ip=<ip-address>]
	 * : IP address of the guest machine. Default is `192.168.33.10`.
	 *
	 * [--lang=<language>]
	 * : Language of the WordPress. Default is `en_US`.
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
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating: ', 10 );

		if ( empty( $assoc_args["host"] ) ) {
			$assoc_args["host"] = "vccw.dev";
		}

		if ( empty( $assoc_args["ip"] ) ) {
			$assoc_args["ip"] = "192.168.33.10";
		}

		if ( empty( $assoc_args["lang"] ) ) {
			$assoc_args["lang"] = "en_US";
		}

		$path = preg_replace( "#/$#", "", $args[0] );

		$url = Scaffold_VCCW::get_latest_vccw_url();
		if ( ! $url ) {
			WP_CLI::error( "Can't connect GitHub's API. Please try later." );
		}
		usleep( 100000 );
		$progress->tick();

		$zip = Scaffold_VCCW::download( $url );
		if ( ! $zip ) {
			WP_CLI::error( "Can't download zip. Please try later." );
		}
		usleep( 100000 );
		$progress->tick( 5 );

		$file = tempnam( sys_get_temp_dir(), "" );
		file_put_contents( $file, $zip );
		usleep( 100000 );
		$progress->tick();

		try {
			$dir = Scaffold_VCCW::tempdir();
			Scaffold_VCCW::unzip( $file, $dir );
			usleep( 100000 );
			$progress->tick();
		} catch (Exception $e) {
			Scaffold_VCCW::rrmdir( $dir );
			WP_CLI::error( $e->getMessage() );
		}

		if ( is_dir( $dir ) ) {
			if ( $dh = opendir( $dir ) ) {
				while ( ( $file = readdir( $dh ) ) !== false ) {
					$src = $dir . "/" . $file;
					if ( preg_match( "/^vccw/", $file ) && is_dir( $src ) ) {
						Scaffold_VCCW::rcopy( $src, $path );
						usleep( 100000 );
						$progress->tick();
						break;
					}
				}
				closedir( $dh );
			}
		}

		$sitefile = WP_CLI\Utils\mustache_render(
			Scaffold_VCCW::get_yml_template(),
			array(
				'host' => $assoc_args["host"],
				'ip' => $assoc_args["ip"],
				'lang' => $assoc_args["lang"],
			)
		);

		file_put_contents( $path . '/site.yml', $sitefile );

		Scaffold_VCCW::rrmdir( $dir );
		usleep( 100000 );
		$progress->finish();

		WP_CLI::success( "Generated. Run `vagrant up`." );
	}
}

WP_CLI::add_command( 'scaffold vccw', 'WP_CLI_Scaffold_VCCW'  );
