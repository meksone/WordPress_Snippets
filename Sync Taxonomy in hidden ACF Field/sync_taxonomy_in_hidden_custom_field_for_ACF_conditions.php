<?php
$snippet_name="sync_taxonomy_in_hidden_custom_field_for_ACF_conditions";
$version = "<!#FV> 0.0.1 </#FV>";

/*
 * Sync selected custom categories with an hidden field in the post;
 * the hidden field loads the categories into ACF so it can be
 * used to enable/disable fields group based on conditions
*/

function update_categoria_film_on_save($post_id) {
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check if the post has a 'categoria' taxonomy
    $categories = wp_get_post_terms($post_id, 'categoria');

    // If the post has a 'categoria' taxonomy
    if (!empty($categories) && !is_wp_error($categories)) {
        // Get the IDs of all categories and convert them to strings
        $category_ids = array_map('strval', wp_list_pluck($categories, 'term_id')); // saves as string - not number

        // Update the 'categorie_film' custom field with the category IDs
        update_post_meta($post_id, 'categoria_film', $category_ids);
    }
}
add_action('save_post', 'update_categoria_film_on_save');
add_action('acf/save_post', 'update_categoria_film_on_save', 20);
