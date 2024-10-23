<?php

$snippet_name = "remove_menu_items";
$version = "<!#FV> 0.0.8 </#FV>";

$user_to_exclude = array('bim-adm');

// Global variable to store removed pages
$removed_pages = array();

$menus_to_disable = array(
    (object) array('menuname' => 'WP Options - Main','mainmenu' => 'options-general.php', 'exclude' => $user_to_exclude),
    (object) array('menuname' => 'WP Options - Reading','mainmenu' => 'options-general.php', 'submenu' => 'options-reading.php', 'exclude' => $user_to_exclude),
    (object) array('menuname' => 'WP Options - Writing','mainmenu' => 'options-general.php', 'submenu' => 'options-writing.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Options - Media', 'mainmenu' => 'options-general.php', 'submenu' => 'options-media.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Options - Permalinks', 'mainmenu' => 'options-general.php', 'submenu' => 'options-permalink.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Tools menu ', 'mainmenu' => 'tools.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Default - Posts', 'mainmenu' => 'edit.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Default - Pages', 'mainmenu' => 'edit.php?post_type=page', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Default - Comments', 'mainmenu' => 'edit-comments.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Themes - Main', 'mainmenu' => 'themes.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Themes - Patterns', 'mainmenu' => 'themes.php', 'submenu' => 'site-editor.php?path=/patterns', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Themes - Editor', 'mainmenu' => 'themes.php', 'submenu' => 'theme-editor.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Plugins - Main', 'mainmenu' => 'plugins.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Plugins - Install', 'mainmenu' => 'plugins.php', 'submenu' => 'plugin-install.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Plugins - Editor', 'mainmenu' => 'plugins.php', 'submenu' => 'plugin-editor.php', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'JAMP Notes', 'mainmenu' => 'edit.php?post_type=jamp_note', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'WP Options - Jamp Notes Plugin', 'mainmenu' => 'options-general.php', 'submenu' => 'jamp_options', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Main', 'mainmenu' => 'admin.php?page=elementor', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Template Library', 'mainmenu' => 'edit.php?post_type=elementor_library', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - License', 'mainmenu' => 'admin.php?page=elementor', 'submenu' => 'elementor-license', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Apps ', 'mainmenu' => 'admin.php?page=elementor', 'submenu' => 'elementor-apps', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Role manager ', 'mainmenu' => 'admin.php?page=elementor', 'submenu' => 'elementor-role-manager', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Settings ', 'mainmenu' => 'admin.php?page=elementor', 'submenu' => 'elementor-settings', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Tools ', 'mainmenu' => 'admin.php?page=elementor', 'submenu' => 'elementor-tools', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Info ', 'mainmenu' => 'admin.php?page=elementor', 'submenu' => 'elementor-system-info', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Elementor - Knowledge Base', 'mainmenu' => 'admin.php?page=elementor', 'submenu' => 'go_knowledge_base_site', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Content for Elementor - Main', 'mainmenu' => 'admin.php?page=dce-features', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Content for Elementor - License ', 'mainmenu' => 'admin.php?page=dce-features', 'submenu' => 'dce-license', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Content for Elementor - HTML Templates', 'mainmenu' => 'admin.php?page=dce-features','submenu' => 'edit.php?post_type=dce_html_template', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Shortcodes - Main', 'mainmenu' => 'admin.php?page=dynamic-shortcodes', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Shortcodes - Settings', 'mainmenu' => 'admin.php?page=dynamic-shortcodes',  'submenu' => 'dynamic-shortcodes', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Shortcodes - Power Shortcodes', 'mainmenu' => 'admin.php?page=dynamic-shortcodes',  'submenu' => 'dynamic-shortcodes-power', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Shortcodes - Getting started', 'mainmenu' => 'admin.php?page=dynamic-shortcodes',  'submenu' => 'dynamic-shortcodes-getting-started', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Shortcodes - Demo Shortcodes', 'mainmenu' => 'admin.php?page=dynamic-shortcodes',  'submenu' => 'dynamic-shortcodes-demo', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Shortcodes - Collection', 'mainmenu' => 'admin.php?page=dynamic-shortcodes',  'submenu' => 'dynamic-shortcodes-collection', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Dynamic Shortcodes - License', 'mainmenu' => 'admin.php?page=dynamic-shortcodes',  'submenu' => 'dynamic-shortcodes-license', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'LoginPress - Main', 'mainmenu' => 'admin.php?page=loginpress-settings', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'LoginPress - License', 'mainmenu' => 'admin.php?page=loginpress-settings','submenu' => 'loginpress-license', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'LoginPress - Import/Export', 'mainmenu' => 'admin.php?page=loginpress-settings','submenu' => 'loginpress-import-export', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'LoginPress - Help', 'mainmenu' => 'admin.php?page=loginpress-settings','submenu' => 'loginpress-help', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'LoginPress - Addons', 'mainmenu' => 'admin.php?page=loginpress-settings','submenu' => 'loginpress-addons', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'LoginPress - submenu in Themes', 'mainmenu' => 'themes.php','submenu' => 'abw', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'ACF Pro - Main', 'mainmenu' => 'edit.php?post_type=acf-field-group', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'ACF Pro - Tools', 'mainmenu' => 'edit.php?post_type=acf-field-group','submenu' => 'acf-tools', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'ACF Pro - Updates/License', 'mainmenu' => 'edit.php?post_type=acf-field-group','submenu' => 'acf-settings-updates', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Duplicator - Main', 'mainmenu' => 'admin.php?page=duplicator', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Duplicator - Informations', 'mainmenu' => 'admin.php?page=duplicator','submenu' => 'duplicator-about-us', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Duplicator - Storage', 'mainmenu' => 'admin.php?page=duplicator','submenu' => 'duplicator-storage', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Duplicator - Update to Pro', 'mainmenu' => 'admin.php?page=duplicator','submenu' => 'https://duplicator.com/lite-upgrade/?utm_medium=admin-menu&utm_content=Upgrade+to+Pro&utm_source=WordPress&utm_campaign=liteplugin', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'PAFE - Main', 'mainmenu' => 'admin.php?page=premium-addons', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'PAFE - License', 'mainmenu' => 'admin.php?page=premium-addons','submenu' => 'premium-addons#tab=license', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'PAFE - White Label', 'mainmenu' => 'admin.php?page=premium-addons','submenu' => 'premium-addons#tab=white-label', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'PAFE - System Info', 'mainmenu' => 'admin.php?page=premium-addons','submenu' => 'premium-addons#tab=system-info', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'PAFE - Version Control', 'mainmenu' => 'admin.php?page=premium-addons','submenu' => 'premium-addons#tab=vcontrol', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'PAFE - Integrations', 'mainmenu' => 'admin.php?page=premium-addons','submenu' => 'premium-addons#tab=integrations', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Options - WP Folder Plugin', 'mainmenu' => 'options-general.php','submenu' => 'option-folder', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Options - Redis Plugin', 'mainmenu' => 'options-general.php','submenu' => 'redis-cache', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Options - Disable Comments Plugin', 'mainmenu' => 'options-general.php','submenu' => 'disable_comments_settings', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Options - Admin Columns Pro', 'mainmenu' => 'options-general.php','submenu' => 'codepress-admin-columns', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Code Snippets - Main', 'mainmenu' => 'admin.php?page=snippets', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Code Snippets - Welcome', 'mainmenu' => 'admin.php?page=snippets','submenu' => 'code-snippets-welcome', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'Steroids for Elementor - Main', 'mainmenu' => 'admin.php?page=steroids_for_elementor', 'exclude' => $user_to_exclude),
);


function disable_menu_items($options) {
    global $removed_pages;
	
	// Clean menu items URL
	$stripFromURL = array( '	edit.php?post_type=',
								'admin.php?page=',
								'site-editor.php?path=',
								'customize.php?return=',
								'themes.php?page=',
								'tools.php?page=',
								'edit-tags.php?taxonomy=',
						);
	
	//echo "-------------------------------------------- clean array: " . $stripFromURL . "<br>";

    // Get the current user
    $current_user = wp_get_current_user();

    // Check if the options array is not empty
    if(!empty($options)) {
        // Loop through the options array
        foreach($options as $option) {
            // Check if the 'exclude' property is set and the current user is not in the exclusion list
            if(isset($option->exclude) && !in_array($current_user->user_login, $option->exclude)) {
                // Check if the submenu is set
                if(isset($option->submenu)) {
                    // Remove the submenu page
                    // Clean menu and submenu string
                    $cleanMenuitem = stripFromArray($option->mainmenu, $stripFromURL);
					$cleanSubMenuitem = stripFromArray($option->submenu, $stripFromURL);
					
					//echo "------------------------------------------- mainmenu page: " . $cleanMenuitem . "<br>";
					//echo "------------------------------------------- submenu page: " . $cleanSubMenuitem . "<br>";
					
                    remove_submenu_page($cleanMenuitem, $cleanSubMenuitem);

                    // Add the submenu page to the removed pages - not cleaned!
                    $removed_pages[] = $option->submenu;
                } else {
                    // Remove the main menu page
                    // Clean menu string
                    $cleanMenuitem = stripFromArray($option->mainmenu, $stripFromURL);
                    remove_menu_page($cleanMenuitem);

                    // Add the main menu page to the removed pages
                    $removed_pages[] = $option->mainmenu;
                }
            }
        }
    }
	/* Block access with 403 error */
	redirect_removed_pages();
}

/* Helper function
 * clean a string using array as input
 * */
function stripFromArray($string, $array) {
    foreach($array as $item) {
        $string = str_replace($item, '', $string);
    }
    return $string;
}

function redirect_removed_pages() {
    global $removed_pages;

    // Get the current page URL
    $current_page_url = $_SERVER['REQUEST_URI'];
	//echo "------------------------------------------- removed page: " . $removed_page;
	//echo "------------------------------------------------------ current page: " . $current_page_url . "<br>";

    // Split the current page URL into its components
    $current_page_url_components = explode('/', trim($current_page_url, '/'));

    // Check if the current page URL contains any of the removed pages
    foreach($removed_pages as $removed_page) {
        if(in_array($removed_page, $current_page_url_components)) {
			//echo "------------------------------------------- removed page: " . $removed_page . "<br>";
			//echo "------------------------------------------------------ current page: " . $current_page_url . "<br>";
            // Return a 403 error
            status_header( 403 );
            wp_die( 'Access denied - you can\'t reach this page' );
        }
    }
}

add_action('admin_menu', function() use ($menus_to_disable) {
    disable_menu_items($menus_to_disable);
},999);

// Hook into the 'template_redirect' action
//add_action('template_redirect', 'redirect_removed_pages');


/*
 * Print WP Dashboard Menus and Submenus
 * Output the array on Dashboard main page
 * reference https://wordpress.stackexchange.com/questions/148973/how-can-i-get-an-array-list-of-all-current-wordpress-admin-menu-items
 * */
if(false){
	if (!function_exists('debug_admin_menus')):
	function debug_admin_menus() {
		global $submenu, $menu, $pagenow;
		if ( current_user_can('manage_options') ) { // Only for admins
			if( $pagenow == 'index.php' ) {  // Print in dashboard
				echo '<pre>'; print_r( $menu ); echo '</pre>'; // TOP LEVEL MENUS
				echo '<pre>'; print_r( $submenu ); echo '</pre>'; // SUBMENUS
			}
		}
	}
	add_action( 'admin_notices', 'debug_admin_menus' );
	endif;
	};