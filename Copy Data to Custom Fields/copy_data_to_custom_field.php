<?php

$snippet_name = "copy_data_to_custom_field";
$version = "<!#FV> 0.0.1 </#FV>";

// Function to copy the post title to the custom field 'titolo_-_ricerca'
function mk_copy_data_to_custom_field( $post_id ) {
    // Check if it's an autosave, if so, return
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    // Get the post type
    $post_type = get_post_type($post_id);

    // Check if the post type is 'corso-master', if not, return
    if ( 'corso-master' !== $post_type ) {
        return;
    }

    // Verify nonce, if you are using a nonce field for security, this is important
    // if ( !isset( $_POST['your_nonce_field'] ) || !wp_verify_nonce( $_POST['your_nonce_field'], 'your_nonce_action' ) ) {
    //     return;
    // }

    // Get the post title
    $post_title = get_the_title( $post_id );
    // Update the custom field 'titolo_-_ricerca' with the post title
    update_post_meta( $post_id, 'titolo_-_ricerca', strip_tags( $post_title ) );
	
	    if ( has_post_thumbnail( $post_id ) ) {
        // Get the ID of the featured image
        $image_id = get_post_thumbnail_id( $post_id );

        // Save the ID to the custom field 'immagine_corso_master'
        update_post_meta( $post_id, 'immagine_corso_master', $image_id );
    }
	
	
}


// Hook the function to 'save_post' action
add_action( 'save_post', 'mk_copy_data_to_custom_field', 15 );