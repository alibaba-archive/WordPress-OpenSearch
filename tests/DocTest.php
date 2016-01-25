<?php

use \OpenSearch\Core;
use \OpenSearch\Core\Registry as Registry;
use \OpenSearch\SDK;

class DocTest extends PHPUnit_Framework_TestCase {
	public function testInstance() {
		$client = new SDK\CloudsearchClient(
			Registry::instance()->getAccessKeyId(),
			Registry::instance()->getAccessKeySecret(),
			array("host"=>Registry::instance()->getAccessHost(),"debug"=>true),
			Registry::instance()->getAccessKeyType()
		);
		$this->assertInstanceOf('CloudsearchClient', get_class($client));
		$doc = new SDK\CloudsearchDoc(Registry::instance()->getAppName(), $client);
		$this->assertInstanceOf('CloudsearchDoc', get_class($doc));
	}

	public function testAdd() {
		$doc = new Core\Doc();
		$tableName = 'main';
		$docsArr = $doc->getDoces($tableName,0,10);
		$docToUpload = array();
		foreach($docsArr as $key => $value) {
			$this->assertArrayHasKey('ID', $value);
			$this->assertArrayHasKey('post_date', $value);
			$this->assertArrayHasKey('post_title', $value);
			$this->assertArrayHasKey('post_content', $value);
			$this->assertArrayHasKey('post_excerpt', $value);
			$this->assertArrayHasKey('post_name', $value);
			$this->assertArrayHasKey('post_modified', $value);
			$this->assertArrayHasKey('post_parent', $value);
			$this->assertArrayHasKey('menu_order', $value);
			$this->assertArrayHasKey('post_type', $value);
			$this->assertArrayHasKey('guid', $value);
			$this->assertArrayHasKey('featured_image', $value);
			$this->assertArrayHasKey('post_author', $value);
			$this->assertArrayHasKey('author', $value);
			$this->assertArrayHasKey('categories', $value);
			$this->assertArrayHasKey('tags', $value);

			$item           = array();
			$item['cmd']    = 'add';
			$item["fields"] = array(
				'object_id'      => $value['ID'],
				'date'           => $value['post_date'],
				'title'          => $value['post_title'],
				'content'        => $value['post_content'],
				'excerpt'        => $value['post_excerpt'],
				'slug'           => $value['post_name'],
				'modified'       => $value['post_modified'],
				'parent'         => $value['post_parent'],
				'menu_order'     => $value['menu_order'],
				'type'           => $value['post_type'],
				'permalink'      => $value['guid'],
				'featured_image' => $value['featured_image'],
				'author_id'      => $value['post_author'],
				'author'         => $value['author'],
				'categories'     => $value['categories'],
				'tags'           => $value['tags'],
			);
			array_push($docToUpload, $item);
		}
		$result = $doc->add($docToUpload, $tableName);
		$this->assertArrayHasKey('totalIndexed', $result);
		$this->assertArrayHasKey('jsonData', $result);
		$this->assertCount($result['totalIndexed'], $docToUpload);
		$json = json_encode($docToUpload);
		$expected = $this->getDocHandler()->add($json, $tableName);
		$this->assertEquals($expected,$result['jsonData']);
	}

	public function testRemove() {
		$doc = new Core\Doc();
		$tableName = 'main';
		$docsArr = $doc->getDoces($tableName,0,10);
		$docToUpload = array();
		foreach($docsArr as $key => $value) {
			$this->assertArrayHasKey('ID', $value);

			$item           = array();
			$item['cmd']    = 'delete';
			$item["fields"] = array(
				'object_id'      => $value['ID']
			);
			array_push($docToUpload, $item);
		}
		$result = $doc->remove($docToUpload, $tableName);
		$this->assertArrayHasKey('totalIndexed', $result);
		$this->assertArrayHasKey('jsonData', $result);
		$this->assertCount($result['totalIndexed'], $docToUpload);
		$json = json_encode($docToUpload);
		$expected = $this->getDocHandler()->remove($json, $tableName);
		$this->assertEquals($expected,$result['jsonData']);
	}

	public function testUpdate() {
		$doc = new Core\Doc();
		$tableName = 'main';
		$docsArr = $doc->getDoces($tableName,0,10);
		$docToUpload = array();
		foreach($docsArr as $key => $value) {
			$this->assertArrayHasKey('ID', $value);
			$this->assertArrayHasKey('post_date', $value);
			$this->assertArrayHasKey('post_title', $value);
			$this->assertArrayHasKey('post_content', $value);
			$this->assertArrayHasKey('post_excerpt', $value);
			$this->assertArrayHasKey('post_name', $value);
			$this->assertArrayHasKey('post_modified', $value);
			$this->assertArrayHasKey('post_parent', $value);
			$this->assertArrayHasKey('menu_order', $value);
			$this->assertArrayHasKey('post_type', $value);
			$this->assertArrayHasKey('guid', $value);
			$this->assertArrayHasKey('featured_image', $value);
			$this->assertArrayHasKey('post_author', $value);
			$this->assertArrayHasKey('author', $value);
			$this->assertArrayHasKey('categories', $value);
			$this->assertArrayHasKey('tags', $value);

			$item           = array();
			$item['cmd']    = 'update';
			$item["fields"] = array(
				'object_id'      => $value['ID'],
				'date'           => $value['post_date'],
				'title'          => $value['post_title'],
				'content'        => $value['post_content'],
				'excerpt'        => $value['post_excerpt'],
				'slug'           => $value['post_name'],
				'modified'       => $value['post_modified'],
				'parent'         => $value['post_parent'],
				'menu_order'     => $value['menu_order'],
				'type'           => $value['post_type'],
				'permalink'      => $value['guid'],
				'featured_image' => $value['featured_image'],
				'author_id'      => $value['post_author'],
				'author'         => $value['author'],
				'categories'     => $value['categories'],
				'tags'           => $value['tags'],
			);
			array_push($docToUpload, $item);
		}
		$result = $doc->update($docToUpload, $tableName);
		$this->assertArrayHasKey('totalIndexed', $result);
		$this->assertArrayHasKey('jsonData', $result);
		$this->assertCount($result['totalIndexed'], $docToUpload);
		$json = json_encode($docToUpload);
		$expected = $this->getDocHandler()->update($json, $tableName);
		$this->assertEquals($expected,$result['jsonData']);
	}

	public function testDetail() {
		$doc = new Core\Doc();
		$docId = 1;
		$result = $doc->detail($docId);
		$expected = $this->getDocHandler()->detail($docId);
		$this->assertEquals($expected, $result);
	}

	public function testPostToOpenSearchObject() {
		$doc = new Core\Doc();
		$post = new \WP_Post();
		$row = $doc->postToOpenSearchObject($post);
		$this->assertNotEmpty($row);
	}

	public function testGetImage() {

	}

	public function getDocHandler() {
		$client = new SDK\CloudsearchClient(
			Registry::instance()->getAccessKeyId(),
			Registry::instance()->getAccessKeySecret(),
			array("host"=>Registry::instance()->getAccessHost(),"debug"=>true),
			Registry::instance()->getAccessKeyType()
		);
		$docHandler = new SDK\CloudsearchDoc(Registry::instance()->getAppName(), $client);
		return $docHandler;
	}
}