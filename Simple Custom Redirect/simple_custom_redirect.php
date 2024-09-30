
<?php

$snippet_name = "simple_custom_redirect";
$version = "<!#FV> 0.0.1 </#FV>";

/*
 * Define your base URL, word to identify, and destination URL
 * any occurrence of the word, either in the URL or in a query string, will be redirected
 * 
 * */


function mk_custom_redirect_based_on_url() {
    $base_url = home_url();
    $word_to_identify = 'test';
    $destination_url = home_url().'/home2'; // Add your destination URL here

    // Get the current URL
    $current_url = home_url(add_query_arg(null, null));

    // Check if the word to identify is in the current URL
    if (strpos($current_url, $word_to_identify) !== false) {
        // If it is, redirect to the destination URL instead of the base URL
        wp_redirect($destination_url);
        exit;
    }
}
add_action('template_redirect', 'mk_custom_redirect_based_on_url');
