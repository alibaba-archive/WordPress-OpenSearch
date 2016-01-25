<?php

namespace OpenSearch\Core;
use \OpenSearch\Core\Registry;
use \OpenSearch\SDK;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;

class Search {
	private static $client = null;
	private static $search = null;
    private static $appName = null;

    private $tags;
    private $categories;

    /**
     * [__construct description]
     */
	public function __construct ( ) {
		if (empty(self::$doc)) {
			self::instance();
		}

		if ( !is_admin() ) {
            add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
            add_filter('the_posts', array($this, 'thePosts'), 10, 1);
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

		$appName = Registry::instance()->getAppName();
		if (empty($appName)) {
			throw new \Exception( '请首先定义应用名称' );
		}

		if (empty(self::$appName)) {
			self::$appName = $appName;
		}
		if(empty(self::$search) && !empty(self::$client)) {
			self::$search = new SDK\CloudsearchSearch(self::$client);
		} else {
			throw new \Exception( 'CloudsearchClient没有实例化' );
		}
	}

    /**
     * Hook with wordpress the_posts filter
     * @param  \WP_Post $posts
     * @return \WP_Post
     */
    public function thePosts($posts) {
        $searchResults = array();
        if( is_search() && !is_admin() ) {
            $query = stripslashes( get_search_query( false ) );

            try {
                $searchResults = $this->frontEndSearch( $query );
            } catch( \Exception $e ) {
                $searchResults = array();
            }

            $posts = !empty($searchResults['posts']) ? $searchResults['posts'] : array();

            // pre($posts);
        }
        return $posts;
    }

    /**
     * Hook with wordpress 'wp_enqueue_scripts' action
     * @return void
     */
    public function enqueueScripts() {
        $csspath = Registry::instance()->getPluginUrl() . "front/assets/styles/opensearch.css";
        wp_enqueue_style( 'openSearchFrontSettings', $csspath, array(), Registry::instance()->getPluginVersion() );
    }

    /**
     * Get OpenSearch result for the front show
     * @param  string $query
     * @return \WP_Post
     */
    public function frontEndSearch( $query )
    {
        $paged = get_query_var('paged');
        $paged = $paged > 0 ? $paged : 1;
        $limit = get_query_var('posts_per_page');
        $offset = ($paged - 1) * $limit;

        $query = "default:'{$query}'";
        static::$search->addIndex(static::$appName);
        static::$search->setQueryString($query);
        static::$search->setFormat('json');
        static::$search->setHits($limit);
        static::$search->setStartHit($offset);
        static::$search->addSummary('title', 100, 'em', '...', 3);
        static::$search->addSummary('content', 300, 'em', '...', 5);
        static::$search->addSummary('categories', 100, 'em', 3, '', '');
        static::$search->addSummary('tags', 100, 'em', 3);
        static::$search->addSummary('author');
        static::$search->addSummary('excerpt', 100, 'em', 3);
        static::$search->addSummary('slug', 100, 'em', 3);

        $ret = static::$search->search();
        $ret = json_decode($ret);

        // pre($ret);

        $result = array(
            'total' => 0,
            'posts' => array()
        );

        if ($ret && isset($ret->result) && $ret->result->viewtotal > 0) {
            $posts = array();
            $localOffsetSecs = get_option('gmt_offset') * HOUR_IN_SECONDS;
            foreach ($ret->result->items as $item) {
                $post = new \WP_Post($item);
                $post->ID = $item->object_id;
                if (empty($item->excerpt)) {
                    $post->post_excerpt = $item->content;
                } else {
                    $post->post_excerpt = $item->excerpt;
                }
                $post->post_date = date(
                    'Y-m-d H:i:s',
                    $item->date + $localOffsetSecs
                );
                $post->post_date_gmt = date(
                    'Y-m-d H:i:s',
                    $item->date
                );
                $post->post_modified = date(
                    'Y-m-d H:i:s',
                    $item->modified + $localOffsetSecs
                );
                $post->post_modified_gmt = date(
                    'Y-m-d H:i:s',
                    $item->modified
                );
                $post->post_title = $item->title;
                $post->post_content = $item->content;
                $post->guid = $item->permalink;
                $post->post_parent = $item->parent;
                unset($post->title, $post->content, $post->date, $post->modified, $post->permalink, $post->parent, $post->object_id);
                $posts[] = $post;

                $this->tags       = $item->tags;
                $this->categories = $item->categories;

                add_filter('get_the_tags', array($this, 'filterTheTags'), 10, 1);
                add_filter('get_the_categories', array($this, 'filterTheCategories'), 10, 1);
                wp_cache_set($post->ID, $post, 'posts');
            }

            // pre($posts);

            $result['posts'] = $posts;
            $result['total'] = $ret->result->total;
        }

        return $result;
    }

    /**
     * Filter the tags to show in the front
     * @param  array $terms
     * @return array
     */
    public function filterTheTags($terms) {
        if (!empty($terms)) {
            try {
                $tags = explode(',', $this->tags);
                foreach ($terms as $key => &$value) {
                    foreach ($tags as $v) {
                        $name = strtolower($value->name);
                        $val = strtolower(strip_tags($v));
                        // pre($val);
                        // pre($name);
                        if ($val == $name) {
                            $value->name = $v;
                        }
                    }
                }
            } catch (Exception $e) {}
            return $terms;
        }
    }

    /**
     * Filter the categories to show in the front
     * @param  array $terms
     * @return array
     */
    public function filterTheCategories($terms) {
        if (!empty($terms)) {
            try {
                $categories = explode(',', $this->categories);
                foreach ($terms as $key => &$value) {
                    // pre($value);
                    foreach ($categories as $v) {
                        $name = strtolower($value->name);
                        $val = strtolower(strip_tags($v));
                        if ($val == $name) {
                            $value->name = $v;
                        }
                    }
                }
            } catch (Exception $e) {}
            return $terms;
        }
    }
}
