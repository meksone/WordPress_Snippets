<?php
$snippet_name="elementor_fix_acf_group";
$version = "<!#FV> 0.0.1 </#FV>";

function mk_sync_acf_fields( $post_id ) {
    // Define the post types where this function should run
    $applicable_post_types = array('film'); // Add your post types here

    // Get the current post type
    $post_type = get_post_type($post_id);

    // Check if the current post type is in the applicable post types array
    if ( in_array($post_type, $applicable_post_types) ) {
        // Get the value of the master field
        $master_value = get_field('gruppo_art_immagine_art', $post_id);

        // If the master field is not empty and is not an empty array, update the slave field with the master field value
        // If the master field is empty or an empty array, delete the value in the slave field
        if ( !empty($master_value) && !(is_array($master_value) && empty($master_value)) ) {
            update_field('hidden_img_art', $master_value, $post_id);
        } else {
            delete_field('hidden_img_art', $post_id);
        }
    }
}
add_action('acf/save_post', 'mk_sync_acf_fields', 20);