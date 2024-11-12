<?php

$snippet_name = "remove_empty_custom_post_types_archives_from_menu";
$version = "<!#FV> 0.0.1 </#FV>";

// Initialize with specific post types
//init_menu_cpt_cleaner(['movies', 'books']);

// Or check all custom post types
init_menu_cpt_cleaner();


/**
 * Removes custom post type archive links from navigation menus if they have no published posts
 * Includes caching and selective CPT filtering
 */

class Empty_CPT_Archive_Cleaner {
    /**
     * Cache key for storing empty archive URLs
     */
    private const CACHE_KEY = 'empty_cpt_archives_urls';
    
    /**
     * Cache expiration in seconds (default 1 hour)
     */
    private const CACHE_EXPIRATION = 3600;
    
    /**
     * Stores the instance of the class (singleton pattern)
     */
    private static $instance = null;
    
    /**
     * Array of post types to check (empty means check all)
     */
    private $post_types_to_check = array();
    
    /**
     * Get the singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Sets up the necessary hooks
     */
    private function __construct() {
        add_filter('wp_nav_menu_objects', array($this, 'remove_empty_cpt_archives'), 10, 2);
        add_action('save_post', array($this, 'clear_cache'));
        add_action('deleted_post', array($this, 'clear_cache'));
        add_action('trash_post', array($this, 'clear_cache'));
    }
    
    /**
     * Set which post types to check
     * 
     * @param array $post_types Array of post type names to check, empty array means check all
     */
    public function set_post_types($post_types = array()) {
        $this->post_types_to_check = array_filter((array) $post_types);
    }
    
    /**
     * Get empty archive URLs (with caching)
     * 
     * @return array Array of URLs for archives with no published posts
     */
    private function get_empty_archive_urls() {
        // Try to get from cache first
        $cached_urls = wp_cache_get(self::CACHE_KEY);
        if (false !== $cached_urls) {
            return $cached_urls;
        }
        
        $empty_archive_urls = array();
        
        // Get post types to check
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        
        if (!empty($this->post_types_to_check)) {
            $args['name'] = $this->post_types_to_check;
        }
        
        $post_types = get_post_types($args, 'objects');
        
        // Check each custom post type
        foreach ($post_types as $post_type) {
            $count = wp_count_posts($post_type->name);
            
            if (!$count || !isset($count->publish) || $count->publish < 1) {
                $archive_url = get_post_type_archive_link($post_type->name);
                if ($archive_url) {
                    $empty_archive_urls[] = rtrim($archive_url, '/');
                    $empty_archive_urls[] = $archive_url; // Add both with and without trailing slash
                }
            }
        }
        
        // Cache the results
        wp_cache_set(self::CACHE_KEY, $empty_archive_urls, '', self::CACHE_EXPIRATION);
        
        return $empty_archive_urls;
    }
    
    /**
     * Clear the cache when posts are modified
     */
    public function clear_cache() {
        wp_cache_delete(self::CACHE_KEY);
    }
    
    /**
     * Remove empty archive links from navigation menu
     * 
     * @param array $items Array of menu items
     * @param object $args Menu arguments
     * @return array Filtered menu items
     */
    public function remove_empty_cpt_archives($items, $args) {
        if (empty($items)) {
            return $items;
        }
        
        $empty_archive_urls = $this->get_empty_archive_urls();
        
        if (empty($empty_archive_urls)) {
            return $items;
        }
        
        return array_filter($items, function($item) use ($empty_archive_urls) {
            $url = rtrim($item->url, '/');
            return !in_array($url, $empty_archive_urls);
        });
    }
}

/**
 * Initialize the menu cleaner with specific post types (optional)
 * 
 * @param array $post_types Array of post type names to check (empty means check all)
 * @return Empty_CPT_Archive_Cleaner Instance of the cleaner
 */
function init_menu_cpt_cleaner($post_types = array()) {
    $cleaner = Empty_CPT_Archive_Cleaner::get_instance();
    $cleaner->set_post_types($post_types);
    return $cleaner;
}