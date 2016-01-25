<?php
/*
Plugin Name: open-search
Description: Sync the Wordpress Posts，Pages, Categories and Tags to OpenSearch
Version: 1.0
Author: zzqer
Author URI: http://www.zzqer.com
*/
namespace OpenSearch;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

//These are the only require_once needed. Then you should use the Loader class
require_once plugin_dir_path( __FILE__ ) . '/core/Loader.php';
Core\Loader::load( plugin_dir_path( __FILE__ ), array( 'core/Registry' ) );

$registry = Core\Registry::instance();
$registry->setPluginDir( plugin_dir_path( __FILE__ ) );
$registry->setPluginUrl( defined( 'DEV_ENV' ) && DEV_ENV ? WP_PLUGIN_URL . "/{basename(plugin_dir_path( __FILE__ ))}/" : plugin_dir_url( __FILE__ ));
$registry->setPluginVersion( "0.1" );
$registry->setPluginName( 'Open Search' );
$registry->setPluginShortName( 'opSou' );
$registry->setServerTemplate(WP_PLUGIN_DIR.'/'.basename(plugin_dir_path( __FILE__ )).'/config/config.json');
$registry->init();

if( is_admin() ){
	$settings = Admin\Controllers\Settings::instance();
	$adminIndexer = Admin\Controllers\Indexer::instance();
}else{
	if( $registry->isEnabled() ){
		try {
			$searcher = new Core\Search();
		} catch ( Exception $exc ) {
			$error = $exc->getTraceAsString();
		}
	}
}