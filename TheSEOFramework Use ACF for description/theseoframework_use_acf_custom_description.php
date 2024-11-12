<?php

$snippet_name = "theseoframework_use_acf_custom_description";
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * Function to set a custom description for The SEO Framework plugin based on an ACF field for a specific post type.
 *
 * @param string $description The current description.
 * @param int $post_id The ID of the post.
 *
 * @return string The modified description.
 */
function mk_custom_seo_framework_description( $description, $post_id ) {
    // Check if the post type is the one we want to target
    if ( get_post_type( $post_id ) === 'film' ) {
        // Get the value of the ACF field
        $acf_field_value = get_field( 'gruppo_destra_sinossi', $post_id );

        // If the field value is not empty, use it as the description
        if ( ! empty( $acf_field_value ) ) {
            $description = $acf_field_value;
        }
    }

    return $description;
}

// Add the filter to the `the_seo_framework_description` filter
add_filter( 'the_seo_framework_description', 'mk_custom_seo_framework_description', 10, 2 );
add_filter( 'the_seo_framework_custom_field_description', 'mk_custom_seo_framework_description', 10, 2);
add_filter( 'the_seo_framework_generated_description', 'mk_custom_seo_framework_description', 10, 2);
add_filter( 'the_seo_framework_fetched_description_excerpt', 'mk_custom_seo_framework_description', 10, 2);