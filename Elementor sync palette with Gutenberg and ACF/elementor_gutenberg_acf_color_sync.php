<?php

$snippet_name = "elementor_gutenberg_acf_color_sync";
$version = "<!#FV> 0.0.1 </#FV>";
/**
 * Sync selected Elementor Kit Colors with Gutenberg Editor and ACF Color Picker
 *
 * reference https://www.wpdiaries.com/gutenberg-color-palette/#replace-colors
 * reference for elementor managing default colors https://github.com/elementor/elementor/issues/12843
 * reference for ACF color picker customization https://www.advancedcustomfields.com/resources/javascript-api/
 **/

function get_elementor_colors() {
    // Check if Elementor is active
    if (!did_action('elementor/loaded')) {
        return new WP_Error('elementor_missing', 'Elementor is not active');
    }

    // Get the active kit ID
    $elementor_kit_id = get_option('elementor_active_kit');
    if (!$elementor_kit_id) {
        return new WP_Error('no_active_kit', 'No active Elementor Kit found');
    }

    // Get the kit settings
    $system_colors = get_post_meta($elementor_kit_id, '_elementor_page_settings');
    if (empty($system_colors) || !is_array($system_colors)) {
        return new WP_Error('no_kit_settings', 'No Elementor Kit settings found');
    }

    // Check if color settings exist
    if (!isset($system_colors['0']['system_colors']) || !isset($system_colors['0']['custom_colors'])) {
        return new WP_Error('no_color_settings', 'No color settings found in Elementor Kit');
    }

    // Extract colors
    $allSystemColors = $system_colors['0']['system_colors'];
    $allCustomColors = $system_colors['0']['custom_colors'];

    // Validate color data structure
    foreach (array_merge($allSystemColors, $allCustomColors) as $color) {
        if (!isset($color['title']) || !isset($color['color']) || !isset($color['_id'])) {
            return new WP_Error('invalid_color_data', 'Invalid color data structure in Elementor Kit');
        }
    }

    // Combine system and custom colors
    return array_merge($allSystemColors, $allCustomColors);
}

function get_fallback_colors() {
    // Default WordPress color palette as fallback
    return [
        [
            'title' => 'Black',
            '_id'   => 'black',
            'color' => '#000000'
        ],
        [
            'title' => 'White',
            '_id'   => 'white',
            'color' => '#ffffff'
        ],
        [
            'title' => 'Blue',
            '_id'   => 'blue',
            'color' => '#0073aa'
        ],
        [
            'title' => 'Grey',
            '_id'   => 'grey',
            'color' => '#767676'
        ]
    ];
}

function mk_sync_elementor_palette_to_gutenberg() {
    $colors = get_elementor_colors();
    
    // If there's an error, use fallback colors and log it
    if (is_wp_error($colors)) {
        error_log('Elementor Color Sync Error: ' . $colors->get_error_message());
        $colors = get_fallback_colors();
    }
    
    // Initialize an empty array to hold the new structure
    $newColorPalette = array();
    
    // Loop through each color
    foreach ($colors as $color) {
        // Build the new array structure for each color
        $newColorPalette[] = array(
            'name' => $color['title'],
            'slug' => $color['_id'],
            'color' => $color['color']
        );
    }
    
    add_theme_support('editor-color-palette', $newColorPalette);
}
add_action('after_setup_theme', 'mk_sync_elementor_palette_to_gutenberg');

function mk_sync_elementor_palette_to_acf() {
    // Only proceed if ACF is active
    if (!class_exists('ACF')) {
        error_log('ACF Color Sync Error: Advanced Custom Fields is not active');
        return;
    }
    
    $colors = get_elementor_colors();
    
    // If there's an error, use fallback colors and log it
    if (is_wp_error($colors)) {
        error_log('Elementor Color Sync Error: ' . $colors->get_error_message());
        $colors = get_fallback_colors();
    }
    
    // Create arrays for colors and their corresponding names
    $color_values = array();
    $color_names = array();
    
    foreach ($colors as $color) {
        $color_values[] = $color['color'];
        $color_names[] = $color['title'];
    }
    
    // Output the JavaScript to customize ACF color picker
    ?>
    <script type="text/javascript">
    (function($) {
        // Store color names for reference
        var colorNames = <?php echo json_encode($color_names); ?>;
        var colorValues = <?php echo json_encode($color_values); ?>;
        
        // Add error handling for missing jQuery
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded - ACF color picker customization failed');
            return;
        }
        
        acf.add_filter('color_picker_args', function(args, $field) {
            try {
                // Add Elementor palette colors
                args.palettes = colorValues;
                
                // Override the iris color picker's _addInputListeners method to add titles
                /*
				args.create = function(event, ui) {
                    try {
                        var iris = $(this).data('iris-border');
                        var palette = $(this).data('picker').palette;
                        
                        // Add titles to the palette swatches
                        palette.find('.iris-palette').each(function(index) {
                            $(this).attr('title', colorNames[index] || '');
                        });
						
                    } catch (e) {
                        console.error('Error initializing color picker:', e);
                    }
                }*/
                
                return args;
            } catch (e) {
                console.error('Error setting up color picker arguments:', e);
                return args;
            }
			
			
        });
        
        // Additional enhancement: Update swatches when color picker is opened
        /*
			acf.addAction('show_field/type=color_picker', function($field) {
            try {
                var $input = $field.find('input.iris-picker');
                setTimeout(function() {
                    try {
                        var palette = $input.closest('.acf-input').find('.iris-palette');
                        palette.each(function(index) {
                            $(this).attr('title', colorNames[index] || '');
                        });
                    } catch (e) {
                        console.error('Error updating color swatches:', e);
                    }
                }, 100);
            } catch (e) {
                console.error('Error in show_field action:', e);
            }
        });
		*/
    })(jQuery);
    </script>
    <?php
}
add_action('acf/input/admin_footer', 'mk_sync_elementor_palette_to_acf');

// Optional: Add admin notice for errors
function mk_elementor_color_sync_admin_notice() {
    $colors = get_elementor_colors();
    if (is_wp_error($colors) && current_user_can('manage_options')) {
        $error_message = $colors->get_error_message();
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo sprintf(
                'Warning: Unable to sync Elementor colors. %s. Using fallback colors instead.',
                esc_html($error_message)
            ); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'mk_elementor_color_sync_admin_notice');