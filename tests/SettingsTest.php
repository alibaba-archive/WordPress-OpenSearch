<?php

use \OpenSearch\Core;
use \OpenSearch\Admin\Controllers\Settings;

class SettingsTest extends PHPUnit_Framework_TestCase {
	public function testInstance() {
        $doc = new Core\Doc();
        $this->assertInstanceOf('Doc', $doc);

        $settings = new Settings();
        $this->assertInstanceOf('Settings', $settings);
    }

    public function testAdminInit() {}

    public function testEnqueueScripts() {
        $jspath  = Core\Registry::instance()->getPluginDir() . "admin/assets/scripts/open-search-functions.js";
        $this->assertFileExists($jspath);
        $csspath = Core\Registry::instance()->getPluginDir() . "admin/assets/styles/settings.css";
        $this->assertFileExists($csspath);
    }

    public function testAdminMenu() {
        $iconUrl  = $registry->getPluginDir()."admin/assets/images/icon.png";
        $this->assertFileExists($iconUrl);
    }

    public function testShowForm() {}

    public function testAdminNotices() {}

    public function testAjaxIndexDoc() {}

    public function testGetTotalPublishedPosts() {
        $result = Settings::getTotalPublishedPosts();
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
            $this->assertEquals($result[$type], $totals[$type]);
        }
    }

    public function testDlTemplate() {
        $template_file = Registry::instance()->getServerTemplate();
        $this->assertFileExists($template_file);
    }

    public function testUpdateOptions() {}
}