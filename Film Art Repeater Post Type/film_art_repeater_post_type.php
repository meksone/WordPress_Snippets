<?php

$snippet_name = "film_art_repeater_post_type";
$version = "<!#FV> 0.0.3 </#FV>";

/**
 * On film post type repeater save, create/update film-art posts
 * The film-art posts are created or updated based on the repeater field data.
 * This code ensures that film-art posts are created with the same dates as the film post.
 */

// Hook into post save for 'film' post type
add_action('acf/save_post', 'handle_film_art_repeater_save', 20);

function handle_film_art_repeater_save($post_id) {
    // Only proceed if this is a 'film' post type
    if (get_post_type($post_id) !== 'film') {
        return;
    }
    
    // Check if the post has the 'art' category (ID: 13)
    if (!has_term(13, 'categoria', $post_id)) {
        return;
    }
    
    // Prevent infinite loops
    if (defined('DOING_FILM_ART_UPDATE')) {
        return;
    }
    define('DOING_FILM_ART_UPDATE', true);
    
    // Get the repeater field data
    $repeater_data = get_field('art_ripetitore', $post_id);
    
    if (!$repeater_data) {
        // If no repeater data, delete all existing film-art posts for this film
        delete_all_film_art_posts($post_id);
        return;
    }
    
    // Get existing film-art posts for this film
    $existing_film_art_posts = get_existing_film_art_posts($post_id);
    $existing_art_ids = array_column($existing_film_art_posts, 'art_id', 'post_id');
    
    $current_art_ids = array();
    
    // Process each repeater row
    foreach ($repeater_data as $index => $row) {
        $row_number = $index + 1;
        $art_id = 'art' . $post_id . '-' . sprintf('%02d', $row_number);
        $current_art_ids[] = $art_id;
        
        // Update the art_id field in the repeater
        update_sub_field(array('art_ripetitore', $index + 1, 'art_id'), $art_id, $post_id);
        
        // Check if film-art post already exists with this art_id
        $existing_post_id = array_search($art_id, $existing_art_ids);
        
        if ($existing_post_id) {
            // Update existing film-art post
            update_film_art_post($existing_post_id, $row, $post_id, $art_id);
        } else {
            // Create new film-art post
            create_film_art_post($row, $post_id, $art_id);
        }
    }
    
    // Delete film-art posts that no longer exist in the repeater
    foreach ($existing_film_art_posts as $film_art_post) {
        if (!in_array($film_art_post['art_id'], $current_art_ids)) {
            wp_delete_post($film_art_post['post_id'], true);
        }
    }
}

/**
 * Create a new film-art post
 * MODIFIED: Uses same dates as film post
 */
function create_film_art_post($row_data, $film_post_id, $art_id) {
    // Get film post dates
    $film_post = get_post($film_post_id);
    
    $post_data = array(
        'post_title'    => sanitize_text_field($row_data['titolo']),
        'post_type'     => 'film-art',
        'post_status'   => 'publish',
        'post_date'     => $film_post->post_date,           // Same creation date
        'post_date_gmt' => $film_post->post_date_gmt,       // Same creation date GMT
        'post_modified' => $film_post->post_modified,       // Same modification date
        'post_modified_gmt' => $film_post->post_modified_gmt // Same modification date GMT
    );
    
    $film_art_post_id = wp_insert_post($post_data);
    
    if ($film_art_post_id && !is_wp_error($film_art_post_id)) {
        // Update ACF fields
        update_field('autore', sanitize_text_field($row_data['autore']), $film_art_post_id);
        update_field('link_autore', esc_url_raw($row_data['link_autore']), $film_art_post_id);
        
        // Handle image field properly
        $image_value = handle_image_field_value($row_data['film_art']);
        update_field('film_art', $image_value, $film_art_post_id);
        
        update_field('art_id', $art_id, $film_art_post_id);
        
        // Set relationship field with proper ACF format
        update_field('film_correlato', array($film_post_id), $film_art_post_id);
    }
    
    return $film_art_post_id;
}

/**
 * Update an existing film-art post
 * MODIFIED: Updates dates to match film post
 */
function update_film_art_post($film_art_post_id, $row_data, $film_post_id, $art_id) {
    // Get film post dates
    $film_post = get_post($film_post_id);
    
    // Update post title and dates
    wp_update_post(array(
        'ID'            => $film_art_post_id,
        'post_title'    => sanitize_text_field($row_data['titolo']),
        'post_date'     => $film_post->post_date,           // Same creation date
        'post_date_gmt' => $film_post->post_date_gmt,       // Same creation date GMT
        'post_modified' => $film_post->post_modified,       // Same modification date
        'post_modified_gmt' => $film_post->post_modified_gmt // Same modification date GMT
    ));
    
    // Update ACF fields
    update_field('autore', sanitize_text_field($row_data['autore']), $film_art_post_id);
    update_field('link_autore', esc_url_raw($row_data['link_autore']), $film_art_post_id);
    
    // Handle image field properly
    $image_value = handle_image_field_value($row_data['film_art']);
    update_field('film_art', $image_value, $film_art_post_id);
    
    update_field('art_id', $art_id, $film_art_post_id);
    
    // Set relationship field with proper ACF format
    update_field('film_correlato', array($film_post_id), $film_art_post_id);
}

/**
 * Handle image field value based on its format
 */
function handle_image_field_value($image_data) {
    // If it's empty or null, return as is
    if (empty($image_data)) {
        return $image_data;
    }
    
    // If it's already an attachment ID (integer), return as is
    if (is_numeric($image_data)) {
        return intval($image_data);
    }
    
    // If it's an array (image object), extract the ID
    if (is_array($image_data) && isset($image_data['ID'])) {
        return intval($image_data['ID']);
    }
    
    // If it's an array with 'id' key (sometimes ACF uses lowercase)
    if (is_array($image_data) && isset($image_data['id'])) {
        return intval($image_data['id']);
    }
    
    // If it's a URL, try to get attachment ID by URL
    if (is_string($image_data) && filter_var($image_data, FILTER_VALIDATE_URL)) {
        $attachment_id = attachment_url_to_postid($image_data);
        if ($attachment_id) {
            return intval($attachment_id);
        }
    }
    
    // If nothing else works, return the original value
    return $image_data;
}

/**
 * Get existing film-art posts for a specific film
 */
function get_existing_film_art_posts($film_post_id) {
    $args = array(
        'post_type' => 'film-art',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'film_correlato',
                'value' => serialize(strval($film_post_id)),
                'compare' => 'LIKE'
            )
        )
    );
    
    $posts = get_posts($args);
    $result = array();
    
    foreach ($posts as $post) {
        $art_id = get_field('art_id', $post->ID);
        $result[] = array(
            'post_id' => $post->ID,
            'art_id' => $art_id
        );
    }
    
    return $result;
}

/**
 * Delete all film-art posts for a specific film
 */
function delete_all_film_art_posts($film_post_id) {
    $existing_posts = get_existing_film_art_posts($film_post_id);
    
    foreach ($existing_posts as $film_art_post) {
        wp_delete_post($film_art_post['post_id'], true);
    }
}

/**
 * Handle film post deletion - also delete related film-art posts
 */
add_action('before_delete_post', 'cleanup_film_art_on_film_delete');

function cleanup_film_art_on_film_delete($post_id) {
    if (get_post_type($post_id) === 'film') {
        delete_all_film_art_posts($post_id);
    }
}

/**
 * Handle individual repeater row deletion via AJAX (if needed)
 * This is an additional safety measure for frontend editing
 */
add_action('wp_ajax_delete_film_art_row', 'handle_film_art_row_deletion');
add_action('wp_ajax_nopriv_delete_film_art_row', 'handle_film_art_row_deletion');

function handle_film_art_row_deletion() {
    if (!isset($_POST['art_id']) || !isset($_POST['film_id'])) {
        wp_die('Missing required parameters');
    }
    
    $art_id = sanitize_text_field($_POST['art_id']);
    $film_id = intval($_POST['film_id']);
    
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'film_art_nonce')) {
        wp_die('Security check failed');
    }
    
    // Find and delete the film-art post with this art_id
    $args = array(
        'post_type' => 'film-art',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'art_id',
                'value' => $art_id,
                'compare' => '='
            ),
            array(
                'key' => 'film_correlato',
                'value' => serialize(strval($film_id)),
                'compare' => 'LIKE'
            )
        )
    );
    
    $posts = get_posts($args);
    
    if (!empty($posts)) {
        wp_delete_post($posts[0]->ID, true);
        wp_send_json_success('Film-art post deleted successfully');
    } else {
        wp_send_json_error('Film-art post not found');
    }
}