<?php

namespace OpenSearch\Admin\Controllers;
use \OpenSearch\Core;
use \OpenSearch\Core\Registry;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Indexer  {
	private static $instance;
	private static $doc;

	/**
	 *
	 */
	public function __construct ( ) {
		if( Registry::instance()->isEnabled() ){
			// Delete the post in the index when it was deleted in the site
			add_action( 'deleted_post', array( &$this, 'postDeleted' ) );
			// Delete the post in the index when it was unpublished
			add_action( 'transition_post_status', array( &$this, 'postUnpublished' ), 10, 3 );
			// Update the post in the index when it was updated
			// JUST WHEN IT IS publish
			add_action( 'save_post', array( &$this, 'postUpdated' ), 11, 3 );

			// Update the term in the index when the counter was updated in WP
			add_action( "edited_term_taxonomy", array( &$this, 'termTaxonomyUpdated' ), 10, 2 );
		}
	}

	/**
	 *
	 * @return \OpenSearch\Core\Registry
	 */
	static function instance() {
		if ( Core\Utils::readyForClient() && Core\Utils::readyToIndex() && Core\Utils::readyForTemplate() && empty(self::$doc)) {
			self::$doc = new Core\Doc();
		}
		if (!isset( self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Remove the post in OpenSearch when it was unpublished in WP
	 * Called from transition_post_status action
	 * @param string $new_status
	 * @param string $old_status
	 * @param \WP_Post $post
	 */
	public function postUnpublished( $new_status, $old_status, $post ) {
		// pre('this is postUnpublished',$post,$new_status);
		// exit;
		if ( $old_status == 'publish' && $new_status != 'publish' && !empty( $post->ID ) ) {
			// Post is unpublished so remove from index
			try {
				$tableName = 'main';
				$result = static::$doc->remove( array(array('ID'=>$post->ID)), $tableName );
			} catch ( Exception $exc ) {

			}
		}
	}

	/**
	 * Update post in OpenSearch when it was unpdated in WP
	 * Called from save_post action
	 * @param integer $postID
	 * @param \WP_Post $post
	 */
	public function postUpdated( $postID, $post ) {
		// pre('this is postUpdated',$post,$postID);
		// exit;
		if ( wp_is_post_revision( $postID ) ){ return; }
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $postID; }
		if ( $post->post_status !== 'publish' ) { return $postID; }

		try {
			$tableName = 'main';
			$detail = static::$doc->detail( $postID );
			$item = static::$doc->postToOpenSearchObject( $post );
			$docsToUpload[] = $item;
			if( $item ){
				if (empty($detail)) {
					$result = static::$doc->add( $docsToUpload, $tableName );
				} else {
					$result = static::$doc->update( $docsToUpload, $tableName );
				}

				//do_action( 'mvnAlgObjectIndexedOnPostUpdate', $post, $item );
			}
		} catch ( Exception $exc ) {

		}
	}

	/**
	 * Remove the post from OpenSearch when it was deleted in WP
	 * Called from deleted_post action
	 * @param int $postId
	 */
	public function postDeleted( $postId ) {
		// pre('this is postDeleted',$postId);
		// exit;
		if ( !empty( $postId ) ) {
			try {
				$tableName = 'main';
				$result = static::$doc->remove( array(array('ID'=>$postId)), $tableName );
			} catch ( Exception $exc ) {

			}
		}
	}

	/**
	 * Update term in OpenSearch when it was unpdated in WP
	 * Called from edited_term and created_term actions
	 * @param integer $ttId
	 * @param object $taxonomy
	 */
	public function termTaxonomyUpdated( $ttId, $taxonomy ) {
		// pre('this is termTaxonomyUpdated');
		// exit;
		$taxonomy = !empty($taxonomy->name) ? $taxonomy->name : $taxonomy;

		// Get the object before deletion so we can pass to actions below
		$termUpdated = get_term_by( 'term_taxonomy_id', $ttId, $taxonomy );

		if ($taxonomy == 'category') {
			$catId = $termUpdated->term_id;
			$posts = query_posts("cat=$catId");
		} elseif ($taxonomy == 'post_tag') {
			$tagId = $termUpdated->term_id;
			$posts = query_posts("tag_id=$tagId");
		}
		if( !is_wp_error( $termUpdated ) && $termUpdated && !empty($posts) && count($posts) >= 1 ){
			try {
				$tableName = 'main';
				$docsToUpload = static::$doc->buildBatch( $posts );
				// pre($docsToUpload);
				// exit;
				if( !empty($docsToUpload) ){
					$result = static::$doc->update( $docsToUpload, $tableName );
				}
			} catch ( Exception $exc ) {

			}
		}
	}
}
