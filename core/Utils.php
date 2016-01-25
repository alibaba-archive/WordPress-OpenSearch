<?php

namespace OpenSearch\Core;
use \OpenSearch\SDK;

if( ! defined( 'ALG_INVALID_SETTINGS' ) ){  define('ALG_INVALID_SETTINGS', 01); }
if( ! defined( 'ALG_INVALID_INDEX' ) ){  define('ALG_INVALID_INDEX', 02); }
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class Utils {
	private static $client;
	private static $doc;
	private static $index;
	private static $search;
	private static $suggest;

	static function client( $accessKeyId, $accessKeySecret, $accessHost, $accessKeyType = 'aliyun' ) {
		if (!isset(self::$client)) {
			self::$client = new SDK\CloudsearchClient( $accessKeyId, $accessKeySecret, array("host"=>$accessHost, "debug"=>true), $accessKeyType );
		}
		return self::$client;
	}

	public static function readyForClient() {
		$registry = Registry::instance();
		return ( $registry->getAccessKeyId() && $registry->getAccessKeySecret() && $registry->getAccessHost() );
	}

	public static function readyToIndex() {
		$registry = Registry::instance();
		return ( $registry->isValidAccess() && $registry->isValidAccessIndex() );
	}

	public static function readyForTemplate() {
		return Registry::instance()->isValidAccessTemplate();
	}

	/**
	 * @param CloudsearchClient $client
	 * @return boolean|\WP_Error TRUE if all is OK and WP_Error if there was an error
	 */
	public static function validAccess( $client = null ) {
		if (empty($client)) {
			$client = self::$client;
		}
		$valid = false;
		$error = null;
		try {
			$debugInfo = $client->getRequest( );
			if( !$debugInfo ) {
				$valid = true;
			}else{
				$error =  new \WP_Error( ALG_INVALID_SETTINGS, __('Invalid Credentials', 'opSou') );
			}
		} catch ( \Exception $exc ) {
			$valid = false;
			$error =  new \WP_Error( ALG_INVALID_SETTINGS, $exc->getMessage() );
		}

		if( !$valid ){
			return $error;
		}
		return $valid;
	}

	/**
	 * [validAccessIndex description]
	 * @param  String 			 $appName
	 * @param  CloudsearchClient $client
	 * @return boolean|\WP_Error TRUE if all is OK and WP_Error if there was an error
	 */
	public static function validAccessIndex( $appName, $client = null ) {
		if (empty($client)) {
			$client = self::$client;
		}
		$index = self::index( $appName, $client );
		$valid = false;
		$error = null;
		try {
			$status = $index->status( );
			if( $status ){
				$valid = true;
			}else{
				$error =  new \WP_Error( ALG_INVALID_SETTINGS, __('Invalid Credentials', 'opSou') );
			}
		} catch ( \Exception $exc ) {
			$valid = false;
			$error =  new \WP_Error( ALG_INVALID_SETTINGS, $exc->getMessage() );
		}

		if( !$valid ){
			return $error;
		}
		return $valid;
	}

	/**
	 * OpenSearch application instance
	 * @param  string 			 $appName
	 * @param  CloudsearchClient $client
	 * @return CloudsearchIndex
	 */
	public static function index($appName, $client) {
		if ( empty( self::$index ) && !empty( $appName ) && !empty( $client ) ) {
			self::$index = new SDK\CloudsearchIndex($appName, $client);
		} else {
			throw new \Exception( 'CloudsearchClient没有实例化或者应用名称为空' );
		}
		return self::$index;
	}
}
