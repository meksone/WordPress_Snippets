<?php

$snippet_name = "gutenberg_whitelist_blocks";
$version = "<!#FV> 0.0.1 </#FV>";

/* Allow only some blocks
 * references https://wordpress.org/documentation/article/blocks-list/
 * reference https://www.wpexplorer.com/wordpress-core-blocks-list/#category-widgets
 * */
add_filter( 'allowed_block_types_all', 'mk_gute_whitelist_blocks' );
function mk_gute_whitelist_blocks( $allowed_block_types ) {
    return array(
        'core/paragraph',
        'core/heading',
        'core/list',
		'core/image'
    );
}


// disable gutenberg frontend styles @ https://m0n.co/15
function disable_gutenberg_wp_enqueue_scripts() {
	
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	
}
add_filter('wp_enqueue_scripts', 'disable_gutenberg_wp_enqueue_scripts', 100);