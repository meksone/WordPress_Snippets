<?php

$snippet_name = "gutenberg_sync_palette_with_elementor";
$version = "<!#FV> 0.0.1 </#FV>";

/**
 * Sync selected Elementor Kit Colors with Gutenberg Editor
 *
 * reference https://www.wpdiaries.com/gutenberg-color-palette/#replace-colors
 * reference for elementor managing default colors https://github.com/elementor/elementor/issues/12843
 **/
function mk_sync_elementor_palette_to_gutenberg() {
	
	/* Get selected Elementor Kit and style data */
	$elementor_kit_id = get_option( 'elementor_active_kit' );
	$system_colors = get_post_meta($elementor_kit_id, '_elementor_page_settings');

	// Extract only the system colors
	$allSystemColors = $system_colors['0']['system_colors'];
	$allCustomColors = $system_colors['0']['custom_colors'];
	

	// Initialize an empty array to hold the new structure
		$newColorPalette = array();

		// Loop through each color in system_colors
		foreach ($allSystemColors as $color) {
		  // Build the new array structure for each color
		  $newColorPalette[] = array(
			'name' => $color['title'], // Use 'title' for 'name'
			'slug' => $color['_id'],    // Use '_id' for 'slug'
			'color' => $color['color']  // Use 'color' for 'color'
		  );
			}
	
		// Loop through each color in custom colors
		foreach ($allCustomColors as $color) {
		  // Build the new array structure for each color
		  $newColorPalette[] = array(
			'name' => $color['title'], // Use 'title' for 'name'
			'slug' => $color['_id'],    // Use '_id' for 'slug'
			'color' => $color['color']  // Use 'color' for 'color'
		  );
			}
	
    add_theme_support( 'editor-color-palette', $newColorPalette);
}
add_action( 'after_setup_theme', 'mk_sync_elementor_palette_to_gutenberg' );
