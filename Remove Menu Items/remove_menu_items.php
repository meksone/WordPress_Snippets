<?php
$version = "<!#FV> 0.0.4 </#FV>";

/* reference https://ilikekillnerds.com/2021/09/how-to-remove-wordpress-menu-items-including-those-created-by-plugins/ 
 * roles https://wordpress.org/documentation/article/roles-and-capabilities/
 * */

function mk_post_remove_all() {
	if( current_user_can('edit_posts') ) { // for all editors
	
	/* WordPress Options */
	if(false){
	remove_menu_page('options-general.php');    										// Options - Main
	remove_submenu_page('options-general.php', 'options-reading.php');					// Options - Reading
	remove_submenu_page('options-general.php', 'options-writing.php');					// Options - Writing
	remove_submenu_page('options-general.php', 'options-media.php');					// Options - Media
	remove_submenu_page('options-general.php', 'options-permalink.php');				// Options - Permalinks
	};

	/* WordPress Tools */
	if(false){
	remove_menu_page('tools.php');                                                  	// Tools menu
	};
	
	/* WordPress Default */
	if(false){
	remove_menu_page('edit.php');                                                   	// Posts
    remove_menu_page('edit.php?post_type=page');                                    	// Pages
	remove_menu_page('edit-comments.php');                                          	// Comments
	};
	
	/* WordPress Themes */
	if(false){
	remove_menu_page('themes.php');                                                  	// Themes - Main
	remove_submenu_page('themes.php', 'site-editor.php?path=/patterns');            	// Themes - Patterns
	remove_submenu_page('themes.php', 'theme-editor.php');          				  	// Themes - Editor
	};
	
	/* WordPress Plugins */
	if(false){
	remove_menu_page('plugins.php');                                                	// Plugins - Main
	remove_submenu_page('plugins.php', 'plugin-install.php');            				// Plugins - Install
	remove_submenu_page('plugins.php', 'plugin-editor.php');          				  	// Plugins - Editor
	};
	
    
	/* JAMP Notes */
	if(false){
	remove_menu_page('edit.php?post_type=jamp_note');                             		// JAMP Notes
	remove_submenu_page('options-general.php', 'jamp_options');							// WP Options - Jamp Notes Plugin
	};
	
	/* Elementor */
	if(false){
	remove_menu_page('elementor');                       								// Elementor - Main
	remove_menu_page('edit.php?post_type=elementor_library');                       	// Elementor - Template Library
	remove_submenu_page('elementor', 'elementor-license');                          	// Elementor - License
	remove_submenu_page('elementor', 'elementor-apps');                             	// Elementor - Apps
	remove_submenu_page('elementor', 'elementor-role-manager');                     	// Elementor - Role manager
    remove_submenu_page('elementor', 'elementor-settings');                         	// Elementor - Settings
	remove_submenu_page('elementor', 'elementor-tools');                            	// Elementor - Tools
	remove_submenu_page('elementor', 'elementor-system-info');                      	// Elementor - Info
	remove_submenu_page('elementor', 'go_knowledge_base_site');                     	// Elementor - Knowledge Base
	};
	
	/* Dynamic Content for Elementor */
	if(false){
	remove_menu_page('dce-features');    												// Dynamic Content for Elementor - Main
	remove_submenu_page('dce-features', 'dce-license');                             	// Dynamic Content for Elementor - License 
	remove_submenu_page('dce-features', 'edit.php?post_type=dce_html_template');    	// Dynamic Content for Elementor - HTML Templates
	};
	
	/* LoginPress */
	if(false){
	remove_menu_page('loginpress-settings');                    					   	// LoginPress - Main
	remove_submenu_page('loginpress-settings', 'loginpress-license');               	// LoginPress - License
	remove_submenu_page('loginpress-settings', 'loginpress-import-export');         	// LoginPress - Import/Export
	remove_submenu_page('loginpress-settings', 'loginpress-help');                  	// LoginPress - Help
	remove_submenu_page('loginpress-settings', 'loginpress-addons');                	// LoginPress - Addons
	remove_submenu_page('themes.php', 'abw');            								// LoginPress - submenu in Themes
	};
	
	/* ACF Pro */
	if(false){
	remove_menu_page('edit.php?post_type=acf-field-group');                         	// ACF Pro - Main
	remove_submenu_page('edit.php?post_type=acf-field-group', 'acf-tools');         	// ACF Pro - Tools
	remove_submenu_page('edit.php?post_type=acf-field-group', 'acf-settings-updates');	// ACF Pro - Updates/License
	};
		
	/* Duplicator*/
	if(false){
	remove_menu_page('duplicator');    													// Duplicator - Main
	remove_submenu_page('duplicator', 'duplicator-about-us');							// Duplicator - Informations
	remove_submenu_page('duplicator', 'duplicator-storage');							// Duplicator - Storage
	remove_submenu_page('duplicator', 'https://duplicator.com/lite-upgrade/?utm_medium=admin-menu&utm_content=Upgrade+to+Pro&utm_source=WordPress&utm_campaign=liteplugin');
	};

	/* Premium Addons for Elementor */
	if(false){
	remove_menu_page('premium-addons');    												// PAFE - Main
	remove_submenu_page('premium-addons', 'premium-addons#tab=license');				// PAFE - License
	remove_submenu_page('premium-addons', 'premium-addons#tab=white-label');			// PAFE - White Label
	remove_submenu_page('premium-addons', 'premium-addons#tab=system-info');			// PAFE - System Info
	remove_submenu_page('premium-addons', 'premium-addons#tab=vcontrol');				// PAFE - Version Control
	remove_submenu_page('premium-addons', 'premium-addons#tab=integrations');			// PAFE - Integrations
	};

	/* WP Media Folder */
	if(false){
	remove_submenu_page('options-general.php', 'option-folder');						// Options - WP Folder Plugin
	};

	/* WP Redis */
	if(false){
	remove_submenu_page('options-general.php', 'redis-cache');							// Options - Redis Plugin
	};

	/* Disable Comments */
	if(false){
	remove_submenu_page('options-general.php', 'disable_comments_settings');			// Options - Disable Comments Plugin
	};	

	/* Admin Columns Pro */
	if(false){
	remove_submenu_page('options-general.php', 'codepress-admin-columns');				// Options - Admin Columns Pro
	};	
	
	/* Code Snippets */
	if(true){
	//remove_menu_page('snippets');    													// Code Snippets - Main
	remove_submenu_page('snippets', 'code-snippets-welcome');							// Code Snippets - XXX
	};	
		

	/* XXX */
	if(false){
	remove_menu_page('XXX');    														// XXX - Main
	remove_submenu_page('XXX', 'XXX');													// XXX - XXX
	};	
	
	
	
}
}

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

add_action('admin_menu', 'mk_post_remove_all', 999);