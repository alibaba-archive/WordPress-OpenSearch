<?php

namespace OpenSearch\Core;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Registry{

	private static $instance;
	private $pluginUrl;
	private $pluginDir;
	private $pluginVersion;
	private $pluginName;
	private $pluginDirectoryName = "";
	private $pluginKey = false;
	private $pluginShortName = false;
	private $options = array( );
	private $serverTemplate;

	/**
	 *
	 * @var WordpressRegistry 
	 */
	private $settingKey = 'open-search';

	/**
	 * 
	 * @return \OpenSearch\Core\Registry
	 */
	static function instance() {
		if ( ! isset( self::$instance ) ) {
			$defaultOptions = array(
				'accessKeyId'         => '',
				'accessKeySecret'     => '',
				'accessHost'          => '',
				'accessKeyType'       => 'aliyun',
				'appName'             => '',
				'accessValid'         => 0,
				'accessSearchValid'   => 0,
				'accessIndexValid'    => 0,
				'accessTemplateValid' => 0,
			);

			self::$instance = new self( );
			self::$instance->setOptions($defaultOptions);
		}
		return self::$instance;
	}

	/**
	 * Return the values
	 * @return \Maven\Settings\Option[]
	 */
	public function getOptions() {
		return $this->options;
	}

	public function getKeys() {
		return array_keys( $this->options );
	}

	/**
	 * This method must be used JUST to initialize the default settings
	 * @param Option[] $values
	 * @return Option[] 
	 */
	protected function setOptions( $options ) {

		foreach ( $options as $optionKey => $option ) {
			$this->options[ $optionKey ] = $option;
		}
	}

	/**
	 * Return a setting
	 * @param string $key
	 * @return \Maven\Settings\Option 
	 */
	public function get( $key ) {

		if ( isset( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}

		return null;
	}

	/**
	 * Set a setting
	 * @param string $key
	 * @param string $value 
	 */
	public function updateOption( $key, $value ) {

		if ( isset( $this->options[ $key ] ) ) {
			$this->options[ $key ] = $value;
			$this->saveOptions( $this->options );
		}
	}

	/**
	 * Set a setting
	 * @param string $key
	 * @param string $value 
	 */
	public function set( $key, $value ) {

		if ( isset( $this->options[ $key ] ) ) {
			$this->options[ $key ] = $value;
		}
	}

	public function getPluginUrl () {
		return $this->pluginUrl;
	}

	public function getPluginDir () {
		return $this->pluginDir;
	}

	public function getPluginVersion () {
		return $this->pluginVersion;
	}

	public function getPluginName () {
		return $this->pluginName;
	}

	public function getPluginDirectoryName () {
		return $this->pluginDirectoryName;
	}

	public function getPluginKey () {
		return $this->pluginKey;
	}

	public function getPluginShortName () {
		return $this->pluginShortName;
	}

	public function getServerTemplate () {
		return $this->serverTemplate;
	}

	public function setPluginUrl ( $pluginUrl ) {
		$this->pluginUrl = $pluginUrl;
	}

	public function setPluginDir ( $pluginDir ) {
		$this->pluginDir = $pluginDir;
	}

	public function setPluginVersion ( $pluginVersion ) {
		$this->pluginVersion = $pluginVersion;
	}

	public function setPluginName ( $pluginName ) {
		$this->pluginName = $pluginName;
	}

	public function setPluginDirectoryName ( $pluginDirectoryName ) {
		$this->pluginDirectoryName = $pluginDirectoryName;
	}

	public function setPluginKey ( $pluginKey ) {
		$this->pluginKey = $pluginKey;
	}

	public function setPluginShortName ( $pluginShortName ) {
		$this->pluginShortName = $pluginShortName;
	}

	public function setServerTemplate ( $serverTemplate ) {
		$this->serverTemplate = $serverTemplate;
	}

	public function reset(){
		delete_option( $this->getSettingKey() );
	}

	/**
	 * 
	 * @param \Maven\Settings\Option[] $options
	 */
	public function saveOptions( $options ){
		if( get_option( $this->getSettingKey() ) !== FALSE ){
			//Save the options in the WP table
			update_option( $this->getSettingKey(), $options );
		}else{
			//Save the options in the WP table
			add_option( $this->getSettingKey(), $options, '', 'no' );
		}
		$this->setOptions( $options );
	}

	private function getSettingKey(){
		return $this->settingKey;
	}

	public function init() {
		// Get the options from the db
		$existingsOptions = get_option( $this->getSettingKey() );
		// pre($existingsOptions);
		// If options exists we need to merge them with the default ones
		$options = wp_parse_args( $existingsOptions, $this->getOptions() );
		$this->setOptions( $options );
	}

	/**
	 * Return a setting
	 * @param string $key
	 * @return null
	 */
	public function getValue( $key ) {
		if ( isset( $this->options[ $key ] ) ) {
			return $this->options[ $key ];
		}
		return null;
	}

	public function getAccessKeyId(){
		return $this->getValue('accessKeyId');
	}

	public function getAccessKeySecret(){
		return $this->getValue('accessKeySecret');
	}

	public function getAccessHost(){
		return $this->getValue('accessHost');
	}

	public function getAccessKeyType(){
		return $this->getValue('accessKeyType');
	}

	public function getAppName(){
		return $this->getValue('appName');
	}

	public function isValidAccess(){
		return (bool)$this->getValue('accessValid');
	}

	public function isValidAccessIndex() {
		return (bool)$this->getValue('accessIndexValid');
	}

	public function isValidAccessTemplate() {
		return (bool)$this->getValue('accessTemplateValid');
	}

	public function isEnabled() {
		return (bool) ( $this->isValidAccess() && $this->isValidAccessIndex() && $this->getAppName() );
	}
}