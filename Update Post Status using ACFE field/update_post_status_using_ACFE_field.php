<?php

$snippet_name = "update_post_status_using_ACFE_field";
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * Updates post status based on the 'news_status' custom field value.
 * Properly handles trashing and trashed posts restoration.
 *  * 
 * @param int    $post_id The ID of the post being saved
 * @param object $post    The post object
 * @param bool   $update  Whether this is an existing post being updated
 * 
 * @return void
 */
function update_post_status_according_to_news_status($post_id, $post = null, $update = false) {
    // Bail if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Bail if this is a post revision
    if (wp_is_post_revision($post_id)) {
        return;
    }

    // Verify user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    process_status_update($post_id);
}

/**
 * Handles the status update when custom field is updated
 * 
 * @param int    $meta_id    ID of updated metadata entry
 * @param int    $post_id    Post ID
 * @param string $meta_key   Metadata key
 * @param mixed  $meta_value Metadata value
 */
function handle_news_status_meta_update($meta_id, $post_id, $meta_key, $meta_value) {
    if ($meta_key !== 'news_status') {
        return;
    }

    process_status_update($post_id);
}

/**
 * Core logic for processing the status update
 * 
 * @param int $post_id The ID of the post being updated
 */
function process_status_update($post_id) {
    // Get the news status value
    $news_status = get_post_meta($post_id, 'news_status', true);

    // Bail if no news status is set
    if (empty($news_status)) {
        return;
    }

    // Define allowed status mappings
    $status_mappings = array(
        'draft' => 'draft',
        'pending' => 'pending',
        'private' => 'private',
        'trash' => 'trash',
        'future' => 'future',
        'publish' => 'publish'
    );

    // Get the mapped status or default to 'publish'
    $post_status = isset($status_mappings[$news_status]) ? $status_mappings[$news_status] : 'publish';

    // Get current post status
    $current_status = get_post_status($post_id);

    // Only proceed if the status is actually different
    if ($current_status !== $post_status) {
        // Remove both hooks temporarily to prevent infinite loops
        remove_action('save_post', 'update_post_status_according_to_news_status', 10);
        remove_action('updated_post_meta', 'handle_news_status_meta_update', 10);
        remove_action('added_post_meta', 'handle_news_status_meta_update', 10);

        // Special handling for trash/untrash operations
        if ($current_status === 'trash' && $post_status !== 'trash') {
            // Untrash the post first
            wp_untrash_post($post_id);
            
            // Then update to the desired status if it's not 'publish'
            if ($post_status !== 'publish') {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => $post_status
                ));
            }
        } elseif ($post_status === 'trash') {
            // Use wp_trash_post for moving to trash
            wp_trash_post($post_id);
        } else {
            // Normal status update
            $result = wp_update_post(array(
                'ID' => $post_id,
                'post_status' => $post_status
            ));

            // Log any errors
            if (is_wp_error($result)) {
                error_log(sprintf(
                    'Failed to update post status for post ID %d: %s',
                    $post_id,
                    $result->get_error_message()
                ));
            }
        }

        // Re-add both hooks
        add_action('save_post', 'update_post_status_according_to_news_status', 10, 3);
        add_action('updated_post_meta', 'handle_news_status_meta_update', 10, 4);
        add_action('added_post_meta', 'handle_news_status_meta_update', 10, 4);

        // Fire an action for other plugins to hook into
        do_action('news_status_updated', $post_id, $post_status, $current_status);
    }
}

// Add all necessary hooks
add_action('save_post', 'update_post_status_according_to_news_status', 10, 3);
add_action('updated_post_meta', 'handle_news_status_meta_update', 10, 4);
add_action('added_post_meta', 'handle_news_status_meta_update', 10, 4);