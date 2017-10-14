<?php

class Scaffold_VCCW_Functions_Test extends WP_UnitTestCase
{
	/**
	 * Tests for the `Scaffold_VCCW_Functions::get_latest_vccw_url()`.
	 *
	 * @test
	 * @since 1.3.0
	 */
	public function get_latest_vccw_url()
	{
		$url = Scaffold_VCCW_Functions::get_latest_vccw_url();
		$this->assertRegExp( '#^https://github.com/vccw-team/vccw/releases/download#', $url );
	}

	/**
	 * Tests for the `Scaffold_VCCW_Functions::rempty()`.
	 *
	 * @test
	 * @since 0.1.0
	 */
	public function rempty()
	{
		$dir = self::mockdir();
		$files = Scaffold_VCCW_Functions::get_files( $dir );
		$this->assertSame( 7, iterator_count($files) );
		$dir = self::mockdir();
		Scaffold_VCCW_Functions::rempty( $dir );
		$files = Scaffold_VCCW_Functions::get_files( $dir );
		$this->assertSame( 0, iterator_count($files) );
		$dir = self::mockdir();
		Scaffold_VCCW_Functions::rempty( $dir, array(
			"dir02/dir02-01.txt",
			"dir01/dir01-01/dir01-01-01.txt"
		) );
		$files = Scaffold_VCCW_Functions::get_files( $dir );
		$this->assertSame( 5, iterator_count($files) );
	}

	/**
	 * Tests for the `Scaffold_VCCW_Functions::tempdir()`.
	 *
	 * @test
	 * @since 0.1.0
	 */
	public function tempdir()
	{
		$dir = Scaffold_VCCW_Functions::tempdir();
		$this->assertTrue( is_dir( $dir ) ); // $dir should exists.
	}

	/**
	 * Tests for the `Scaffold_VCCW_Functions::rrmdir()`.
	 *
	 * @test
	 * @since 0.1.0
	 */
	public function rrmdir()
	{
		$dir = self::mockdir();
		$this->assertTrue( is_dir( $dir ) ); // $dir should exists.
		Scaffold_VCCW_Functions::rrmdir( $dir );
		$this->assertFalse( is_dir( $dir ) ); // $dir should not exists.
	}

	/**
	 * Tests for the `Scaffold_VCCW_Functions::rcopy()`.
	 *
	 * @test
	 * @since 0.1.0
	 */
	public function rcopy()
	{
		$src = self::mockdir();
		$this->assertTrue( is_dir( $src ) ); // $dir should exists.
		$dest = Scaffold_VCCW_Functions::tempdir();
		$this->assertTrue( is_dir( $dest ) ); // $dir should exists.
		$this->assertTrue( self::md5sum( $src ) !== self::md5sum( $dest ) );
		// Copy directory recursively then check md5.
		Scaffold_VCCW_Functions::rcopy( $src, $dest );
		$this->assertTrue( self::md5sum( $src ) === self::md5sum( $dest ) );
		$dest = Scaffold_VCCW_Functions::tempdir();
		Scaffold_VCCW_Functions::rcopy( $src, $dest, array( "dir01/dir01-01.txt" ) );
		$this->assertFalse( is_file( $dest . '/dir01/dir01-01.txt' ) );
		$this->assertTrue( is_file( $dest . '/dir01/dir01-02.txt' ) );
		$this->assertTrue( is_file( $dest . '/dir02/dir02-01.txt' ) );
	}

	/**
	 * Create files and directories as mock for the test.
	 *
	 * @since  0.1.0
	 * @return string $dir Path to the temporary directory.
	 */
	private static function mockdir()
	{
		$dir = Scaffold_VCCW_Functions::tempdir();
		mkdir( $dir . "/dir01" );
		file_put_contents( $dir . "/dir01/dir01-01.txt", time() );
		file_put_contents( $dir . "/dir01/dir01-02.txt", time() );
		mkdir( $dir . "/dir01/dir01-01" );
		file_put_contents( $dir . "/dir01/dir01-01/dir01-01-01.txt", time() );
		mkdir( $dir . "/dir02" );
		file_put_contents( $dir . "/dir02/dir02-01.txt", time() );
		return $dir;
	}

	/**
	 * Create a md5 hash from directory.
	 *
	 * @since  0.1.0
	 * @param  strint $dir Path to the directory.
	 * @return string      Hash of the files in the derectory.
	 */
	private static function md5sum( $dir )
	{
		if ( ! is_dir( $dir ) ) {
			return false;
		}
		$iterator = Scaffold_VCCW_Functions::get_files( $dir );
		$md5 = array();
		foreach ( $iterator as $item ) {
			if ( ! $item->isDir() ) {
				$md5[] = md5_file( $item );
			}
		}
		return md5( implode( '', $md5 ) );
	}
}
