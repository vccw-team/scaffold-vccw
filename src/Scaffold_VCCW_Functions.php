<?php

use WP_CLI\Utils;

class Scaffold_VCCW_Functions
{
	/**
	 * Get path to the mustache template.
	 *
	 * @return string Path to the template.
	 */
	public static function get_yml_template()
	{
		$home = getenv( 'HOME' );
		if ( !$home ) {
			// sometime in windows $HOME is not defined
			$home = getenv( 'HOMEDRIVE' ) . getenv( 'HOMEPATH' );
		}

		$config = $home . '/.wp-cli';

		if ( is_file( $config . '/vccw.yml.mustache' ) ) {
			return $config . '/vccw.yml.mustache';
		} else {
			return dirname( __FILE__ ) . '/../templates/site.yml.mustache';
		}
	}

	/**
	 * Get URL of the latest VCCW.
	 *
	 * @return string Return URL or WP_Error object.
	 */
	public static function get_latest_vccw_url()
	{
		$api = "https://api.github.com/repos/vccw-team/vccw/releases/latest";
		$body = json_decode( self::download( $api ) );

		if ( ! empty( $body->assets[0] ) && ! empty( $body->assets[0]->browser_download_url ) ) {
			return $body->assets[0]->browser_download_url;
		} elseif ( $body ) {
			return $body->zipball_url;
		}
	}

	/**
	 * Download form `$url`.
	 *
	 * @return string Downloaded object.
	 */
	public static function download( $url )
	{
		$context = stream_context_create( array(
			'http' => [
				'method' => 'GET',
				'header' => [
					'User-Agent: PHP'
				]
			]
		) );

		return file_get_contents( $url, false, $context );
	}

	/**
	 * Unzip
	 *
	 * @param string $src  Path to the .zip archive.
	 * @param string $dest Path to extract .zip.
	 * @return string      `true` or WP_Error object.
	 */
	public static function unzip( $src, $dest )
	{
		if ( ! is_file( $src ) ) {
			throw new Exception( "No such file or directory." );
		}
		$zip = new ZipArchive;
		$res = $zip->open( $src );
		if ( true === $res ) {
			// extract it to the path we determined above
			$zip->extractTo( $dest );
			$zip->close();
			return true;
		}
		throw new Exception( "Can not open {$src}." );
	}

	/**
	 * Create a temporary working directory
	 *
	 * @param  string $prefix Prefix for the temporary directory you want to create.
	 * @return string         Path to the temporary directory.
	 */
	public static function tempdir( $prefix = '' )
	{
		$tempfile = tempnam( sys_get_temp_dir(), $prefix );
		if ( file_exists( $tempfile ) ) {
			unlink( $tempfile );
		}
		mkdir( $tempfile );
		if ( is_dir( $tempfile ) ) {
			return $tempfile;
		}
	}

	/**
	 * Copy directory recursively.
	 *
	 * @param  string $source  Path to the source directory.
	 * @param  string $dest    Path to the destination.
	 * @param  array  $exclude An array of the files to exclude.
	 * @return void
	 */
	public static function rcopy( $src, $dest, $exclude = array() )
	{
		$src = preg_replace( "#/$#", "", $src );
		$dest = preg_replace( "#/$#", "", $dest );
		if ( ! is_dir( $dest ) ) {
			mkdir( $dest, 0755 );
		}
		$iterator = self::get_files( $src );
		foreach ( $iterator as $item ) {
			if ( $item->isDir() ) {
				if ( ! is_dir( $dest . '/' . $iterator->getSubPathName() ) ) {
					mkdir( $dest . '/' . $iterator->getSubPathName() );
				}
			} else {
				if ( ! in_array( $iterator->getSubPathName(), $exclude ) ) {
					copy( $item, $dest . '/' . $iterator->getSubPathName() );
				}
			}
		}
	}

	/**
	 * Remove a directory recursively.
	 *
	 * @param  string $dir Path to the directory you want to remove.
	 * @return void
	 */
	public static function rrmdir( $dir )
	{
		self::rempty( $dir );
		rmdir( $dir );
	}

	/**
	 * Empty a directory recursively.
	 *
	 * @param  string $dir     Path to the directory you want to remove.
	 * @param  array  $exclude An array of the files to exclude.
	 * @return void
	 */
	public static function rempty( $dir, $excludes = array() )
	{
		$dir = preg_replace( "#/$#", "", $dir );
		$files = self::get_files( $dir, RecursiveIteratorIterator::CHILD_FIRST );
		foreach ( $files as $fileinfo ) {
			if ( $fileinfo->isDir() ) {
				$skip = false;
				foreach ( $excludes as $exclude ) {
					if ( 0 === strpos( $exclude, $files->getSubPathName() ) ) {
						$skip = true;
					}
				}
				if ( ! $skip ) {
					rmdir( $fileinfo->getRealPath() );
				}
			} else {
				if ( ! in_array( $files->getSubPathName(), $excludes ) ) {
					unlink( $fileinfo->getRealPath() );
				}
			}
		}
	}

	/**
	 * Get file's iterator object from the directory.
	 *
	 * @param string $dir   Path to the directory.
	 * @param string $flags Flags for the `RecursiveIteratorIterator()`.
	 * @return string       Literator object of the `RecursiveIteratorIterator()`.
	 */
	public static function get_files( $dir, $flags = RecursiveIteratorIterator::SELF_FIRST )
	{
		$dir = preg_replace( "#/$#", "", $dir );
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			$flags
		);
		return $iterator;
	}
}
