<?php

use \OpenSearch\Core;
use \OpenSearch\SDK;

class UtilsTest extends PHPUnit_Framework_TestCase {
	public function testClient() {
        $client = Core\Utils::client(
            Core\Registry::instance()->getAccessKeyId(),
            Core\Registry::instance()->getAccessKeySecret(),
            Core\Registry::instance()->getAccessHost());
        $this->assertInstanceOf('CloudsearchClient', get_class($client));
    }

    public function testReadyForClient() {
        $readyForClient = Core\Utils::readyForClient();
        $this->assertEquals(true, $readyForClient);
    }

    public function testReadyToIndex() {
        $readyToIndex = Core\Utils::readyToIndex();
        $this->assertEquals(true, $readyToIndex);
    }

    public function testReadyForTemplate() {
        $readyForTemplate = Core\Utils::readyForTemplate();
        $this->assertEquals(true, $readyForTemplate);
    }

    public function testValidAccess() {
        $client = Core\Utils::client(
            Core\Registry::instance()->getAccessKeyId(),
            Core\Registry::instance()->getAccessKeySecret(),
            Core\Registry::instance()->getAccessHost());
        $validAccess = Core\Utils::validAccess($client);
        $this->assertEquals(true, $validAccess);
    }

    public function testValidAccessIndex() {
        $client = Core\Utils::client(
            Core\Registry::instance()->getAccessKeyId(),
            Core\Registry::instance()->getAccessKeySecret(),
            Core\Registry::instance()->getAccessHost());
        $validAccessIndex = Core\Utils::validAccessIndex('wp', $client);
        $this->assertEquals(true, $validAccessIndex);
    }

    public function testIndex() {
        $client = Core\Utils::client(
            Core\Registry::instance()->getAccessKeyId(),
            Core\Registry::instance()->getAccessKeySecret(),
            Core\Registry::instance()->getAccessHost());
        $index = new SDK\CloudsearchIndex('wp', $client);
        $this->assertInstanceOf('CloudsearchIndex', get_class($index));
    }
}