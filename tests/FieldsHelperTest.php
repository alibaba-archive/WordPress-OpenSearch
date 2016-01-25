<?php

use OpenSearch\Core;

class FieldsHelperTest extends PHPUnit_Framework_TestCase {
	public function testGetFieldsForQuery() {
		$fields = Core\FieldsHelper::getFieldsForQuery();
		$fields = explode(',', $fields);
		$this->assertArrayHasKey('ID', $fields);
		$this->assertArrayHasKey('post_date', $fields);
		$this->assertArrayHasKey('post_title', $fields);
		$this->assertArrayHasKey('post_content', $fields);
		$this->assertArrayHasKey('post_excerpt', $fields);
		$this->assertArrayHasKey('post_name', $fields);
		$this->assertArrayHasKey('post_modified', $fields);
		$this->assertArrayHasKey('post_parent', $fields);
		$this->assertArrayHasKey('menu_order', $fields);
		$this->assertArrayHasKey('post_type', $fields);
		$this->assertArrayHasKey('guid', $fields);
		$this->assertArrayHasKey('post_author', $fields);
	}

	public function testGetPostTypesToIndex() {
		$postTypesToIndex = Core\FieldsHelper::getPostTypesToIndex();
		$keys = array_keys($postTypesToIndex);
		$this->assertArrayHasKey('post', $keys);
		$this->assertArrayHasKey('page', $keys);
	}

	public function testGetPostTypesLabels() {
		$postTypesToIndex = Core\FieldsHelper::getPostTypesToIndex();
		$postLabels       = Core\FieldsHelper::getPostTypesLabels( $postTypesToIndex );

		$type = 'post';
		$postType = get_post_type_object( $type );
		$this->assertEquals($postType->labels->name,$postLabels[$type]);

		$type = 'page';
		$postType = get_post_type_object( $type );
		$this->assertEquals($postType->labels->name,$postLabels[$type]);
	}
}