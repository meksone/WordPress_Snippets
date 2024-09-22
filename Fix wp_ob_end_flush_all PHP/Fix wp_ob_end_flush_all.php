<?php
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * Proper ob_end_flush() for all levels
 *
 * This replaces the WordPress `wp_ob_end_flush_all()` function
 * with a replacement that doesn't cause PHP notices.
 * 
 * reference https://www.kevinleary.net/blog/wordpress-ob_end_flush-error-fix/
 * 
 * Conflict with Duplicator - 2024-09-22
 */
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
add_action( 'shutdown', function() {
   while ( @ob_end_flush() );
} );