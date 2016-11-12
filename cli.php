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
	 *
	 * ## EXAMPLES
	 *
	 *     # Basic usage
	 *     $ wp scaffold movefile
	 *     Success: /path/to/Movefile
	 */
	public function __invoke( $args, $assoc_args )
	{
	}
}

WP_CLI::add_command( 'scaffold vccw', 'WP_CLI_Scaffold_VCCW'  );
