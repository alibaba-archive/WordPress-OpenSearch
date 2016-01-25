<?php

namespace OpenSearch\Core;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Loader {
	/**
	 * @param string $plugin_dir
	 * @param string/array $files
	 * @param array $data
	 * @param boolean $requireOnce
	 * @param boolean $return
	 */
	public static function load ( $plugin_dir, $files, $data = false, $requireOnce = true, $return = false ) {

		if ( is_array( $files ) ) {
			foreach ( $files as $file ) {
				self::loadFile( $plugin_dir . $file, $data, $requireOnce, $return );
			}
		} else if ( $files )
			return self::loadFile( $plugin_dir . $files, $data, $requireOnce, $return );
	}

	/**
	 * Add '.php' extendion
	 * @param [type] $file [description]
	 */
	private static function addExtension ( $file ) {

		$extension = "";

		if ( !pathinfo( $file, PATHINFO_EXTENSION ) )
			$extension = ".php";

		return $file . $extension;
	}

	/**
	 * Load class
	 * @param  String  $file
	 * @param  array   $data
	 * @param  boolean $requireOnce
	 * @param  boolean $return
	 * @return void
	 */
	private static function loadFile ( $file, $data = false, $requireOnce = true, $return = false ) {
		$file = self::addExtension( $file );

		if ( $data )
			extract( $data );

		if ( file_exists( $file ) ) {
			if ( $requireOnce ) {
				if ( $return ) {
					ob_start();
					require_once $file;
					return ob_get_clean();
				} else {

					require_once $file;
				}
			} else {
				if ( $return ) {

					ob_start();
					require $file;
					return ob_get_clean();
				} else
					require $file;
			}
		} else
			die( $file );
	}
}

//Autoload class
spl_autoload_register( function($className) {
	if (stripos($className, 'OpenSearch') === false) {
		return;
	}
	//Get the class name
	$classOnlyName = substr( $className, strrpos( $className, "\\" ) + 1 );

	//Remove the class name from the full path
	$start = stripos( $className, "\\" ) + 1;
	$end = strripos( $className, "\\" ) + 1;
	$len = $end - $start;
	$className = substr( $className, $start, $len);

	$classPath = strtolower( strtr( $className, '\\', DIRECTORY_SEPARATOR ) ) . $classOnlyName . ".php";

	$filePath = Registry::instance()->getPluginDir() . $classPath;

	if ( file_exists( $filePath ) ) {
		require_once( $filePath );
	}
	// else {
	// 	throw new \Exception( '没有找到'. $filePath .'此文件' );
	// }
} );

if (!function_exists('pre')) :
function pre($param = null) {
	echo '<pre>';
	print_r($param);
	echo '</pre>';
}
endif;
