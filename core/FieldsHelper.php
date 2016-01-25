<?php

namespace OpenSearch\Core;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class FieldsHelper {
	/**
	 * Get fields for sync
	 * @return [type] [description]
	 */
	public static function getFieldsForQuery() {
		$fields = array(
			'object_id'  => 'ID',
			'date'       => 'post_date',
			'title'      => 'post_title',
			'content'    => 'post_content',
			'excerpt'    => 'post_excerpt',
			'slug'       => 'post_name',
			'modified'   => 'post_modified',
			'parent'     => 'post_parent',
			'menu_order' => 'menu_order',
			'type'       => 'post_type',
			'permalink'  => 'guid',
			'author_id'  => 'post_author',
		);
		return implode(' , ', $fields);
	}

	/**
	 * Return the array of posts to index with their fields
	 * @return array
	 */
	public static function getPostTypesToIndex(){
		$defaultPostsTypes = array(
			'post' => array('indexFeaturedImage' => TRUE),
			'page' => array('indexFeaturedImage' => TRUE),
		);
		return $defaultPostsTypes;
	}

	/**
	 * Return post type labels
	 * @param  array $postTypes
	 * @return array
	 */
	public static function getPostTypesLabels( $postTypes ) {
		$postTypesLabels = array();
		if( empty( $postTypes ) ){
			return $postTypesLabels;
		}
		foreach ( array_keys( $postTypes ) as $type ) {
			$postType = get_post_type_object( $type );
			if( $postType && !empty( $postType->labels ) && !empty( $postType->labels->name ) ){
				$postTypesLabels[$type] = $postType->labels->name;
			}
		}

		return $postTypesLabels;
	}
}