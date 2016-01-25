<?php

use \OpenSearch\Core;
use \OpenSearch\Admin\Controllers;

class IndexerTest extends PHPUnit_Framework_TestCase {
	public function testInstance() {
        $doc = new Core\Doc();
        $this->assertInstanceOf('Doc', get_class($doc));

        $indexer = new Controllers\Indexer();
        $this->assertInstanceOf('Indexer', get_class($indexer));
    }

    public function testPostUnpublished() {
        $doc = new Core\Doc();
        $result = $doc->remove( array(array('ID'=>1)), 'main' );
        $this->assertCount(2, $result);
        $this->assertJson($result['jsonData']);
    }

    public function testPostUpdated() {
        $doc = new Core\Doc();
        $detail = $doc->detail( 1 );
        $post = array(); //WP_Post
        $item = $doc->postToOpenSearchObject( $post );
        $docsToUpload[] = $item;
        if( $item ){
            if (empty($detail)) {
                $result = $doc->add( $docsToUpload, 'main' );
            } else {
                $result = $doc->update( $docsToUpload, 'main' );
            }
        }
        $this->assertCount(2, $result);
        $this->assertJson($result['jsonData']);
    }

    public function testPostDeleted() {
        $doc = new Core\Doc();
        $result = $doc->remove( array(array('ID'=>1)), 'main' );
        $this->assertCount(2, $result);
        $this->assertJson($result['jsonData']);
    }

    public function testTermTaxonomyUpdated() {
        $doc = new Core\Doc();
        $tableName = 'main';
        $data = file_get_contents(OPSOU_DIR . '/tests/test.json');
        $data = json_decode($data, true);
        $posts =array();
        foreach ($data as $key => $value) {
            $item = array();
            $item['ID'] = $value['fields']['object_id'];
            $item['post_date'] = $value['fields']['date'];
            $item['post_title'] = $value['fields']['title'];
            $item['post_content'] = $value['fields']['content'];
            $item['post_excerpt'] = $value['fields']['excerpt'];
            $item['post_name'] = $value['fields']['slug'];
            $item['post_modified'] = $value['fields']['modified'];
            $item['post_parent'] = $value['fields']['parent'];
            $item['menu_order'] = $value['fields']['menu_order'];
            $item['post_type'] = $value['fields']['type'];
            $item['guid'] = $value['fields']['permalink'];
            $item['featured_image'] = $value['fields']['featured_image'];
            $item['post_author'] = $value['fields']['author_id'];
            $item['author'] = $value['fields']['author'];
            $item['categories'] = $value['fields']['categories'];
            $item['tags'] = $value['fields']['tags'];

            array_push($posts, $item);
        }
        $docsToUpload = $doc->buildBatch( $posts );
        $this->assertCount(count($posts), $docsToUpload);
        $result = $doc->update( $docsToUpload, $tableName );
        $this->assertCount(2, $result);
        $this->assertJson($result['jsonData']);
    }
}