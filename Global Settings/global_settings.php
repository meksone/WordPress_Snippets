<?php
$snippet_name = "global_settings";
$version = "<!#FV> 0.0.3 </#FV>";

add_action('init', 'check_user_and_execute_functions');
function check_user_and_execute_functions() {
    // Array of usernames to exclude
    $usernames = array('bim-adm', 'placeholder');
	
	// Options for the functions
	$pluginsToDisable = [
        'admin-color-schemes/admin-color-schemes.php',
        'jamp-notes/jamp.php',
        'advanced-custom-fields-pro/acf.php',
        'advanced-custom-fields/acf.php',
        'acf-extended/acf-extended.php',
        'code-snippets/code-snippets.php',
        'disable-comments/disable-comments.php',
        'wp-media-folder/wp-media-folder.php',
        'redis-cache/redis-cache.php',
        'dynamic-content-for-elementor/dynamic-content-for-elementor.php',
        'jsm-show-user-meta/jsm-show-user-meta.php',
        'jsm-show-post-meta/jsm-show-post-meta.php',
        ];

    // Check if user is logged in
    if ( is_user_logged_in() ) {
        // Get current user
        $current_user = wp_get_current_user();

        // Check if current user's username is in the array
        if ( !in_array( $current_user->user_login, $usernames ) ) {
            // Execute your functions here
            hide_plugins($pluginsToDisable); // hide specific plugins
			add_filter( 'get_user_option_admin_color', 'update_user_option_admin_color', 5 ); // set default color scheme
			remove_action("admin_color_scheme_picker", "admin_color_scheme_picker"); // remove color schemes from user screen
        }
    }
}

/* Disable Plugins */
function hide_plugins($hiddenPlugins) {
    add_filter('all_plugins', function ($plugins) use ($hiddenPlugins) {
        $shouldHide = ! array_key_exists('show_all', $_GET);

        if ($shouldHide) {
            foreach ($hiddenPlugins as $hiddenPlugin) {
                unset($plugins[$hiddenPlugin]);
            }
        }
        return $plugins;
    });
}

/* Set defaultColor Scheme */
function update_user_option_admin_color( $color_scheme ) {
    $color_scheme = '80s-kid';
    return $color_scheme;
}

/* Disable Capital P Dangit */
remove_filter( 'the_title', 'capital_P_dangit', 11 );
remove_filter( 'the_content', 'capital_P_dangit', 11 );
remove_filter( 'comment_text', 'capital_P_dangit', 31 );