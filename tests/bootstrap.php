<?php
define("OPSOU_DIR", dirname(dirname(__FILE__)));

$wp_root_dir = dirname(dirname(dirname(OPSOU_DIR)));
require_once( $wp_root_dir . '/wp-load.php' );

$data = OPSOU_DIR . '/tests/test.json';

require_once( OPSOU_DIR . '/vendor/autoload.php' );

include_once( OPSOU_DIR . '/core/Loader.php' );