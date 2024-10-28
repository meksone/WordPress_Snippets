<?php
$snippet_name="check_fields_and_taxonomies_and_show_warning";
$version = "<!#FV> 0.0.2 </#FV>";

/*
 * Check ACF field group, and show warning if selected but got empty values;
 * Check also if the corresponding taxonomy is selected and show warning;
 * Check if taxonomy is selected but section isn't selected;
*/


// Add the validation function to admin_notices hook
add_action('admin_notices', 'validate_films_custom_fields');

function validate_films_custom_fields() {
    // Check if we're on the films list page in admin
    $screen = get_current_screen();
    if ($screen->base !== 'edit' || $screen->post_type !== 'film') {
        return;
    }
	
	  // Check if the user has dismissed the warning
    $user_id = get_current_user_id();
    $is_dismissed = get_user_meta($user_id, 'film_validation_warning_dismissed', true);
    
    if ($is_dismissed === 'yes') {
        return;
    }
	

    // Get the field group configurations
    // Select your groups to check
    $art_group = acf_get_field('gruppo_art');
    $scuole_group = acf_get_field('gruppo_scuole');

    // Query all published films
    $args = array(
        'post_type' => 'film',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $films = get_posts($args);
    $validation_messages = array();

    foreach ($films as $film) {
        $has_empty_fields = false;
        $empty_fields = array();
        
        // Get taxonomy terms
        $has_categoria = has_term('', 'categoria', $film->ID);
        $has_art_category = has_term('art', 'categoria', $film->ID);
        $has_scuole_category = has_term('scuole', 'categoria', $film->ID);
        
        // Get group values
        $art_section = get_field('gruppo_centrale_1_sezione_art', $film->ID);
        $scuole_section = get_field('gruppo_centrale_1_sezione_scuole', $film->ID);

        // Check sections and categories
        $art_inactive = $art_section === 'no' || empty($art_section);
        $scuole_inactive = $scuole_section === 'no' || empty($scuole_section);

        // Check for mismatches between sections and categories
        if (!$art_inactive && !$has_art_category) {
            $has_empty_fields = true;
            $empty_fields[] = "Sezione Art attiva ma categoria Art non selezionata";
        }

        if (!$scuole_inactive && !$has_scuole_category) {
            $has_empty_fields = true;
            $empty_fields[] = "Sezione Scuole attiva ma categoria Scuole non selezionata";
        }

        // Only check for category-section mismatches if there are categories selected
        if ($has_categoria) {
            // Validate Art section if art category is selected
            if ($has_art_category && $art_inactive) {
                $has_empty_fields = true;
                $empty_fields[] = "Categoria Art selezionata ma sezione Art non attiva";
            }
            
            // Validate Scuole section if scuole category is selected
            if ($has_scuole_category && $scuole_inactive) {
                $has_empty_fields = true;
                $empty_fields[] = "Categoria Scuole selezionata ma sezione Scuole non attiva";
            }
        }

        // Check gruppo_centrale_1_sezione_art fields if active
        if ($art_section === 'si') {
            $art_values = get_field('gruppo_art', $film->ID);
            if (is_array($art_values) && isset($art_group['sub_fields'])) {
                foreach ($art_group['sub_fields'] as $sub_field) {
                    if (isset($art_values[$sub_field['name']]) && empty($art_values[$sub_field['name']])) {
                        $has_empty_fields = true;
                        $empty_fields[] = "Art: " . $sub_field['label'];
                    }
                }
            }
        }

        // Check gruppo_centrale_1_sezione_scuole fields if active
        if ($scuole_section === 'si') {
            $scuole_values = get_field('gruppo_scuole', $film->ID);
            if (is_array($scuole_values) && isset($scuole_group['sub_fields'])) {
                foreach ($scuole_group['sub_fields'] as $sub_field) {
                    if (isset($scuole_values[$sub_field['name']]) && empty($scuole_values[$sub_field['name']])) {
                        $has_empty_fields = true;
                        $empty_fields[] = "Scuole: " . $sub_field['label'];
                    }
                }
            }
        }

        // If this film has empty required fields, add it to the messages
        if ($has_empty_fields) {
            $edit_link = get_edit_post_link($film->ID);
            $missing_fields_html = array_map(function($field) {
				
				// Add special class for section/category mismatch messages
                  if (strpos($field, "Sezione Art") === 0 || strpos($field, "Categoria Art" ) === 0 || strpos($field, "Art") === 0 ) {
                    return sprintf('<li class="mk-missing-item mk-miss-art">%s</li>', esc_html($field));
                }
				  if (strpos($field, "Categoria Scuole") === 0 || strpos($field, "Sezione Scuole") === 0 || strpos($field, "Scuole") === 0) {
                    return sprintf('<li class="mk-missing-item mk-miss-scuole">%s</li>', esc_html($field));
                }
				/*
				  if (strpos($field, "Art") === 0) {
                    return sprintf('<li class="mk-missing-item mk-miss-art">%s</li>', esc_html($field));
                }
				*/
				
                return sprintf('<li class="mk-missing-item">%s</li>', esc_html($field));
            }, $empty_fields);
            
            $validation_messages[] = sprintf(
                '<p>⚠️ Il film <strong><a href="%s">%s</a></strong> ha i seguenti dati mancanti:</p><ul class="missing-fields-list">%s</ul>',
                esc_url($edit_link),
                esc_html($film->post_title),
                implode('', $missing_fields_html)
            );
        }
    }

    // If we have any validation messages, display them with dismiss button
    if (!empty($validation_messages)) {
        echo '<div class="notice notice-warning is-dismissible film-validation-notice">';
        echo '<p><strong>⚠️ I seguenti film richiedono attenzione:</strong></p>';
        echo implode('<hr style="margin: 10px 0;">', $validation_messages);
        echo '<p><button class="button dismiss-film-validation" data-nonce="' . wp_create_nonce('dismiss_film_validation') . '">Non mostrare più questo avviso</button></p>';
        echo '</div>';
    }
}

// Add re-enable button to the films list page
add_action('admin_notices', 'show_reenable_validation_button');
function show_reenable_validation_button() {
    $screen = get_current_screen();
    if ($screen->base !== 'edit' || $screen->post_type !== 'film') {
        return;
    }

    $user_id = get_current_user_id();
    $is_dismissed = get_user_meta($user_id, 'film_validation_warning_dismissed', true);

    if ($is_dismissed === 'yes') {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><button class="button reenable-film-validation" data-nonce="' . wp_create_nonce('reenable_film_validation') . '">Riattiva gli avvisi di validazione dei film</button></p>';
        echo '</div>';
    }
}

// Add a custom column to the films list table
add_filter('manage_film_posts_columns', 'add_film_validation_column');
function add_film_validation_column($columns) {
    $columns['mk_validate_post'] = 'Status Art/Scuole';
    return $columns;
}

add_action('manage_film_posts_custom_column', 'display_film_validation_status', 10, 2);
function display_film_validation_status($column, $post_id) {
    if ($column !== 'mk_validate_post') {
        return;
    }

    $has_empty_fields = false;
    
    // Get taxonomy terms
    $has_categoria = has_term('', 'categoria', $post_id);
    $has_art_category = has_term('art', 'categoria', $post_id);
    $has_scuole_category = has_term('scuole', 'categoria', $post_id);
    
    // Get group values
    $art_section = get_field('gruppo_centrale_1_sezione_art', $post_id);
    $scuole_section = get_field('gruppo_centrale_1_sezione_scuole', $post_id);

    // Check sections and categories
    $art_inactive = $art_section === 'no' || empty($art_section);
    $scuole_inactive = $scuole_section === 'no' || empty($scuole_section);

    // Check for active sections without corresponding categories
    if (!$art_inactive && !$has_art_category) {
        $has_empty_fields = true;
    }

    if (!$scuole_inactive && !$has_scuole_category) {
        $has_empty_fields = true;
    }

    // Only check for category-section mismatches if there are categories selected
    if ($has_categoria) {
        // Check category and section mismatches
        if (($has_art_category && $art_inactive) || 
            ($has_scuole_category && $scuole_inactive)) {
            $has_empty_fields = true;
        }
    }
    
    // Get the field group configurations
    $art_group = acf_get_field('gruppo_art');
    $scuole_group = acf_get_field('gruppo_scuole');

    // Check gruppo_centrale_1_sezione_art fields if active
    if ($art_section === 'si') {
        $art_values = get_field('gruppo_art', $post_id);
        if (is_array($art_values) && isset($art_group['sub_fields'])) {
            foreach ($art_group['sub_fields'] as $sub_field) {
                if (isset($art_values[$sub_field['name']]) && empty($art_values[$sub_field['name']])) {
                    $has_empty_fields = true;
                    break;
                }
            }
        }
    }

    // Check gruppo_centrale_1_sezione_scuole fields if active
    if ($scuole_section === 'si') {
        $scuole_values = get_field('gruppo_scuole', $post_id);
        if (is_array($scuole_values) && isset($scuole_group['sub_fields'])) {
            foreach ($scuole_group['sub_fields'] as $sub_field) {
                if (isset($scuole_values[$sub_field['name']]) && empty($scuole_values[$sub_field['name']])) {
                    $has_empty_fields = true;
                    break;
                }
            }
        }
    }

    if ($has_empty_fields) {
        echo '<span style="color: #d63638;">⚠️ Attenzione</span>';
    } else {
        echo '<span style="color: #00a32a;">✓ Ok</span>';
    }
}

// Add JavaScript to handle dismiss/re-enable actions
add_action('admin_footer', 'add_validation_notice_scripts');
function add_validation_notice_scripts() {
    $screen = get_current_screen();
    if ($screen->base !== 'edit' || $screen->post_type !== 'film') {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Handle dismiss button click
        $('.dismiss-film-validation').on('click', function(e) {
            e.preventDefault();
            var nonce = $(this).data('nonce');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'dismiss_film_validation',
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.film-validation-notice').fadeOut();
                        location.reload();
                    }
                }
            });
        });

        // Handle re-enable button click
        $('.reenable-film-validation').on('click', function(e) {
            e.preventDefault();
            var nonce = $(this).data('nonce');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'reenable_film_validation',
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        });
    });
    </script>
    <?php
}

// Add AJAX handlers for dismiss/re-enable actions
add_action('wp_ajax_dismiss_film_validation', 'dismiss_film_validation_handler');
function dismiss_film_validation_handler() {
    check_ajax_referer('dismiss_film_validation', 'nonce');
    
    $user_id = get_current_user_id();
    update_user_meta($user_id, 'film_validation_warning_dismissed', 'yes');
    
    wp_send_json_success();
}

add_action('wp_ajax_reenable_film_validation', 'reenable_film_validation_handler');
function reenable_film_validation_handler() {
    check_ajax_referer('reenable_film_validation', 'nonce');
    
    $user_id = get_current_user_id();
    delete_user_meta($user_id, 'film_validation_warning_dismissed');
    
    wp_send_json_success();
}


// Update the CSS to include styles for the new buttons
add_action('admin_head', 'add_missing_fields_styles');
function add_missing_fields_styles() {
    echo '<style>
        .mk-missing-item {
            background-color: #DEDEDE;
            border-radius: 4px;
            padding: 3px;
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .mk-missing-item.mk-miss-category {
            background-color: #f1bce2;
        }
        
        .mk-missing-item.mk-miss-section {
            background-color: #ffc3c3;
        }
        
        .mk-missing-item.mk-miss-art {
            background-color: #f9cca0;
        }
        
        .mk-missing-item.mk-miss-scuole {
            background-color: #a2e7e7;
        }
        
        .missing-fields-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .dismiss-film-validation,
        .reenable-film-validation {
            margin: 10px 0;
        }
    </style>';
}