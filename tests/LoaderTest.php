<?php

use OpenSearch\Core;
use OpenSearch\Admin\Controllers;

class LoaderTest extends PHPUnit_Framework_TestCase {
	public function testLoad() {

	}

	public function testAddExtension() {

	}

	public function testLoadFile() {
        $registry = Core\Registry::instance();
        $settings = Controllers\Settings::instance();
        $indexer = Controllers\Indexer::instance();
        $doc = new Core\Doc();
        $search = new Core\Search();
        $this->assertTrue(class_exists('Registry'));
        $this->assertTrue(class_exists('Settings'));
        $this->assertTrue(class_exists('Indexer'));
        $this->assertTrue(class_exists('Doc'));
        $this->assertTrue(class_exists('Search'));
        $this->assertTrue(class_exists('CloudsearchClient'));
        $this->assertTrue(class_exists('CloudsearchDoc'));
        $this->assertTrue(class_exists('CloudsearchSearch'));
	}
}