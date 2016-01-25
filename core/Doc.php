<?php

namespace OpenSearch\Core;
use \OpenSearch\Core\Registry;
use \OpenSearch\SDK;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Doc {
	private static $client = null;
	private static $doc = null;

	public function __construct() {
		if (empty(self::$doc)) {
			self::instance();
		}
	}

	static function instance() {
		if (empty(self::$client)) {
			self::$client = new SDK\CloudsearchClient(
				Registry::instance()->getAccessKeyId(),
				Registry::instance()->getAccessKeySecret(),
				array("host"=>Registry::instance()->getAccessHost(),"debug"=>true),
				Registry::instance()->getAccessKeyType()
			);
		}
		$app_name = Registry::instance()->getAppName();
		if ( empty( self::$doc ) && !empty( $app_name ) && !empty( self::$client ) ) {
			self::$doc = new SDK\CloudsearchDoc($app_name, self::$client);
		} else {
			throw new \Exception( 'CloudsearchClient没有实例化或者应用名称为空' );
		}
	}

	/**
	 * Upload document to OpenSearch
	 * @param array  $docs
	 * @param string $tableName
	 * @return array
	 */
	public function add( $docs, $tableName ) {
		// pre($docs);
		// exit;
		$this->remove( $docs, $tableName );

		$docs_to_upload = array();
		foreach ($docs as $key => $value) {
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
			array_push($docs_to_upload, $item);
		}
		$json   = json_encode($docs_to_upload);
		$result = array(
					'totalIndexed' => count($docs_to_upload),
					'jsonData'     => self::$doc->add($json, $tableName)
				);
		return $result;
	}

	/**
	 * Delete document from OpenSearch
	 * @param array   $docs
	 * @param string  $tableName
	 * @return array
	 */
	public function remove( $docs, $tableName ) {
		$docs_to_upload = array();
		foreach ($docs as $key => $value) {
			$item           = array();
			$item['cmd']    = 'delete';
			$item["fields"] = array(
				'object_id'      => $value['ID']
			);
			array_push($docs_to_upload, $item);
		}
		$json   = json_encode($docs_to_upload);
		$result = array(
					'totalRemoved' => count($docs_to_upload),
					'jsonData'     => self::$doc->remove($json, $tableName)
				);
		return $result;
	}

	/**
	 * Update document from OpenSearch
	 * @param  array  $docs
	 * @param  string $tableName
	 * @return array
	 */
	public function update( $docs, $tableName ) {
		$docs_to_upload = array();
		foreach ($docs as $key => $value) {
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
			array_push($docs_to_upload, $item);
		}
		$json   = json_encode($docs_to_upload);
		$result = array(
					'totalIndexed' => count($docs_to_upload),
					'jsonData'     => self::$doc->update($json, $tableName)
				);
		return $result;
	}

	/**
	 * Get wp document from OpenSearch
	 * @param  integer $docId
	 * @return string
	 */
	public function detail( $docId ) {
		return self::$doc->detail($docId);
	}

	/**
	 * Get wp document from database
	 * @param  string  $tableName
	 * @param  integer $postsPerPage
	 * @param  integer $offset
	 * @return array
	 */
	public function getDoces( $tableName, $postsPerPage = -1, $offset = 0 ) {
		global $wpdb;

		if ( ! $tableName  ) {
			throw new \Exception( 'Missing or Invalid Table Name' );
		}

		$limit = '';
		if( (int)$postsPerPage > 0 ){
			$limit = sprintf( "LIMIT %d, %d", $offset, $postsPerPage );
		}
		$postFields = FieldsHelper::getFieldsForQuery();
		$where = " AND post_status = 'publish' ";
		$query = "SELECT {$postFields} FROM {$wpdb->posts} WHERE 1 = 1 {$where} {$limit}";
		$posts = $wpdb->get_results( $query );
		$batch = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$row = $this->postToOpenSearchObject( $post );
				array_push( $batch, $row );
			}
		}
		unset($postFields,$row, $post, $posts);

		return $batch;
	}

	/**
	 * build posts to adapt to OpenSearch
	 * @param \WP_Post $posts
	 * @return array
	 */
	public function buildBatch( $posts ) {
		$batch = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$row = $this->postToOpenSearchObject( $post );
				array_push( $batch, $row );
			}
		}

		return $batch;
	}

	/**
	 * Convert WP_post to OpenSearch array
	 * @param \WP_Post $post
	 * @return array
	 */
	public function postToOpenSearchObject( $post ) {
		$row = array();

		// Get WP Tags
		$tags = array();
		$terms = wp_get_post_terms( $post->ID, 'post_tag' );
		if( !empty( $terms && is_array($terms) ) ) {
			foreach ($terms as $key => $value) {
				if (!empty($value->name)) {
					$tags[] = $value->name;
				}
			}
		}
		if( !empty( $tags ) && is_array( $tags ) ) {
			$tags = array_unique($tags);
			$row[ 'tags' ] = implode(',', $tags);
		} else {
			$row[ 'tags' ] = '';
		}
		// pre($terms, $tags);
		unset( $terms, $tags );

		// Get WP Categories
		$categories = array();
		$terms = wp_get_post_terms( $post->ID, 'category' );
		if( !empty( $terms && is_array($terms) ) ) {
			foreach ($terms as $key => $value) {
				if (!empty($value->name)) {
					$categories[] = $value->name;
				}
			}
		}
		if( !empty( $categories ) && is_array( $categories ) ){
			$categories = array_unique($categories);
			$row[ 'categories' ] = implode(',', $categories);
		} else {
			$row[ 'categories' ] = '';
		}
		// pre($terms, $categories);
		unset( $terms, $categories );

		// Get WP featured_image
		if( has_post_thumbnail( $post->ID ) ){
			$postThumbId = get_post_thumbnail_id( $post->ID );
			$row['featured_image'] = $this->getImage( $postThumbId );
		} else {
			$row['featured_image'] = '';
		}

		// Get WP author
		$row['author'] = get_the_author_meta( 'display_name', $post->post_author );

		foreach ($post as $key => $value) {
			if (in_array($key, array('post_date','post_modified'))) {
				$row[$key] = strtotime($value);
			} else {
				$row[$key] = $value;
			}
		}

		return $row;
	}

	/**
	 * Get image info
	 * @global type $wpdb
	 * @param int $attachId Attachment Post ID
	 * @return array  Image information
	 */
	public function getImage( $attachId ) {
		global $wpdb;

		if( empty($attachId) ){
			return array();
		}
		$uploadDir = wp_upload_dir();
		$uploadBaseUrl = $uploadDir['baseurl'];
		$image['ID'] = $attachId;
		//we will need to get the ALT info from Metas
		$image['alt'] = get_post_meta( $attachId, '_wp_attachment_image_alt', TRUE );

		$query = $wpdb->prepare( "SELECT post_title, post_content, post_excerpt, post_mime_type FROM {$wpdb->posts} WHERE ID = %d", $attachId );
		$attachment = $wpdb->get_row( $query );
		if( $attachment ){
			$image['title']       = $attachment->post_title;
			$image['description'] = $attachment->post_content;
			$image['caption']     = $attachment->post_excerpt;
			$image['mime_type']   = $attachment->post_mime_type;
		}
		unset( $query, $attachment );

		$attachmentMeta = get_post_meta( $attachId, '_wp_attachment_metadata', TRUE );

		if( is_array($attachmentMeta) && !empty( $attachmentMeta ) ){
			$image['width']  = $attachmentMeta['width'];
			$image['height'] =	$attachmentMeta['height'];
			$image['file']   = sprintf('%s/%s', $uploadBaseUrl, $attachmentMeta['file'] );
			$image['sizes']  = $attachmentMeta['sizes'];
			if( isset( $image['sizes'] ) && is_array( $image['sizes'] ) ){
				$sizesToIndex = apply_filters( 'ma_image_siz	es_to_index', array('thumbnail', 'medium', 'large') );
				foreach ( $image['sizes'] as $size => &$sizeAttrs ) {
					if( !in_array( $size, $sizesToIndex ) ){
						unset( $image['sizes'][$size] );
						continue;
					}
					if( isset( $sizeAttrs['file'] ) && $sizeAttrs['file'] ){
						$baseFileUrl       = str_replace( wp_basename($attachmentMeta['file']), '', $attachmentMeta['file']);
						$sizeAttrs['file'] = sprintf( '%s/%s%s', $uploadBaseUrl, $baseFileUrl, $sizeAttrs['file']);
					}
				}
			}
			unset($attachmentMeta);
		}
		return $image;
	}
}