<?php

use WP_CLI\Utils;

class Scaffold_VCCW
{
	/**
	 * Get URL of the latest VCCW.
	 *
	 * @return string Return URL or WP_Error object.
	 */
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
			return new WP_Error( "No such file or directory." );
		}
		$zip = new ZipArchive;
		$res = $zip->open( $src );
		if ( true === $res ) {
			// extract it to the path we determined above
			$zip->extractTo( $dest );
			$zip->close();
			return true;
		}
		return new WP_Error( "Can not open {$src}." );
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
		$src = untrailingslashit( $src );
		$dest = untrailingslashit( $dest );
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
		$dir = untrailingslashit( $dir );
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
		$dir = untrailingslashit( $dir );
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			$flags
		);
		return $iterator;
	}
}
