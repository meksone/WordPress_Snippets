<?php
$version = "<!#FV> 0.0.1 </#FV>";

// custom die handler
function wpmu_get_custom_die_handler() {
    return 'wpmu_custom_css_on_wp_die';
}

// hook the function
add_filter('wp_die_handler', 'wpmu_get_custom_die_handler' );

// custom login for theme
function wpmu_custom_css_on_wp_die($message, $title = '', $args = array() ) {
	//$defaults = array( 'ciao' => 403 );
	$r = wp_parse_args($args);
	//$r['response']; //output error code response
	$message = $message;
   require_once get_stylesheet_directory() . '/wp-die.php';
	die();
}