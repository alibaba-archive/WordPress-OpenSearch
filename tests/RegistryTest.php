<?php

use \OpenSearch\Core\Registry;

class RegistryTest extends PHPUnit_Framework_TestCase {
	public function testInstance() {
        $registry = Registry::instance();
        $this->assertInstanceOf('Registry', get_class($registry));
    }

    public function testGetOptions() {
        $options = Registry::instance()->getOptions();
        $this->assertArrayHasKey('accessKeyId', $options);
        $this->assertArrayHasKey('accessKeySecret', $options);
        $this->assertArrayHasKey('accessHost', $options);
        $this->assertArrayHasKey('accessKeyType', $options);
        $this->assertArrayHasKey('appName', $options);
        $this->assertArrayHasKey('accessValid', $options);
        $this->assertArrayHasKey('accessSearchValid', $options);
        $this->assertArrayHasKey('accessIndexValid', $options);
        $this->assertArrayHasKey('accessTemplateValid', $options);
    }

    public function testSetOptions() {
        $defaultOptions = array(
            'accessKeyId'         => 'assess key id',
            'accessKeySecret'     => 'assess secret',
            'accessHost'          => 'http://opensearch-cn-qingdao.aliyuncs.com',
            'accessKeyType'       => 'aliyun',
            'appName'             => 'test',
            'accessValid'         => 1,
            'accessSearchValid'   => 1,
            'accessIndexValid'    => 1,
            'accessTemplateValid' => 1,
        );
        Registry::instance()->setOptions($defaultOptions);
        $options = Registry::instance()->getOptions();
        $this->assertEquals('assess key id', $options['accessKeyId']);
        $this->assertEquals('assess secret', $options['accessKeySecret']);
        $this->assertEquals('http://opensearch-cn-qingdao.aliyuncs.com', $options['accessHost']);
        $this->assertEquals('aliyun', $options['accessKeyType']);
        $this->assertEquals('test', $options['appName']);
        $this->assertEquals(1, $options['accessValid']);
        $this->assertEquals(1, $options['accessSearchValid']);
        $this->assertEquals(1, $options['accessIndexValid']);
        $this->assertEquals(1, $options['accessTemplateValid']);
    }

    /**
     * @depends testSetOptions
     */
    public function testGet() {
        $accessKeyId = Registry::instance()->get('accessKeyId');
        $this->assertEquals('assess key id', $accessKeyId);
    }

    public function testUpdateOption() {}

    public function testSet() {
        Registry::instance()->set('appName', 'wp');
        $appName = Registry::instance()->get('appName');
        $this->assertEquals('wp', $appName);
    }

    /**
     * @depends testSetPluginUrl
     */
    public function testGetPluginUrl() {
        $pluginUrl = Registry::instance()->getPluginUrl();
        $this->assertEquals('http://test-url/', $pluginUrl);
    }

    /**
     * @depends testSetPluginDir
     */
    public function testGetPluginDir() {
        $pluginDir = Registry::instance()->getPluginDir();
        $this->assertEquals('/test-dir/', $pluginDir);
    }

    /**
     * @depends testSetPluginVersion
     */
    public function testGetPluginVersion() {
        $pluginVersion = Registry::instance()->getPluginVersion();
        $this->assertEquals('1.0', $pluginVersion);
    }

    /**
     * @depends testSetPluginName
     */
    public function testGetPluginName() {
        $pluginName = Registry::instance()->getPluginName();
        $this->assertEquals('open-search', $pluginName);
    }

    /**
     * @depends testSetPluginDirectoryName
     */
    public function testGetPluginDirectoryName() {
        $pluginDirecotryName = Registry::instance()->getPluginDirectoryName();
        $this->assertEquals('test-plugin-name', $pluginDirecotryName);
    }

    /**
     * @depends testSetPluginKey
     */
    public function testGetPluginKey() {
        $pluginKey = Registry::instance()->getPluginKey();
        $this->assertEquals('test-key', $pluginKey);
    }

    /**
     * @depends testSetPluginShortName
     */
    public function testGetPluginShortName() {
        $pluginShortName = Registry::instance()->getPluginShortName();
        $this->assertEquals('test-short-name', $pluginShortName);
    }

    /**
     * @depends testSetServerTemplate
     */
    public function testGetServerTemplate() {
        $serverTemplate = Registry::instance()->getServerTemplate();
        $this->assertEquals('test-server-template', $serverTemplate);
    }

    public function testSetPluginUrl() {
        Registry::instance()->setPluginUrl('http://test-url/');
    }

    public function testSetPluginDir() {
        Registry::instance()->setPluginDir('/test-dir');
    }

    public function testSetPluginVersion() {
        Registry::instance()->setPluginVersion('1.0');
    }

    public function testSetPluginName() {
        Registry::instance()->setPluginName('open-name');
    }

    public function testSetPluginDirectoryName() {
        Registry::instance()->setPluginDirectoryName('test-plugin-name');
    }

    public function testSetPluginKey() {
        Registry::instance()->setPluginKey('test-key');
    }

    public function testSetPluginShortName() {
        Registry::instance()->setPluginShortName('test-short-name');
    }

    public function testSetServerTemplate() {
        Registry::instance()->setServerTemplate('test-server-template');
    }

    public function testReset() {
        Registry::instance()->reset();
        $options = Registry::instance()->getOptions();
        $this->assertEmpty($options);
    }

    /**
     * @depends testSetOptions
     */
    public function testSaveOptions() {}

    public function testGetSettingKey() {
        $settingKey = Registry::instance()->getSettingKey();
        $this->assertNotEmpty($settingKey);
    }

    /**
     * @depends testSetOptions
     */
    public function testInit() {}

    /**
     * @depends testSetOptions
     */
    public function testGetValue() {}

    /**
     * @depends testGetValue
     */
    public function testGetAccessKeyId() {}

    /**
     * @depends testGetValue
     */
    public function testGetAccessKeySecret() {}

    /**
     * @depends testGetValue
     */
    public function testGetAccessHost() {}

    /**
     * @depends testGetValue
     */
    public function testGetAccessKeyType() {}

    /**
     * @depends testGetValue
     */
    public function testGetAppName() {}

    /**
     * @depends testGetValue
     */
    public function testIsValidAccess() {}

    /**
     * @depends testGetValue
     */
    public function testIsValidAccessIndex() {}

    /**
     * @depends testGetValue
     */
    public function testIsValidAccessTemplate() {}

    /**
     * @depends testIsValidAccess
     * @depends testIsValidAccessIndex
     * @depends testGetAppName
     */
    public function testIsEnabled() {}
}