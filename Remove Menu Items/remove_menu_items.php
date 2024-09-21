<?php
$version = "<!#FV> 0.0.5 </#FV>";

$user_to_exclude = array('bim-adm');

$menus_to_disable = array(
    (object) array('menuname' => 'WP Options - Main', 'mainmenu' => 'options-general.php', 'exclude' => $user_to_exclude), 																
    (object) array('menuname' => 'WP Options - Reading', 'mainmenu' => 'options-general.php', 'submenu' => 'options-reading.php', 'exclude' => $user_to_exclude), 							
    (object) array('menuname' => 'WP Options - Writing', 'mainmenu' => 'options-general.php', 'submenu' => 'options-writing.php', 'exclude' => $user_to_exclude), 							
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
	(object) array('menuname' => 'Elementor - Main', 'mainmenu' => 'elementor', 'exclude' => $user_to_exclude),																			
	(object) array('menuname' => 'Elementor - Template Library', 'mainmenu' => 'edit.php?post_type=elementor_library', 'exclude' => $user_to_exclude),												
	(object) array('menuname' => 'Elementor - License', 'mainmenu' => 'elementor', 'submenu' => 'elementor-license', 'exclude' => $user_to_exclude), 										
	(object) array('menuname' => 'Elementor - Apps ', 'mainmenu' => 'elementor', 'submenu' => 'elementor-apps', 'exclude' => $user_to_exclude), 											
	(object) array('menuname' => 'Elementor - Role manager ', 'mainmenu' => 'elementor', 'submenu' => 'elementor-role-manager', 'exclude' => $user_to_exclude), 									
	(object) array('menuname' => 'Elementor - Settings ', 'mainmenu' => 'elementor', 'submenu' => 'elementor-settings', 'exclude' => $user_to_exclude), 										
	(object) array('menuname' => 'Elementor - Tools ', 'mainmenu' => 'elementor', 'submenu' => 'elementor-tools', 'exclude' => $user_to_exclude), 										
	(object) array('menuname' => 'Elementor - Info ', 'mainmenu' => 'elementor', 'submenu' => 'elementor-system-info', 'exclude' => $user_to_exclude), 									
	(object) array('menuname' => 'Elementor - Knowledge Base', 'mainmenu' => 'elementor', 'submenu' => 'go_knowledge_base_site', 'exclude' => $user_to_exclude), 									
	(object) array('menuname' => 'Dynamic Content for Elementor - Main', 'mainmenu' => 'dce-features', 'exclude' => $user_to_exclude),    																	
	(object) array('menuname' => 'Dynamic Content for Elementor - License ', 'mainmenu' => 'dce-features', 'submenu' => 'dce-license', 'exclude' => $user_to_exclude),                            				
	(object) array('menuname' => 'Dynamic Content for Elementor - HTML Templates', 'mainmenu' => 'dce-features','submenu' => 'edit.php?post_type=dce_html_template', 'exclude' => $user_to_exclude),    				
	(object) array('menuname' => 'LoginPress - Main', 'mainmenu' => 'loginpress-settings', 'exclude' => $user_to_exclude),                  					  						 	
	(object) array('menuname' => 'LoginPress - License', 'mainmenu' => 'loginpress-settings','submenu' => 'loginpress-license', 'exclude' => $user_to_exclude),               				
	(object) array('menuname' => 'LoginPress - Import/Export', 'mainmenu' => 'loginpress-settings','submenu' => 'loginpress-import-export', 'exclude' => $user_to_exclude),        	 			
	(object) array('menuname' => 'LoginPress - Help', 'mainmenu' => 'loginpress-settings','submenu' => 'loginpress-help', 'exclude' => $user_to_exclude),              			    	
	(object) array('menuname' => 'LoginPress - Addons', 'mainmenu' => 'loginpress-settings','submenu' => 'loginpress-addons', 'exclude' => $user_to_exclude),           				    
	(object) array('menuname' => 'LoginPress - submenu in Themes', 'mainmenu' => 'themes.php','submenu' => 'abw', 'exclude' => $user_to_exclude),            											
	(object) array('menuname' => 'ACF Pro - Main', 'mainmenu' => 'edit.php?post_type=acf-field-group', 'exclude' => $user_to_exclude),                       							
	(object) array('menuname' => 'ACF Pro - Tools', 'mainmenu' => 'edit.php?post_type=acf-field-group','submenu' => 'acf-tools', 'exclude' => $user_to_exclude),         				
	(object) array('menuname' => 'ACF Pro - Updates/License', 'mainmenu' => 'edit.php?post_type=acf-field-group','submenu' => 'acf-settings-updates', 'exclude' => $user_to_exclude),			
	(object) array('menuname' => 'Duplicator - Main', 'mainmenu' => 'duplicator', 'exclude' => $user_to_exclude),    																	
	(object) array('menuname' => 'Duplicator - Informations', 'mainmenu' => 'duplicator','submenu' => 'duplicator-about-us', 'exclude' => $user_to_exclude),										
	(object) array('menuname' => 'Duplicator - Storage', 'mainmenu' => 'duplicator','submenu' => 'duplicator-storage', 'exclude' => $user_to_exclude),										
	(object) array('menuname' => 'Duplicator - Update to Pro', 'mainmenu' => 'duplicator','submenu' => 'https://duplicator.com/lite-upgrade/?utm_medium=admin-menu&utm_content=Upgrade+to+Pro&utm_source=WordPress&utm_campaign=liteplugin', 'exclude' => $user_to_exclude),
	(object) array('menuname' => 'PAFE - Main', 'mainmenu' => 'premium-addons', 'exclude' => $user_to_exclude),    																
	(object) array('menuname' => 'PAFE - License', 'mainmenu' => 'premium-addons','submenu' => 'premium-addons#tab=license', 'exclude' => $user_to_exclude),							
	(object) array('menuname' => 'PAFE - White Label', 'mainmenu' => 'premium-addons','submenu' => 'premium-addons#tab=white-label', 'exclude' => $user_to_exclude),						
	(object) array('menuname' => 'PAFE - System Info', 'mainmenu' => 'premium-addons','submenu' => 'premium-addons#tab=system-info', 'exclude' => $user_to_exclude),						
	(object) array('menuname' => 'PAFE - Version Control', 'mainmenu' => 'premium-addons','submenu' => 'premium-addons#tab=vcontrol', 'exclude' => $user_to_exclude),							
	(object) array('menuname' => 'PAFE - Integrations', 'mainmenu' => 'premium-addons','submenu' => 'premium-addons#tab=integrations', 'exclude' => $user_to_exclude),						
	(object) array('menuname' => 'Options - WP Folder Plugin', 'mainmenu' => 'options-general.php','submenu' => 'option-folder', 'exclude' => $user_to_exclude),									
	(object) array('menuname' => 'Options - Redis Plugin', 'mainmenu' => 'options-general.php','submenu' => 'redis-cache', 'exclude' => $user_to_exclude),									
	(object) array('menuname' => 'Options - Disable Comments Plugin', 'mainmenu' => 'options-general.php','submenu' => 'disable_comments_settings', 'exclude' => $user_to_exclude),						
	(object) array('menuname' => 'Options - Admin Columns Pro', 'mainmenu' => 'options-general.php','submenu' => 'codepress-admin-columns', 'exclude' => $user_to_exclude),						
	(object) array('menuname' => 'Code Snippets - Main', 'mainmenu' => 'snippets', 'exclude' => $user_to_exclude),    																		
	(object) array('menuname' => 'Code Snippets - XXX', 'mainmenu' => 'snippets','submenu' => 'code-snippets-welcome', 'exclude' => $user_to_exclude),										
);


function disable_menu_items($options) {
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
                    remove_submenu_page($option->mainmenu, $option->submenu);
                } else {
                    // Remove the main menu page
                    remove_menu_page($option->mainmenu);
                }
            }
        }
    }
}

add_action('admin_menu', function() use ($menus_to_disable) {
    disable_menu_items($menus_to_disable);
},999);