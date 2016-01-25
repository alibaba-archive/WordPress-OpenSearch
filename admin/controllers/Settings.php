<?php

namespace OpenSearch\Admin\Controllers;
use \OpenSearch\Core as Core;
use \OpenSearch\Core\Registry;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Settings {

	private static $instance;
	private static $client;
	private static $doc;

	public $postsPerPageToIndex = 20;
	public $postsPerPageToRemove = 500;

	const settingsField   = 'opSouSettings';
	const updateAction    = 'opSouUpdateSettings';
	const ajaxIndexAction = 'opSouAjaxIndex';

	/**
	 * Constructor. Initialize all the hooks
	 */
	public function __construct() {
		add_action( 'admin_init', array($this, 'adminInit'), 10, 1);
		add_action( 'admin_menu', array($this, 'adminMenu'), 10, 1);
		add_action( 'admin_notices', array($this, 'adminNotices'), 10, 1);
		add_action( 'wp_ajax_' . Settings::ajaxIndexAction , array($this, 'ajaxIndexDoc' ), 10, 1);
	}

	/**
	 *
	 * @return \OpenSearch\Core\Registry
	 */
	static function instance() {
		if ( Core\Utils::readyForClient() && Core\Utils::readyToIndex() && Core\Utils::readyForTemplate() && empty(self::$doc)) {
			self::$doc = new Core\Doc();
		}
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( );
		}
		return self::$instance;
	}

	/**
	 * Method executed on WP admin init hook
	 */
	public function adminInit(){
		if ( !empty($_GET['opSou_dlTemplate']) && $_POST[Settings::settingsField]['accessTemplateValid'] ) {
			$this->dlTemplate();
		} elseif ( !empty( $_POST ) && count( $_POST ) > 0 && isset( $_POST['opSou_action'] ) &&  $_POST['opSou_action'] == self::updateAction  ) {
			$this->updateOptions();
		}
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueueScripts' ) );
	}

	/**
	 * Enqueue the admin scripts
	 * @param string $hook Page where it was called
	 * @return void
	 */
	public function enqueueScripts($hook) {
		if( $hook != 'toplevel_page_opSou_general_settings' ){
			return;
		}
		$jspath  = Registry::instance()->getPluginUrl() . "admin/assets/scripts/open-search-functions.js";
		$csspath = Registry::instance()->getPluginUrl() . "admin/assets/styles/settings.css";

		wp_enqueue_script( 'jquery-ui-progressbar' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'openSearchSettings', $jspath, array( 'jquery', 'jquery-ui-progressbar' ), Registry::instance()->getPluginVersion() );
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'openSearchSettings', $csspath, array(), Registry::instance()->getPluginVersion() );
		$adminUrl = admin_url();
		$homeUrl = home_url(null, is_ssl() ? 'https' : 'http');

		$postTypesToIndex  = Core\FieldsHelper::getPostTypesToIndex();
		$postLabels        = Core\FieldsHelper::getPostTypesLabels( $postTypesToIndex );

		$args = array(
					'siteUrl'                => $homeUrl,
					'ajaxUrl'                => sprintf('%s%s',$adminUrl, "admin-ajax.php"),
					'ajaxIndexAction'        => Settings::ajaxIndexAction,
					'ajaxIndexNonce'         => wp_create_nonce( Settings::ajaxIndexAction ),
					'postsPerPage'           => $this->postsPerPageToIndex,
					'postsPerPageToRemove'   => $this->postsPerPageToRemove,
					'postTypesToIndex'       => array_keys( $postTypesToIndex ),
					'totalPublishedPosts'    => self::getTotalPublishedPosts(),
					'labels'                 => array(
						'indexationError'  => 'There was an error trying to run indexation, please contact to the author.',
						'starting'         => 'Starting...',
						'indexing'         => 'Indexing ',
						'complete'         => 'Completed!',
						'running'          => "We're indexing your content and sending it to OpenSearch. Hang tight - it could take several minutes!",
						'removing'         => 'Removing unpublish posts from the index',
						'postsLabels'      => $postLabels,
						'indexNameChanged' => 'Index name was changed. Please save changes to index content.'
					),
				);
		// pre($args);
		wp_localize_script( 'openSearchSettings', 'opSouVars', $args, Registry::instance()->getPluginVersion() );
	}

	/**
	 * Show OpenSearch item in the sidebar menu
	 */
	public static function adminMenu(){
		if (self::$instance) {
			$registry = Registry::instance();
			$iconUrl  = $registry->getPluginUrl()."admin/assets/images/icon.png";
			add_menu_page(__( 'Open Search Settings', 'opSou' ), __( 'Open Search', 'opSou' ), 'manage_options', $registry->getPluginShortName().'_general_settings', array(self::$instance, 'showForm'), $iconUrl );
		} else {
			throw new \Exception( '没有实例化OpenSearch\Admin\Controllers\Settings' );
		}
	}

	/**
	 * Load the settings form when settings page is called
	 */
	public static function showForm() {
		include Registry::instance()->getPluginDir() . "admin/views/settings.php";
	}

	/**
	 * Set the admin notices if they are neccesary
	 */
	public static function adminNotices() {
		if( !empty($_REQUEST['opSouMessage']) && $_REQUEST['opSouMessage'] === 'settingsUpdated' ):
	?>
		<div class="updated">
			<p><?php _e( 'Settings were updated.', Registry::instance()->getPluginShortName() ); ?></p>
		</div>
	<?php
		elseif( !empty($_REQUEST['opSouMessage']) && $_REQUEST['opSouMessage'] === 'settingsNotUpdated' ):
	?>
		<div class="error">
			<p><?php _e( 'Settings were NOT updated, please try again.', Registry::instance()->getPluginShortName() ); ?></p>
		</div>
	<?php
		endif;
		if(Registry::instance()->isValidAccess() && !Registry::instance()->isValidAccessIndex()):
	?>
		<div class="error">
			<p><?php _e( 'Please go to the Open Search section and set a valid "App Name" to enable the Open Search search in your site. <a href="admin.php?page=opSou_general_settings">View Details</a>', Registry::instance()->getPluginShortName() ); ?></p>
		</div>
	<?php
		endif;
		if(!Registry::instance()->getAccessKeyId()
			|| !Registry::instance()->getAccessKeySecret()
			|| !Registry::instance()->getAccessHost()
			|| !Registry::instance()->isValidAccess()):
	?>
		<div class="error">
			<p><?php _e( 'Please go to the Open Search section to enable the Open Search module to work. <a href="admin.php?page=opSou_general_settings">View Details</a>', Registry::instance()->getPluginShortName() ); ?></p>
		</div>
	<?php
		endif;
	}

	/**
	 * Run the indexation called from ajax action
	 */
	public function ajaxIndexDoc() {
		check_ajax_referer( Settings::ajaxIndexAction, '_ajax_nonce_index');

		if( !empty( $_POST['runIndex'] ) )
		{
			$errorMessage = '';
			$error = FALSE;
			$totalIndexed = 0;
			if( Registry::instance()->isValidAccess() ){
				$indexPostType = !empty( $_POST['indexPostType'] ) ? sanitize_text_field( $_POST['indexPostType'] ) : 0;
				$offset = !empty( $_POST['queryOffset'] ) ? (int)$_POST['queryOffset'] : 0;
				try {
					$tableName = 'main';
					$docsToUpload = static::$doc->getDoces( $tableName, $this->postsPerPageToIndex, $offset );
					if (!empty($docsToUpload)) {
						$result = static::$doc->add( $docsToUpload, $tableName );
					}
				} catch ( Exception $exc ) {
					$errorMessage = $exc->getTraceAsString();
					$error = TRUE;
				}

			}else{
				$errorMessage = __( 'Looks like your credentials are not valids', Registry::instance()->getPluginShortName() );
				$error = TRUE;
			}

			$result['totalIndexed'] = !empty($result['totalIndexed']) ? $result['totalIndexed'] : $totalIndexed;
			$result['error'] = $error;
			$result['opSouErrorMessage'] = $errorMessage;

			// response output
			header( "Content-Type: application/json" );
			echo json_encode($result);
			exit;
		}
	}

	public static function getTotalPublishedPosts( ) {
		$postsTypesToIndex = array_keys( Core\FieldsHelper::getPostTypesToIndex() );

		$totals = array();
		foreach( $postsTypesToIndex as $type ) {
			$countByType = wp_count_posts( $type );
			$totals[$type] = 0;
			foreach( $countByType as $postStatus => $count) {
				if( 'publish' === $postStatus ) {
					$totals[$type] += $count;
				}
			}
		}
		return $totals;
	}

	/**
	 * Download the template for upload to OpenSearch
	 * @return [type] [description]
	 */
	public function dlTemplate() {
		$template_file = Registry::instance()->getServerTemplate();
		if (file_exists($template_file)) {
			@header("Content-type:text/html;charset=utf-8");
			$file_name = basename($template_file);
			$file_name = iconv("utf-8","gb2312",$file_name);

			@header("Content-type: application/octet-stream");
			// header("Accept-Ranges: bytes");
			// header("Accept-Length:".$file_size);
			@header("Content-Disposition: attachment; filename=".$file_name);
			@header("Content-Length: ". filesize($template_file));
			@readfile($template_file);
		}

		$options = Registry::instance()->getOptions();
		$options['accessTemplateValid'] = $_POST[Settings::settingsField]['accessTemplateValid'];
		Registry::instance()->saveOptions( $options );

		// $referer = add_query_arg( array( 'opSouMessage' => 'downloadedTemplate' ), wp_get_referer() );
		// wp_safe_redirect( $referer );
		// die('Failed redirect saving settings');
	}

	/**
	 * Save settings modified by the user
	 */
	public function updateOptions( ) {
		if( ! current_user_can( 'manage_options' )
				|| ! isset($_REQUEST['_wpnonce'])
				|| ! wp_verify_nonce( $_REQUEST['_wpnonce'], Settings::updateAction ) ){
			return;
		}

		// Get all the settings
		$options = Registry::instance()->getOptions();
		foreach ( $options as $option => $value ) {
			if ( !isset( $_POST[self::settingsField] ) || !isset( $_POST[self::settingsField][ $option ] ) ){
				continue;
			}

			if( is_array( $_POST[self::settingsField][ $option ] ) ){
				$options[$option] = array_map( 'sanitize_text_field', $_POST[self::settingsField][ $option ] );
			}else{
				$options[$option] = sanitize_text_field( $_POST[self::settingsField][ $option ] );
			}
		}

		if( !empty( $options['accessKeyId'] ) && !empty( $options['accessKeySecret'] ) && !empty( $options['accessHost'] ) ) {
			static::$client = Core\Utils::client( $options['accessKeyId'], $options['accessKeySecret'], $options['accessHost'] );
		}

		// If there is a CloudsearchClient instance validate it
		$options['accessValid'] = 0;
		if( !empty( static::$client )) {
			$validAccess = Core\Utils::validAccess( static::$client );
			if( ! is_wp_error( $validAccess ) ){
				$options['accessValid'] = 1;
			}else{
				$options['accessValid'] = 0;
			}
		}

		// If there is an appName and a cloudsearchClient instance validate them
		$options['accessIndexValid'] = 0;
		if( !empty( $options['appName'] ) && !empty( static::$client ) ){
			$validIndex = Core\Utils::validAccessIndex( $options['appName'], static::$client );
			if( ! is_wp_error( $validIndex ) ){
				$options['accessIndexValid'] = 1;
			}else{
				$options['accessIndexValid'] = 0;
			}
		}

		// pre($options);
		// exit;

		Registry::instance()->saveOptions( $options );

		$referer = add_query_arg( array( 'opSouMessage' => 'settingsUpdated' ), wp_get_referer() );
		//Redirect back to the settings page that was submitted
		wp_safe_redirect( $referer );
		die('Failed redirect saving settings');
	}

}
