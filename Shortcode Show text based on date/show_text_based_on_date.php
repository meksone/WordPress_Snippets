<?php

$snippet_name = "show_text_based_on_date";
$version = "<!#FV> 0.0.3 </#FV>";

add_shortcode('film_date_display', 'date_display_function');
function date_display_function($atts) {
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

    // If text exists and date is empty, show only text
    if (!empty($custom_field_text) && empty($custom_field_date)) {
        return '<h2 class="elementor-heading-title elementor-size-default">' . esc_html($custom_field_text) . '</h2>';
    }
    
    // Process the date display if it exists
    if (!empty($custom_field_date)) {
        $custom_date_int = (int)date('Ymd', strtotime($custom_field_date));
        $today_date_int = (int)date('Ymd', time());
        $day = date('j', strtotime($custom_field_date));
        $month_num = (int)date('n', strtotime($custom_field_date));
		if( $cf_mostra_anno == 1 ){
		$year = date('Y', strtotime($custom_field_date));
		} else {
		$year = "";
		};

        if ($custom_date_int > $today_date_int) {
			if ($day == 1 || $day == 8 || $day == 11 ) {
				return sprintf('<h2 class="elementor-heading-title elementor-size-default">Dall\'%d %s %s al cinema</h2>', $day, $italian_months[$month_num],$year);
			}
			else {
				return sprintf('<h2 class="elementor-heading-title elementor-size-default">Dal %d %s %s al cinema</h2>', $day, $italian_months[$month_num],$year);
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