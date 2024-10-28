<?php
$snippet_name="sync_custom_taxonomies_with_acf_fields";
$version = "<!#FV> 0.0.1 </#FV>";
/**
 * Synchronize custom fields with taxonomies for specific CPT using taxonomy IDs
 * Useful for lowering possible errors in categorization and/or trigger conditional
 * rules on ACF fields
 */
function sync_custom_taxonomies_with_fields($post_id) {
    // Exit if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if we're working with 'film' post type
    if (get_post_type($post_id) !== 'film') {
        return;
    }

    // Check if 'categoria' taxonomy exists
    if (!taxonomy_exists('categoria')) {
        return;
    }

    // Get term IDs (you should replace these with your actual term IDs)
    $art_term_id = 13; // Replace with actual 'art' term ID
    $scuole_term_id = 12; // Replace with actual 'scuole' term ID

    // Get the custom field values
    $sezione_art = get_post_meta($post_id, 'gruppo_centrale_1_sezione_art', true);
    $sezione_scuole = get_post_meta($post_id, 'gruppo_centrale_1_sezione_scuole', true);

    // Get current terms
    $current_terms = wp_get_object_terms($post_id, 'categoria', array('fields' => 'ids'));
    
    // Handle 'art' taxonomy
    if ($sezione_art === 'si') {
        if (!in_array($art_term_id, $current_terms)) {
            $current_terms[] = $art_term_id;
        }
    } else {
        $current_terms = array_diff($current_terms, array($art_term_id));
    }

    // Handle 'scuole' taxonomy
    if ($sezione_scuole === 'si') {
        if (!in_array($scuole_term_id, $current_terms)) {
            $current_terms[] = $scuole_term_id;
        }
    } else {
        $current_terms = array_diff($current_terms, array($scuole_term_id));
    }

    // Update terms
    wp_set_object_terms($post_id, array_values($current_terms), 'categoria');
}

// Helper function to get term ID by slug (use this to find your term IDs)
function get_categoria_term_id_by_slug($slug) {
    $term = get_term_by('slug', $slug, 'categoria');
    return $term ? $term->term_id : false;
}

// Hook the function to post save actions
add_action('save_post', 'sync_custom_taxonomies_with_fields');
add_action('acf/save_post', 'sync_custom_taxonomies_with_fields'); // Include this if using ACF