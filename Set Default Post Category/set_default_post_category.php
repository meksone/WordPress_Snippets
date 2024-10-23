<?php

$snippet_name = "set_default_post_category";
$version = "<!#FV> 0.0.1 </#FV>";

function set_default_category($post_ID) {
    $default_category_id = 1; // Set your default category ID here

    // Get the post type
    $post_type = get_post_type($post_ID);

    // Check if the post type is 'post'
    if($post_type == 'post') {
        $categories = wp_get_post_categories($post_ID);

        if(empty($categories)) {
            wp_set_post_categories($post_ID, array($default_category_id));
        }
    }
}
add_action('save_post', 'set_default_category');