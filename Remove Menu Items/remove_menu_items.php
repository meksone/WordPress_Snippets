<?php
$version = "<!#FV> 0.0.2 </#FV>";

/* reference https://ilikekillnerds.com/2021/09/how-to-remove-wordpress-menu-items-including-those-created-by-plugins/ 
 * roles https://wordpress.org/documentation/article/roles-and-capabilities/
 * */

function mk_post_remove_all() {
	if( current_user_can('edit_posts') ) { // for all editors
	remove_menu_page('edit.php');                                                   // Posts
    remove_menu_page('edit.php?post_type=page');                                    // Pages
	remove_menu_page('edit-comments.php');                                          // Comments
	remove_menu_page('tools.php');                                                  // Tools menu
    remove_menu_page('edit.php?post_type=wpf_post_it');                             // Plugin - Dashboard Notes
	remove_menu_page('edit.php?post_type=elementor_library');                       // Elementor Template Library
	remove_submenu_page('elementor', 'elementor-license');                          // Elementor - License
	remove_submenu_page('elementor', 'elementor-apps');                             // Elementor - Apps
	remove_submenu_page('elementor', 'elementor-role-manager');                     // Elementor - Role manager
    remove_submenu_page('elementor', 'elementor-settings');                         // Elementor - Settings
	remove_submenu_page('elementor', 'elementor-tools');                            // Elementor - Tools
	remove_submenu_page('elementor', 'elementor-system-info');                      // Elementor - Info
	remove_submenu_page('elementor', 'go_knowledge_base_site');                     // Elementor - Knowledge Base
	remove_submenu_page('dce-features', 'dce-license');                             // Dynamic Content for Elementor - License 
	remove_submenu_page('dce-features', 'edit.php?post_type=dce_html_template');    // Dynamic Content for Elementor - HTML Templates
	remove_submenu_page('loginpress-settings', 'loginpress-license');               // LoginPress - License
	remove_submenu_page('loginpress-settings', 'loginpress-import-export');         // LoginPress - Import/Export
	remove_submenu_page('loginpress-settings', 'loginpress-help');                  // LoginPress - Help
	remove_submenu_page('loginpress-settings', 'loginpress-addons');                // LoginPress - Addons
	remove_submenu_page('themes.php', 'site-editor.php?path=/patterns');            // Themes - Patterns
}
}

/*
 * Print the entire menu inside WP-Admin to check menu items
 * 
add_action( 'admin_init', 'get_main_menu_items' );
function get_main_menu_items() {
    echo '<pre>' . print_r( $GLOBALS[ 'menu' ], TRUE) . '</pre>';
}
*/

add_action('admin_menu', 'mk_post_remove_all', 999);