<?php

$snippet_name = "show_text_based_on_date";
$version = "<!#FV> 0.0.6 </#FV>";
add_shortcode('film_date_display', 'date_display_function');
function date_display_function($atts) {
    // Parse attributes with default values
    $atts = shortcode_atts(array(
        'yearonly' => false,
		'looptitle' => false
    ), $atts, 'film_date_display');
	
	// yearonly = when date is set, shows only the date followed by a | - useful only for loop item title
	// looptitle = if set, don't show anything on loop item title, even with date set and custom "cappello" set 

    // Static array of Italian month names
    $italian_months = array(
        1 => 'gennaio',
        2 => 'febbraio',
        3 => 'marzo',
        4 => 'aprile',
        5 => 'maggio',
        6 => 'giugno',
        7 => 'luglio',
        8 => 'agosto',
        9 => 'settembre',
        10 => 'ottobre',
        11 => 'novembre',
        12 => 'dicembre'
    );
    
    // Get both custom fields
    $custom_field_date = get_field('gruppo_sinistra_data_film');
    $custom_field_text = get_field('gruppo_sinistra_cappello');
    $cf_mostra_anno = get_field('gruppo_sinistra_mostrare_anno');
	$cf_no_message = get_field('gruppo_sinistra_no_message');
    
    // If text exists and date is empty, show only text
    /*if (!empty($custom_field_text) && empty($custom_field_date)) {
        return '<h2 class="elementor-heading-title elementor-size-default">' . esc_html($custom_field_text) . '</h2>';
    }*/


	// If no message is ticked, shows nothing - useful in conjunction with date to display the film on the top of the list
	if ( $cf_no_message == 1 && empty($custom_field_text)  ) {
		return '<h2 class="elementor-heading-title elementor-size-default"></h2>';
	}
	
	// If no message is ticked, shows nothing - useful in conjunction with date to display the film on the top of the list
	if ( $cf_no_message == 1 && $atts['looptitle'] ) {
		return '<h2 class="elementor-heading-title elementor-size-default"></h2>';
	}
	
	// if cappello exists and looptitle true, doesn't show nothing - useful for loop title
	if ( !empty($custom_field_text) && $atts['looptitle'] ) {
		return '<h2 class="elementor-heading-title elementor-size-default"></h2>';
	}
	
	// If "cappello" exists, show it and avoid displaying date things
    if ( !empty($custom_field_text) && $atts['looptitle'] == false  ) {
        return '<h2 class="elementor-heading-title elementor-size-default">' . esc_html($custom_field_text) . '</h2>';
    }
    
    // Process the date display if it exists
    if (!empty($custom_field_date)) {
        $custom_date = strtotime($custom_field_date);
        $today = time();
        
        // If yearonly is true, return the year ignoring the 90-day limit
        if ($atts['yearonly']) {
            return '<span>' . date('Y', $custom_date) . ' | ' . '</span>';
        }
        
        // Calculate the difference between today and the custom date
        $difference = $today - $custom_date;
        $days_difference = floor($difference / (24*60*60));
        
        // Check if it's been at least 90 days since the custom date
        if ($days_difference >= 90) {
            return '<h2 class="elementor-heading-title elementor-size-default"></h2>';
        }
        
        $custom_date_int = (int)date('Ymd', $custom_date);
        $today_date_int = (int)date('Ymd', $today);
        $day = date('j', $custom_date);
        $month_num = (int)date('n', $custom_date);
        
        // Determine year display based on custom field
        if ($cf_mostra_anno == 1) {
            $year = date('Y', $custom_date);
        } else {
            $year = "";
        }
        
        if ($custom_date_int > $today_date_int) {
            if ($day == 1 || $day == 8 || $day == 11) {
                return sprintf('<h2 class="elementor-heading-title elementor-size-default">Dall\'%d %s %s al cinema</h2>', $day, $italian_months[$month_num], $year);
            } else {
                return sprintf('<h2 class="elementor-heading-title elementor-size-default">Dal %d %s %s al cinema</h2>', $day, $italian_months[$month_num], $year);
            }
        } elseif ($custom_date_int == $today_date_int) {
            return '<h2 class="elementor-heading-title elementor-size-default">Da oggi al cinema</h2>';
        } else {
            return '<h2 class="elementor-heading-title elementor-size-default">Al cinema</h2>';
        }
    }
    
    // If no date and no text, show no text
    return '<h2 class="elementor-heading-title elementor-size-default"></h2>';
}