<?php

use \OpenSearch\Core;
use \OpenSearch\SDK;

class SearchTest extends PHPUnit_Framework_TestCase {
	public function testInstance() {
        $client = new SDK\CloudsearchClient(
            Core\Registry::instance()->getAccessKeyId(),
            Core\Registry::instance()->getAccessKeySecret(),
            array("host"=>Registry::instance()->getAccessHost(),"debug"=>true),
            Core\Registry::instance()->getAccessKeyType()
        );
        $this->assertInstanceOf('CloudsearchClient', get_class($client));
        $appName = Core\Registry::instance()->getAppName();
        $this->assertEquals('wp', $appName);
        $search = new SDK\CloudsearchSearch($client);
        $this->assertInstanceOf('CloudsearchSearch', $search);
    }

    public function testThePosts() {
        $search = new Core\Search();
        $query = 'aliyun';
        $result = $search->frontEndSearch($query);
        $$exceptedJson = json_encode($result['posts']);
        $posts = $search->thePosts($result['posts']);
        $actualJson = json_encode($posts);
        $this->assertJsonStringEqualsJsonString($exceptedJson,$actualJson);
    }

    public function testEnqueueScripts() {
        $csspath = Core\Registry::instance()->getPluginDir() . "front/assets/styles/opensearch.css";
        $this->assertFileExists($csspath);
    }

    public function testFrontEndSearch() {
        $search = new Core\Search();
        $query = 'aliyun';
        $result = $search->frontEndSearch($query);
        $this->assertNotEmpty($result);
    }

    public function testFilterTheTags() {
        $search = new Core\Search();
        $postID = 1;
        $terms = get_the_terms($postID, 'post_tag');
        $result = $search->filterTheTags($terms);
        $this->assertNotEmpty($result);
    }

    public function testFilterTheCategories() {
        $search = new Core\Search();
        $postID = 1;
        $terms = get_the_terms($postID, 'category');
        $result = $search->filterTheTags($terms);
        $this->assertNotEmpty($result);
    }
}