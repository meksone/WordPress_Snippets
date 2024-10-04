<?php

$snippet_name = "custom_wp_die_page";
$version = "<!#FV> 0.0.1 </#FV>";
/**
 * Create a custom WP_Die() page
 * Remember to point to theme/site assets in wp-die.php 
 */

// custom die handler
function mk_get_custom_die_handler() {
    return 'mk_custom_css_on_wp_die';
}

// hook the function
add_filter('wp_die_handler', 'mk_get_custom_die_handler' );

// custom login for theme
function mk_custom_css_on_wp_die($message, $title = '', $args = array() ) {
	//$defaults = array( 'ciao' => 403 );
	$r = wp_parse_args($args);
	//$r['response']; //output error code response
	$message = $message;
   require_once get_stylesheet_directory() . '/wp-die.php';
	die();
}