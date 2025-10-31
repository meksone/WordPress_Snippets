<?php
/**
 * Plugin Name: MK Plugin Organizer
 * Description: Organizes plugins into folders on the plugins.php page.
 * Version: 1.13
 * Author: MK (via Gemini)
 * Text Domain: mk-plugin-organizer
 */

$snippet_name = "mk_plugin_organizer";
$version = "<!#FV> 1.13.0 </#FV>";

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main initialization function.
 */
function mkpo_init() {
    
    // 1. Load Textdomain
    load_plugin_textdomain( 'mk-plugin-organizer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    // 2. Add Settings Page
    add_action( 'admin_menu', 'mkpo_add_settings_page' );

    // 3. Register Settings and Handle Actions (Import/Export/Delete/Update Folders)
    add_action( 'admin_init', 'mkpo_register_settings_and_actions' );

    // 4. Enqueue scripts and styles
    add_action( 'admin_enqueue_scripts', 'mkpo_enqueue_assets' );

    // 5. Add custom CSS to head (only on plugins.php)
    add_action( 'admin_head-plugins.php', 'mkpo_print_css' );

    // 6. Add custom JS to footer (plugins.php)
    add_action( 'admin_footer-plugins.php', 'mkpo_print_js' );
    
    // 7. Add custom JS to footer (Settings Page)
    add_action( 'admin_footer-settings_page_mk-plugin-organizer', 'mkpo_print_settings_js' );

    // 8. Add the folder manager UI above the plugin list
    add_action( 'pre_current_active_plugins', 'mkpo_render_folder_ui' );

    // 9. Add the new "Folder" column
    add_filter( 'manage_plugins_columns', 'mkpo_add_folder_column' );

    // 10. Render content for the "Folder" column
    add_action( 'manage_plugins_custom_column', 'mkpo_render_folder_column', 10, 2 );

    // 11. Add folder filter links
    add_filter( 'views_plugins', 'mkpo_add_folder_views' );

    // 12. Filter the plugin list based on the selected folder
    add_filter( 'all_plugins', 'mkpo_filter_plugins_by_folder' );
    
    // 13. Handle Default Folder Redirect
    add_action( 'load-plugins.php', 'mkpo_redirect_to_default_folder' );

    // 14. AJAX Handlers
    add_action( 'wp_ajax_mkpo_create_folder', 'mkpo_ajax_create_folder' );
    add_action( 'wp_ajax_mkpo_delete_folder', 'mkpo_ajax_delete_folder' );
    add_action( 'wp_ajax_mkpo_assign_plugin', 'mkpo_ajax_assign_plugin' );
    add_action( 'wp_ajax_mkpo_unassign_plugin', 'mkpo_ajax_unassign_plugin' );
}
add_action( 'plugins_loaded', 'mkpo_init' );

// =========================================================================
// 1. Helper Functions (Unchanged from v1.12)
// =========================================================================

/**
 * Get all plugin organizer data from the database.
 */
function mkpo_get_data() {
    $defaults = [
        'folders'     => [], 
        'assignments' => [], 
    ];
    return get_option( 'mkpo_data', $defaults );
}

/**
 * Get just the folders.
 */
function mkpo_get_folders() {
    $data = mkpo_get_data();
    return isset($data['folders']) ? $data['folders'] : [];
}

/**
 * Get just the assignments.
 */
function mkpo_get_assignments() {
    $data = mkpo_get_data();
    return isset($data['assignments']) ? $data['assignments'] : [];
}

/**
 * Get the plugin's meta-settings.
 */
function mkpo_get_settings() {
    $defaults = [
        'debug_mode'     => 0,
        'default_folder' => '',
    ];
    return get_option( 'mkpo_settings', $defaults );
}

/**
 * Helper to determine if text on a colored background should be light or dark.
 */
function mkpo_get_text_color($hex) {
    if (empty($hex)) return '#000000';
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (strlen($hex) !== 6) {
        return '#000000';
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    return $luminance > 0.5 ? '#000000' : '#FFFFFF';
}

/**
 * Helper to get background and text color with fallbacks for old data.
 */
function mkpo_get_folder_colors( $folder ) {
    $bg_color = '#e0e0e0'; // Default BG
    
    if ( ! empty( $folder['bg_color'] ) ) {
        $bg_color = $folder['bg_color'];
    } elseif ( ! empty( $folder['color'] ) ) {
        $bg_color = $folder['color']; // Fallback to old 'color' field
    }
    
    $text_color = ! empty( $folder['text_color'] )
        ? $folder['text_color']
        : mkpo_get_text_color( $bg_color ); // Fallback to auto-detect
        
    return [
        'bg'   => $bg_color,
        'text' => $text_color,
    ];
}


// =========================================================================
// 2. Settings Page & Actions (Unchanged from v1.12)
// =========================================================================

/**
 * Register settings and handle form actions (Import/Export/Delete/Update Folders).
 */
function mkpo_register_settings_and_actions() {
    
    // --- 1. Register Main Settings ---
    register_setting(
        'mkpo_options_group',       // Option group
        'mkpo_settings',            // Option name
        'mkpo_sanitize_settings'    // Sanitize callback
    );

    add_settings_section(
        'mkpo_main_section',        // ID
        __( 'Main Settings', 'mk-plugin-organizer' ), // Title
        null,                       // Callback
        'mk-plugin-organizer'       // Page
    );

    add_settings_field(
        'mkpo_debug_mode',          // ID
        __( 'Debug Mode', 'mk-plugin-organizer' ), // Title
        'mkpo_field_debug_mode_cb', // Callback
        'mk-plugin-organizer',      // Page
        'mkpo_main_section'         // Section
    );
    
    add_settings_field(
        'mkpo_default_folder',          // ID
        __( 'Default Folder View', 'mk-plugin-organizer' ), // Title
        'mkpo_field_default_folder_cb', // Callback
        'mk-plugin-organizer',      // Page
        'mkpo_main_section'         // Section
    );

    // --- 2. Handle POST Actions ---
    if ( ! isset( $_POST['mkpo_action'] ) || ! isset( $_POST['_wpnonce'] ) ) {
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $action = sanitize_text_field( $_POST['mkpo_action'] );

    // --- Handle UPDATE FOLDERS ---
    if ( $action === 'update_folders' && wp_verify_nonce( $_POST['_wpnonce'], 'mkpo_update_folders_action' ) ) {
        mkpo_handle_update_folders();
        wp_redirect( admin_url('options-general.php?page=mk-plugin-organizer&folders_updated=true') );
        exit;
    }

    // --- Handle EXPORT ---
    if ( $action === 'export_settings' && wp_verify_nonce( $_POST['_wpnonce'], 'mkpo_export_action' ) ) {
        $export_data = [
            'data'     => mkpo_get_data(),
            'settings' => mkpo_get_settings(),
        ];
        
        $json_data = wp_json_encode( $export_data, JSON_PRETTY_PRINT );
        $filename  = 'mk-plugin-organizer-export-' . date('Y-m-d') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $json_data;
        exit;
    }

    // --- Handle IMPORT ---
    if ( $action === 'import_settings' && wp_verify_nonce( $_POST['_wpnonce'], 'mkpo_import_action' ) ) {
        if ( ! isset( $_FILES['mkpo_import_file'] ) || $_FILES['mkpo_import_file']['error'] !== UPLOAD_ERR_OK ) {
            wp_redirect( admin_url('options-general.php?page=mk-plugin-organizer&import_error=1') );
            exit;
        }

        if ( pathinfo( $_FILES['mkpo_import_file']['name'], PATHINFO_EXTENSION ) !== 'json' ) {
            wp_redirect( admin_url('options-general.php?page=mk-plugin-organizer&import_error=2') );
            exit;
        }

        $content = file_get_contents( $_FILES['mkpo_import_file']['tmp_name'] );
        $import_data = json_decode( $content, true );

        if ( empty( $import_data ) || ! isset( $import_data['data'] ) || ! isset( $import_data['settings'] ) ) {
            wp_redirect( admin_url('options-general.php?page=mk-plugin-organizer&import_error=3') );
            exit;
        }

        // Data looks valid, save it
        update_option( 'mkpo_data', $import_data['data'] );
        update_option( 'mkpo_settings', $import_data['settings'] );

        wp_redirect( admin_url('options-general.php?page=mk-plugin-organizer&imported=true') );
        exit;
    }

    // --- Handle DELETE ALL ---
    if ( $action === 'delete_all' && wp_verify_nonce( $_POST['_wpnonce'], 'mkpo_delete_all_action' ) ) {
        delete_option( 'mkpo_data' );
        delete_option( 'mkpo_settings' );
        
        wp_redirect( admin_url('options-general.php?page=mk-plugin-organizer&deleted=true') );
        exit;
    }
}

/**
 * Handle the saving, updating, and deleting of folders from the settings page.
 */
function mkpo_handle_update_folders() {
    if ( empty( $_POST['folders'] ) || ! is_array( $_POST['folders'] ) ) {
        return;
    }
    
    $data = mkpo_get_data();
    $posted_folders = $_POST['folders'];
    
    $updated_folders = [];
    $folders_to_delete = [];

    // Loop over posted data, sanitize, and sort into 'update' or 'delete'
    foreach ( $posted_folders as $id => $folder_data ) {
        $id = sanitize_text_field( $id );
        
        // Check if delete box is checked
        if ( isset( $folder_data['delete'] ) && $folder_data['delete'] == '1' ) {
            $folders_to_delete[] = $id;
            continue; // Skip to next folder
        }
        
        // If not deleting, sanitize and update
        $updated_folders[ $id ] = [
            'name'       => sanitize_text_field( $folder_data['name'] ),
            'bg_color'   => sanitize_hex_color( $folder_data['bg_color'] ),
            'text_color' => sanitize_hex_color( $folder_data['text_color'] ),
        ];
    }
    
    // Process deletions
    if ( ! empty( $folders_to_delete ) ) {
        $settings = mkpo_get_settings();
        $default_folder = $settings['default_folder'] ?? '';
        
        foreach ( $folders_to_delete as $id_to_delete ) {
            // Remove from folders
            unset( $updated_folders[ $id_to_delete ] ); // Just in case
            unset( $data['folders'][ $id_to_delete ] ); // Remove from original data
            
            // Un-assign plugins
            foreach ( $data['assignments'] as $plugin_file => $folder_id ) {
                if ( $folder_id === $id_to_delete ) {
                    unset( $data['assignments'][ $plugin_file ] );
                }
            }
            
            // If this was the default folder, unset it
            if ( $default_folder === $id_to_delete ) {
                $settings['default_folder'] = '';
            }
        }
        
        // Save the updated settings if default was removed
        update_option('mkpo_settings', $settings);
    }
    
    // Merge updates back into data
    $data['folders'] = array_merge( $data['folders'], $updated_folders );
    
    update_option( 'mkpo_data', $data );
}


/**
 * Sanitize the main settings array.
 */
function mkpo_sanitize_settings( $input ) {
    $new_input = [];
    $new_input['debug_mode'] = isset( $input['debug_mode'] ) ? 1 : 0;
    
    if ( isset( $input['default_folder'] ) ) {
        $new_input['default_folder'] = sanitize_text_field( $input['default_folder'] );
    }
    
    return $new_input;
}

/**
 * Callback to render the Debug Mode checkbox.
 */
function mkpo_field_debug_mode_cb() {
    $settings = mkpo_get_settings();
    $is_checked = ! empty( $settings['debug_mode'] );
    ?>
    <label for="mkpo_debug_mode">
        <input type="checkbox" id="mkpo_debug_mode" name="mkpo_settings[debug_mode]" value="1" <?php checked( $is_checked ); ?>>
        <?php esc_html_e( 'Enable JavaScript debug messages in the browser console.', 'mk-plugin-organizer' ); ?>
    </label>
    <?php
}

/**
 * Callback to render the Default Folder dropdown.
 */
function mkpo_field_default_folder_cb() {
    $settings = mkpo_get_settings();
    $folders = mkpo_get_folders();
    $current_default = $settings['default_folder'] ?? '';
    ?>
    <select id="mkpo_default_folder" name="mkpo_settings[default_folder]">
        <option value="">&mdash; <?php esc_html_e( 'None', 'mk-plugin-organizer' ); ?> &mdash;</option>
        <?php foreach ( $folders as $id => $folder ) : ?>
            <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $current_default, $id ); ?>>
                <?php echo esc_html( $folder['name'] ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">
        <?php esc_html_e( 'Automatically redirect to this folder view when visiting the main Plugins page.', 'mk-plugin-organizer' ); ?>
    </p>
    <?php
}


/**
 * Add the minimal settings page under "Settings".
 */
function mkpo_add_settings_page() {
    add_options_page(
        __( 'Plugin Organizer', 'mk-plugin-organizer' ), // Page Title
        __( 'Plugin Organizer', 'mk-plugin-organizer' ), // Menu Title
        'manage_options',                                // Capability
        'mk-plugin-organizer',                           // Menu Slug
        'mkpo_render_settings_page'                      // Callback
    );
}

/**
 * Render the settings page content.
 */
function mkpo_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Plugin Organizer Settings', 'mk-plugin-organizer' ); ?></h1>

        <?php
        // --- Show Admin Notices for actions ---
        if ( isset( $_GET['deleted'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'All folders and settings have been deleted.', 'mk-plugin-organizer' ) . '</p></div>';
        }
        if ( isset( $_GET['imported'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings imported successfully.', 'mk-plugin-organizer' ) . '</p></div>';
        }
        if ( isset( $_GET['import_error'] ) ) {
            $error_code = (int) $_GET['import_error'];
            $message = esc_html__( 'An unknown import error occurred.', 'mk-plugin-organizer' );
            if ($error_code === 1) $message = esc_html__( 'No file was uploaded.', 'mk-plugin-organizer' );
            if ($error_code === 2) $message = esc_html__( 'Invalid file type. Please upload a .json file.', 'mk-plugin-organizer' );
            if ($error_code === 3) $message = esc_html__( 'Invalid file content. The JSON structure is incorrect.', 'mk-plugin-organizer' );
            echo '<div class="notice notice-error is-dismissible"><p>' . $message . '</p></div>';
        }
        if ( isset( $_GET['folders_updated'] ) ) {
             echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Folders updated successfully.', 'mk-plugin-organizer' ) . '</p></div>';
        }
        ?>

        <!-- 1. Main Settings Form -->
        <form action="options.php" method="POST">
            <?php
            settings_fields( 'mkpo_options_group' );
            do_settings_sections( 'mk-plugin-organizer' );
            submit_button();
            ?>
        </form>

        <hr>

        <!-- 2. Manage Folders -->
        <h2><?php esc_html_e( 'Manage Folders', 'mk-plugin-organizer' ); ?></h2>
        <form method="POST">
            <?php wp_nonce_field( 'mkpo_update_folders_action' ); ?>
            <input type="hidden" name="mkpo_action" value="update_folders">
            
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th style="width: 25%;"><?php esc_html_e( 'Folder Name', 'mk-plugin-organizer' ); ?></th>
                        <th style="width: 20%;"><?php esc_html_e( 'Background Color', 'mk-plugin-organizer' ); ?></th>
                        <th style="width: 20%;"><?php esc_html_e( 'Text Color', 'mk-plugin-organizer' ); ?></th>
                        <th style="width: 10%;"><?php esc_html_e( 'Preview', 'mk-plugin-organizer' ); ?></th>
                        <th style="width: 10%;"><?php esc_html_e( 'Delete?', 'mk-plugin-organizer' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $folders = mkpo_get_folders();
                    if ( empty( $folders ) ) :
                    ?>
                        <tr>
                            <td colspan="5"><?php esc_html_e( 'No folders found. Go to the Plugins page to create some.', 'mk-plugin-organizer' ); ?></td>
                        </tr>
                    <?php
                    else :
                        foreach ( $folders as $id => $folder ) :
                            $colors = mkpo_get_folder_colors( $folder );
                    ?>
                            <tr>
                                <td>
                                    <input type="text" name="folders[<?php echo esc_attr( $id ); ?>][name]"
                                           value="<?php echo esc_attr( $folder['name'] ); ?>" class="regular-text">
                                </td>
                                <td>
                                    <input type="text" name="folders[<?php echo esc_attr( $id ); ?>][bg_color]"
                                           value="<?php echo esc_attr( $colors['bg'] ); ?>" class="mkpo-color-picker">
                                </td>
                                <td>
                                    <input type="text" name="folders[<?php echo esc_attr( $id ); ?>][text_color]"
                                           value="<?php echo esc_attr( $colors['text'] ); ?>" class="mkpo-color-picker">
                                </td>
                                <td>
                                    <span class="mkpo-folder-tag" style="background-color: <?php echo esc_attr( $colors['bg'] ); ?>; color: <?php echo esc_attr( $colors['text'] ); ?>; border-color: <?php echo esc_attr( $colors['text'] ); ?>;">
                                        <?php esc_html_e( 'Preview', 'mk-plugin-organizer' ); ?>
                                    </span>
                                </td>
                                <td>
                                    <input type="checkbox" name="folders[<?php echo esc_attr( $id ); ?>][delete]" value="1">
                                </td>
                            </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
            <?php submit_button( __( 'Save Folder Changes', 'mk-plugin-organizer' ), 'primary', 'submit', false, [ 'style' => 'margin-top: 15px;' ] ); ?>
        </form>

        <hr>

        <!-- 3. Import/Export Forms -->
        <h2><?php esc_html_e( 'Import / Export', 'mk-plugin-organizer' ); ?></h2>
        
        <form method="POST" style="display: inline-block; margin-right: 20px;">
            <?php wp_nonce_field( 'mkpo_export_action' ); ?>
            <input type="hidden" name="mkpo_action" value="export_settings">
            <p><?php esc_html_e( 'Download a .json file of all your folders and plugin assignments.', 'mk-plugin-organizer' ); ?></p>
            <?php submit_button( __( 'Export Settings', 'mk-plugin-organizer' ), 'secondary', 'submit', false ); ?>
        </form>

        <form method="POST" enctype="multipart/form-data" style="display: inline-block;">
            <?php wp_nonce_field( 'mkpo_import_action' ); ?>
            <input type="hidden" name="mkpo_action" value="import_settings">
            <p><?php esc_html_e( 'Import settings from a .json file. This will overwrite all current settings.', 'mk-plugin-organizer' ); ?></p>
            <input type="file" name="mkpo_import_file" accept=".json" required>
            <?php submit_button( __( 'Import Settings', 'mk-plugin-organizer' ), 'secondary', 'submit', false ); ?>
        </form>
        
        <hr>

        <!-- 4. Danger Zone -->
        <h2><?php esc_html_e( 'Danger Zone', 'mk-plugin-organizer' ); ?></h2>
        <form method="POST" onsubmit="return confirm('<?php esc_attr_e( 'Are you sure you want to delete ALL plugin folders and assignments? This cannot be undone.', 'mk-plugin-organizer' ); ?>');">
            <?php wp_nonce_field( 'mkpo_delete_all_action' ); ?>
            <input type="hidden" name="mkpo_action" value="delete_all">
            <p><?php esc_html_e( 'Delete all folders, plugin assignments, and settings for this plugin.', 'mk-plugin-organizer' ); ?></p>
            <?php submit_button( __( 'Delete All Data', 'mk-plugin-organizer' ), 'delete', 'submit', false ); ?>
        </form>

    </div>
    <?php
}

// =========================================================================
// 3. Enqueue Assets (JS/CSS) (Unchanged from v1.12)
// =========================================================================

/**
 * Enqueue necessary JS libraries and pass data to JavaScript.
 */
function mkpo_enqueue_assets( $hook ) {
    
    // On plugins.php
    if ( 'plugins.php' === $hook ) {
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'jquery-ui-droppable' );
        
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        
        $settings = mkpo_get_settings();
        $debug_mode = ! empty( $settings['debug_mode'] );

        wp_localize_script( 'jquery-ui-draggable', 'mkpo_vars', [
            'ajax_url'     => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'mkpo-ajax-nonce' ),
            'text_loading' => esc_html__( 'Loading...', 'mk-plugin-organizer' ),
            'text_confirm' => esc_html__( 'Are you sure you want to delete this folder? Plugins in it will be unassigned.', 'mk-plugin-organizer' ),
            'debug_mode'   => (bool) $debug_mode,
            'text_hide_folders' => esc_html__( 'Hide Folders', 'mk-plugin-organizer' ), 
            'text_show_folders' => esc_html__( 'Show Folders', 'mk-plugin-organizer' ), 
        ] );
    }
    
    // On our settings page
    if ( 'settings_page_mk-plugin-organizer' === $hook ) {
         wp_enqueue_style( 'wp-color-picker' );
         wp_enqueue_script( 'wp-color-picker' );
    }
}

// =========================================================================
// 4. Print Custom CSS (*** UPDATED ***)
// =========================================================================

/**
 * Print the custom CSS directly into the admin head.
 */
function mkpo_print_css() {
    // Get admin bar height (normally 32px)
    $admin_bar_height = is_admin_bar_showing() ? '32px' : '0px';
    ?>
    <style type="text/css">
        /* Folder Manager Box (v1.2 Sticky CSS) */
        #mkpo-folder-manager {
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #c3c4c7;
            background: #fff;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            
            /* -- Sticky Settings -- */
            position: sticky;
            top: <?php echo $admin_bar_height; ?>; 
            z-index: 99; 
        }
        
        /* Class to hide the box */
        #mkpo-folder-manager.hidden {
            display: none;
        }

        /* Toggle Button */
        #mkpo-toggle-button {
            margin-left: 5px;
            vertical-align: top; /* Align with "Apply" button */
        }

        /* Folder List */
        #mkpo-folder-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 15px;
            min-height: 40px;
        }
        .mkpo-folder-item {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #c3c4c7;
            background: #f6f7f7;
            border-radius: 4px;
            font-weight: 600;
            user-select: none;
            position: relative;
        }
        .mkpo-folder-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            border: 1px solid rgba(0,0,0,0.2);
            background-clip: padding-box;
        }
        .mkpo-delete-folder {
            cursor: pointer;
            margin-left: 8px;
            color: #d63638; /* Default, will be overridden by inline style */
            opacity: 0.7; 
            transition: opacity 0.2s;
            font-size: 1.2em;
            text-decoration: none;
        }
        .mkpo-delete-folder:hover {
            opacity: 1;
        }
        
        /* Unassign Folder */
        .mkpo-unassign {
            background: #fef3f3;
            border-color: #d63638;
            color: #d63638;
        }
        .mkpo-unassign .mkpo-delete-folder {
             color: #d63638 !important; /* Ensure delete X is red */
        }

        /* Drag-and-Drop States */
        .mkpo-folder-item.ui-droppable-hover {
            box-shadow: 0 0 5px 2px #007cba; 
            border-color: #007cba;
        }
        .mkpo-folder-item.mkpo-unassign.ui-droppable-hover {
            border-color: #d63638;
            box-shadow: 0 0 5px 2px #d63638; 
        }
        .ui-draggable-helper {
            background: #fff;
            padding: 10px 20px;
            border: 1px solid #c3c4c7;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            z-index: 9999;
            font-size: 1.1em;
            font-weight: bold;
        }
        
        /* New Folder Form */
        #mkpo-new-folder-form {
            display: flex;
            flex-wrap: wrap; /* Allow wrapping */
            align-items: center;
            gap: 10px;
        }
        #mkpo-new-folder-form label {
            font-weight: bold;
            margin-right: -5px;
        }
        #mkpo-new-folder-form .wp-picker-container {
            vertical-align: middle;
        }
        #mkpo-new-folder-form .wp-picker-input-wrap {
            vertical-align: top;
        }
        .mkpo-color-picker-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .mkpo-color-picker-wrapper label {
            font-size: 0.9em;
            color: #555;
            font-weight: normal;
        }
        
        /* (*** NEW ***) Fix for color picker margin */
        #mkpo-new-folder-form .wp-picker-container .wp-color-result.button {
            margin: 0;
        }


        /* "Folder" Column & Settings Page Tag */
        .wp-list-table .column-mkpo_folder {
            width: 15%;
        }
        .mkpo-folder-tag {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 0.9em;
            border: 1px solid;
            line-height: 1.5;
        }
        
        /* Settings page delete button */
        .submit .delete {
            color: #d63638;
            border-color: #d63638;
        }
        .submit .delete:hover {
            background: #d63638;
            color: #fff;
        }

        /* Filter Links Color */
        .subsubsub a[data-mkpo-link="true"].current {
            padding-left: 5px; /* Re-add padding for current link */
            padding-right: 5px;
            border: 1px solid #000;
        }
        .subsubsub a[data-mkpo-link="true"] .count {
            color: inherit !important; 
        }
        
        /* (*** NEW ***) Class for filter links */
        .subsubsub a.mkpo-filter-link {
            border-radius: 3px;
            padding: 4px 6px; /* User's suggested padding */
        }

    </style>
    <?php
}

// =========================================================================
// 5. Render Folder Manager UI (Unchanged from v1.12)
// =========================================================================

/**
 * Render the HTML for the folder manager box above the plugin list.
 */
function mkpo_render_folder_ui() {
    // This empty div acts as a trigger for the IntersectionObserver to detect when the folder manager becomes sticky.
    // The observer is set up in mkpo_print_js().
    echo '<div id="mkpo-sticky-sentinel" style="height: 1px; position: relative; top: -1px;"></div>';

    $folders = mkpo_get_folders();
    ?>
    <div id="mkpo-folder-manager">
        <h2 id="mkpo-manager-title"><?php esc_html_e( 'Plugin Organizer', 'mk-plugin-organizer' ); ?></h2>

        <div id="mkpo-folder-list">
            <!-- "Unassign" Folder -->
            <div class="mkpo-folder-item mkpo-unassign" data-folder-id="">
                <?php esc_html_e( 'Unassign', 'mk-plugin-organizer' ); ?>
            </div>

            <!-- Dynamic Folders -->
            <?php foreach ( $folders as $id => $folder ) : ?>
                <?php
                $name = $folder['name'];
                $colors = mkpo_get_folder_colors( $folder ); // Use new helper
                ?>
                <div class="mkpo-folder-item"
                     data-folder-id="<?php echo esc_attr( $id ); ?>"
                     style="background-color: <?php echo esc_attr( $colors['bg'] ); ?>; color: <?php echo esc_attr( $colors['text'] ); ?>; border-color: <?php echo esc_attr( $colors['text'] ); ?>;">
                    
                    <span class="mkpo-folder-color"
                          style="background-color: <?php echo esc_attr( $colors['bg'] ); ?>; border-color: <?php echo esc_attr( $colors['text'] ); ?>;">
                    </span>
                    
                    <span class="mkpo-folder-name"><?php echo esc_html( $name ); ?></span>
                    
                    <a href="#" class="mkpo-delete-folder"
                       title="<?php esc_attr_e( 'Delete folder', 'mk-plugin-organizer' ); ?>"
                       style="color: <?php echo esc_attr( $colors['text'] ); ?>;">
                       &times;
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="mkpo-new-folder-form">
            <label for="mkpo-new-folder-name"><?php esc_html_e( 'New Folder:', 'mk-plugin-organizer' ); ?></label>
            <input type="text" id="mkpo-new-folder-name" placeholder="<?php esc_attr_e( 'Folder Name', 'mk-plugin-organizer' ); ?>" />
            
            <div class="mkpo-color-picker-wrapper">
                <label for="mkpo-new-folder-bg-color"><?php esc_html_e( 'BG:', 'mk-plugin-organizer' ); ?></label>
                <input type="text" id="mkpo-new-folder-bg-color" class="mkpo-color-picker" value="#cccccc" />
            </div>
            
            <div class="mkpo-color-picker-wrapper">
                 <label for="mkpo-new-folder-text-color"><?php esc_html_e( 'Text:', 'mk-plugin-organizer' ); ?></label>
                <input type="text" id="mkpo-new-folder-text-color" class="mkpo-color-picker" value="#000000" />
            </div>

            <button type="button" class="button button-secondary" id="mkpo-create-folder-btn"><?php esc_html_e( 'Create', 'mk-plugin-organizer' ); ?></button>
            <span class="spinner"></span>
        </div>
    </div>
    <?php
}

// =========================================================================
// 6. Add/Render Plugin Table Column (Unchanged from v1.12)
// =========================================================================

/**
 * Add the "Folder" column header to the plugins table.
 */
function mkpo_add_folder_column( $columns ) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        if ($key === 'description') {
            $new_columns['mkpo_folder'] = __( 'Folder', 'mk-plugin-organizer' );
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

/**
 * Render the content for each plugin's "Folder" column.
 */
function mkpo_render_folder_column( $column_name, $plugin_file ) {
    if ( 'mkpo_folder' === $column_name ) {
        $assignments = mkpo_get_assignments();
        $folders     = mkpo_get_folders();
        
        $folder_id = $assignments[ $plugin_file ] ?? '';
        
        echo '<span class="mkpo-folder-tag-container">';
        if ( $folder_id && isset( $folders[ $folder_id ] ) ) {
            $folder = $folders[ $folder_id ];
            $colors = mkpo_get_folder_colors( $folder ); // Use new helper
            
            printf(
                '<span class="mkpo-folder-tag" style="background-color: %s; color: %s; border-color: %s;">%s</span>',
                esc_attr( $colors['bg'] ),
                esc_attr( $colors['text'] ),
                esc_attr( $colors['text'] ), // Border matches text
                esc_html( $folder['name'] )
            );
        } else {
            echo '—';
        }
        echo '</span>';
    }
}


// =========================================================================
// 7. Add Filter Links (*** UPDATED ***)
// =========================================================================

/**
 * Add filter links (e.g., "Folder: SEO") next to "All", "Active", etc.
 */
function mkpo_add_folder_views( $views ) {
    $folders     = mkpo_get_folders();
    $assignments = mkpo_get_assignments();
    
    if ( empty( $folders ) ) {
        return $views;
    }
    
    // We need to get all plugins to count accurately
    $all_plugins = get_plugins();
    
    // Count plugins in each folder
    $counts = [];
    foreach ( $assignments as $plugin_file => $folder_id ) {
        // Only count plugins that are actually installed
        if ( isset( $all_plugins[$plugin_file] ) ) {
             if ( ! isset( $counts[ $folder_id ] ) ) {
                $counts[ $folder_id ] = 0;
            }
            $counts[ $folder_id ]++;
        }
    }

    $current_folder = $_GET['plugin_folder'] ?? '';

    foreach ( $folders as $id => $folder ) {
        $count = $counts[ $id ] ?? 0;
        if ( $count === 0 ) {
            continue; // Don't show filters for empty folders
        }

        $url = add_query_arg( 'plugin_folder', $id, 'plugins.php' );
        $is_current = ( $current_folder === $id );
        
        $colors = mkpo_get_folder_colors( $folder ); // Use new helper
        
        // --- Style for the link (colors only) ---
        $style_attr = sprintf(
            'style="background-color: %s !important; color: %s !important;"',
            esc_attr( $colors['bg'] ),
            esc_attr( $colors['text'] )
        );
        
        // --- Class for the link (layout + current state) ---
        $class_attr = $is_current ? 'class="current mkpo-filter-link"' : 'class="mkpo-filter-link"';

        $views[ 'mkpo_folder_' . $id ] = sprintf(
            '<a href="%s" %s %s data-mkpo-link="true">%s <span class="count">(%d)</span></a>',
            esc_url( $url ),
            $class_attr, // Contains 'mkpo-filter-link'
            $style_attr, // Contains only colors
            esc_html( $folder['name'] ),
            $count
        );
    }
    
    // Check if we are filtering, if so, make "All" not current
    if ( ! empty( $current_folder ) && isset( $views['all'] ) ) {
         $views['all'] = str_replace( 'class="current"', '', $views['all'] );
    }

    return $views;
}

// =========================================================================
// 8. Filter Plugin List (Unchanged from v1.12)
// =========================================================================

/**
 * Filter the main $all_plugins array based on the `plugin_folder` query var.
 */
function mkpo_filter_plugins_by_folder( $all_plugins ) {
    $current_folder = $_GET['plugin_folder'] ?? '';

    if ( empty( $current_folder ) ) {
        return $all_plugins;
    }

    $assignments = mkpo_get_assignments();
    $filtered_plugins = [];

    foreach ( $all_plugins as $plugin_file => $plugin_data ) {
        $plugin_folder = $assignments[ $plugin_file ] ?? '';
        
        if ( $plugin_folder === $current_folder ) {
            $filtered_plugins[ $plugin_file ] = $plugin_data;
        }
    }

    return $filtered_plugins;
}

// =========================================================================
// 9. Handle Default Folder Redirect (Unchanged from v1.12)
// =========================================================================

/**
 * Redirects to the default folder view if one is set.
 * Hooked to 'load-plugins.php'.
 */
function mkpo_redirect_to_default_folder() {
    // Check if we are on the main plugins page.
    // We only want to redirect if NO other filters are set.
    if ( ! empty( $_GET ) ) {
        return; // Has GET params (e.g., plugin_status, s, plugin_folder), so don't redirect.
    }
    
    // Make sure we're not doing something like activating/deactivating a plugin
    if ( isset( $_GET['action'] ) ) {
        return;
    }
    
    $settings = mkpo_get_settings();
    $default_folder = $settings['default_folder'] ?? '';
    
    // If a default folder is set, redirect to it.
    if ( ! empty( $default_folder ) ) {
        $redirect_url = add_query_arg( 'plugin_folder', $default_folder, admin_url( 'plugins.php' ) );
        
        // Safe redirect
        wp_redirect( $redirect_url );
        exit;
    }
}


// =========================================================================
// 10. AJAX Handlers (Unchanged from v1.12)
// =========================================================================

/**
 * AJAX: Create a new folder.
 */
function mkpo_ajax_create_folder() {
    check_ajax_referer( 'mkpo-ajax-nonce', '_ajax_nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied.' ] );
    }

    $name       = sanitize_text_field( $_POST['name'] );
    $bg_color   = sanitize_hex_color( $_POST['bg_color'] );
    $text_color = sanitize_hex_color( $_POST['text_color'] );

    if ( empty( $name ) ) {
        wp_send_json_error( [ 'message' => 'Folder name is required.' ] );
    }
    if ( empty( $bg_color ) ) $bg_color = '#cccccc';
    if ( empty( $text_color ) ) $text_color = '#000000';

    $id   = sanitize_title( $name ) . '-' . wp_rand(100, 999);
    $data = mkpo_get_data();

    $data['folders'][ $id ] = [
        'name'       => $name,
        'bg_color'   => $bg_color,
        'text_color' => $text_color,
    ];

    update_option( 'mkpo_data', $data );

    wp_send_json_success( [
        'id'         => $id,
        'name'       => $name,
        'bg_color'   => $bg_color,
        'text_color' => $text_color,
    ] );
}

/**
 * AJAX: Delete a folder.
 */
function mkpo_ajax_delete_folder() {
    check_ajax_referer( 'mkpo-ajax-nonce', '_ajax_nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied.' ] );
    }

    $folder_id = sanitize_text_field( $_POST['folder_id'] );

    if ( empty( $folder_id ) ) {
        wp_send_json_error( [ 'message' => 'Invalid folder ID.' ] );
    }

    $data = mkpo_get_data();

    // Remove the folder
    unset( $data['folders'][ $folder_id ] );

    // Unassign all plugins from this folder
    foreach ( $data['assignments'] as $plugin_file => $plugin_folder_id ) {
        if ( $plugin_folder_id === $folder_id ) {
            unset( $data['assignments'][ $plugin_file ] );
        }
    }

    update_option( 'mkpo_data', $data );
    wp_send_json_success( [ 'message' => 'Folder deleted.' ] );
}

/**
 * AJAX: Assign a plugin to a folder.
 */
function mkpo_ajax_assign_plugin() {
    check_ajax_referer( 'mkpo-ajax-nonce', '_ajax_nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied.' ] );
    }

    $plugin_file = sanitize_text_field( $_POST['plugin'] );
    $folder_id   = sanitize_text_field( $_POST['folder_id'] );
    
    $data = mkpo_get_data();

    if ( ! isset( $data['folders'][ $folder_id ] ) ) {
        wp_send_json_error( [ 'message' => 'Folder not found.' ] );
    }

    $data['assignments'][ $plugin_file ] = $folder_id;
    update_option( 'mkpo_data', $data );

    // Send back the new HTML for the table cell
    $folder = $data['folders'][ $folder_id ];
    $colors = mkpo_get_folder_colors( $folder ); // Use new helper
    
    $html = sprintf(
        '<span class="mkpo-folder-tag" style="background-color: %s; color: %s; border-color: %s;">%s</span>',
        esc_attr( $colors['bg'] ),
        esc_attr( $colors['text'] ),
        esc_attr( $colors['text'] ),
        esc_html( $folder['name'] )
    );

    wp_send_json_success( [ 'html' => $html ] );
}

/**
 * AJAX: Unassign a plugin from any folder.
 */
function mkpo_ajax_unassign_plugin() {
    check_ajax_referer( 'mkpo-ajax-nonce', '_ajax_nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => 'Permission denied.' ] );
    }

    $plugin_file = sanitize_text_field( $_POST['plugin'] );
    
    $data = mkpo_get_data();

    if ( isset( $data['assignments'][ $plugin_file ] ) ) {
        unset( $data['assignments'][ $plugin_file ] );
        update_option( 'mkpo_data', $data );
    }

    wp_send_json_success( [ 'html' => '—' ] );
}


// =========================================================================
// 11. Print Custom JavaScript (plugins.php) (Unchanged from v1.12)
// =========================================================================

/**
 * Print the custom jQuery script directly into the admin footer
 * for the main plugins.php page.
 */
function mkpo_print_js() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {

        // Define the localStorage key
        var MKPO_VISIBILITY_KEY = 'mkpo_folders_visible';

        // Debug helper
        function mkpo_debug( message, data ) {
            if ( ! mkpo_vars.debug_mode ) {
                return;
            }
            if ( data ) {
                console.log( 'MKPO Debug:', message, data );
            } else {
                console.log( 'MKPO Debug:', message );
            }
        }

        mkpo_debug( 'Plugin Organizer script loaded.' );

        /**
         * Helper function to initialize droppable behavior on folder items.
         */
        function mkpo_init_droppable( $elements ) {
            if ( !$elements || $elements.length === 0 ) {
                return;
            }

            $elements.droppable({
                accept: '#the-list tr[data-plugin]',
                tolerance: 'pointer',
                
                drop: function(event, ui) {
                    var $folder = $(this);
                    var $pluginRow = ui.draggable;
                    
                    var pluginFile = $pluginRow.data('plugin');
                    var folderId = $folder.data('folder-id');
                    var action = (folderId === '' || folderId === undefined) ? 'mkpo_unassign_plugin' : 'mkpo_assign_plugin';

                    mkpo_debug( 'Plugin dropped!', { 
                        plugin: pluginFile, 
                        folderId: folderId,
                        action: action
                    });

                    $pluginRow.find('.column-mkpo_folder .mkpo-folder-tag-container').html(mkpo_vars.text_loading);
                    $folder.css('opacity', '0.5');

                    $.post(mkpo_vars.ajax_url, {
                        action: action,
                        _ajax_nonce: mkpo_vars.nonce,
                        plugin: pluginFile,
                        folder_id: folderId
                    })
                    .done(function(response) {
                        mkpo_debug( 'AJAX Success', response );
                        if (response.success) {
                            $pluginRow.find('.column-mkpo_folder .mkpo-folder-tag-container').html(response.data.html);
                            $pluginRow.css('background-color', '#F0FFF0').animate({
                                backgroundColor: $pluginRow.hasClass('alternate') ? '#f6f7f7' : '#ffffff'
                            }, 1000);
                        } else {
                            console.error('MKPO Error: ' + response.data.message);
                            $pluginRow.find('.column-mkpo_folder .mkpo-folder-tag-container').html('—');
                        }
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.error('MKPO AJAX Failed', { status: textStatus, error: errorThrown });
                        alert('An unknown AJAX error occurred.');
                        $pluginRow.find('.column-mkpo_folder .mkpo-folder-tag-container').html('—');
                    })
                    .always(function() {
                        $folder.css('opacity', '1');
                    });
                },
                
                over: function(event, ui) {
                    $(this).addClass('ui-droppable-hover');
                },
                
                out: function(event, ui) {
                    $(this).removeClass('ui-droppable-hover');
                }
            });
            
            mkpo_debug( 'Initialized droppable on ' + $elements.length + ' folder items.' );
        }

        // 1. Initialize Color Picker
        $('#mkpo-new-folder-bg-color').wpColorPicker();
        $('#mkpo-new-folder-text-color').wpColorPicker();
        mkpo_debug( 'Color pickers initialized.' );

        // 2. Make Plugin Rows Draggable
        $('#the-list tr[data-plugin]').draggable({
            revert: 'invalid',
            containment: 'document',
            helper: function() {
                var pluginName = $(this).find('.plugin-title strong').text();
                return $('<div class="ui-draggable-helper"></div>').text(pluginName);
            },
            cursorAt: { left: 5, top: 5 },
            start: function(event, ui) {
                mkpo_debug( 'Dragging plugin:', $(this).data('plugin') );
                $(this).addClass('plugin-dragging');
            },
            stop: function(event, ui) {
                $(this).removeClass('plugin-dragging');
            }
        });
        mkpo_debug( 'Draggable initialized on ' + $('#the-list tr[data-plugin]').length + ' plugin rows.' );

        // 3. Make *existing* Folders Droppable on page load
        mkpo_init_droppable( $('.mkpo-folder-item') );
        
        // 4. Handle Create Folder Button Click
        $('#mkpo-create-folder-btn').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $spinner = $button.siblings('.spinner');
            var $nameInput = $('#mkpo-new-folder-name');
            var $bgColorInput = $('#mkpo-new-folder-bg-color');
            var $textColorInput = $('#mkpo-new-folder-text-color');
            
            var folderName = $nameInput.val();
            var folderBgColor = $bgColorInput.val();
            var folderTextColor = $textColorInput.val();

            if (!folderName) {
                $nameInput.css('border-color', 'red');
                return;
            }
            $nameInput.css('border-color', '');

            $button.prop('disabled', true);
            $spinner.addClass('is-active');

            $.post(mkpo_vars.ajax_url, {
                action: 'mkpo_create_folder',
                _ajax_nonce: mkpo_vars.nonce,
                name: folderName,
                bg_color: folderBgColor,
                text_color: folderTextColor
            })
            .done(function(response) {
                if (response.success) {
                    mkpo_debug('Folder created', response.data);
                    var newFolderHtml = `
                        <div class="mkpo-folder-item"
                             data-folder-id="${response.data.id}"
                             style="background-color: ${response.data.bg_color}; color: ${response.data.text_color}; border-color: ${response.data.text_color};">
                            
                            <span class="mkpo-folder-color"
                                  style="background-color: ${response.data.bg_color}; border-color: ${response.data.text_color};">
                            </span>
                            
                            <span class="mkpo-folder-name">${response.data.name}</span>
                            
                            <a href="#" class="mkpo-delete-folder"
                               title="<?php esc_attr_e( 'Delete folder', 'mk-plugin-organizer' ); ?>"
                               style="color: ${response.data.text_color};">
                               &times;
                            </a>
                        </div>
                    `;
                    
                    var $newFolder = $(newFolderHtml).appendTo('#mkpo-folder-list');
                    
                    mkpo_init_droppable( $newFolder );
                    
                    $nameInput.val('');
                    $bgColorInput.wpColorPicker('color', '#cccccc');
                    $textColorInput.wpColorPicker('color', '#000000');
                } else {
                    console.error('MKPO Error: ' + response.data.message);
                }
            })
            .fail(function() {
                console.error('MKPO Error: Folder creation AJAX failed.');
            })
            .always(function() {
                $button.prop('disabled', false);
                $spinner.removeClass('is-active');
            });
        });

        // 5. Handle Delete Folder Button Click (using delegation)
        $('#mkpo-folder-list').on('click', '.mkpo-delete-folder', function(e) {
            e.preventDefault();
            
            if ( ! confirm( mkpo_vars.text_confirm ) ) {
                return;
            }

            var $folder = $(this).closest('.mkpo-folder-item');
            var folderId = $folder.data('folder-id');
            mkpo_debug( 'Deleting folder:', folderId );

            $folder.css('opacity', '0.5');

            $.post(mkpo_vars.ajax_url, {
                action: 'mkpo_delete_folder',
                _ajax_nonce: mkpo_vars.nonce,
                folder_id: folderId
            })
            .done(function(response) {
                if (response.success) {
                    mkpo_debug( 'Folder deleted. Reloading page.' );
                    $folder.remove();
                    location.reload(); // Reload to update table and filters
                } else {
                    console.error('MKPO Error: ' + response.data.message);
                    $folder.css('opacity', '1');
                }
            })
            .fail(function() {
                console.error('MKPO Error: Folder deletion AJAX failed.');
                $folder.css('opacity', '1');
            });
        });

        // 6. Add Toggle Button
        var $toggleButton = $('<button type="button" class="button" id="mkpo-toggle-button"></button>');
        var $applyButton = $('#doaction'); // Top "Apply" button
        
        if ($applyButton.length) {
            $applyButton.after($toggleButton);
            mkpo_debug('Toggle button added.');
        }

        // 7. Check Saved State and Set Initial View
        var $box = $('#mkpo-folder-manager');
        var isVisible = localStorage.getItem(MKPO_VISIBILITY_KEY);

        if (isVisible === 'false') {
            // It was saved as hidden
            $box.addClass('hidden');
            $toggleButton.text(mkpo_vars.text_show_folders);
            mkpo_debug('Restored state: hidden');
        } else {
            // Default to visible
            $box.removeClass('hidden');
            $toggleButton.text(mkpo_vars.text_hide_folders);
            mkpo_debug('Restored state: visible');
        }

        // 8. Handle Toggle Button Click
        $toggleButton.on('click', function(e) {
            e.preventDefault();
            
            // Toggle the box
            $box.toggleClass('hidden'); 
            
            // Update button text and save state
            if ( $box.is(':visible') ) {
                $(this).text(mkpo_vars.text_hide_folders);
                localStorage.setItem(MKPO_VISIBILITY_KEY, 'true');
                mkpo_debug('Toggled visible, saved state.');
            } else {
                $(this).text(mkpo_vars.text_show_folders);
                localStorage.setItem(MKPO_VISIBILITY_KEY, 'false');
                mkpo_debug('Toggled hidden, saved state.');
            }
        });

        // 9. Handle Sticky State for the "New Folder" form
        var $folderManager = $('#mkpo-folder-manager');
        var $newFolderForm = $('#mkpo-new-folder-form');
        var $managerTitle = $('#mkpo-manager-title');
        var sentinel = document.getElementById('mkpo-sticky-sentinel');

        if (sentinel && 'IntersectionObserver' in window) {
            mkpo_debug('Setting up IntersectionObserver for sticky state.');

            var observer = new IntersectionObserver(function(entries) {
                var entry = entries[0];
                
                // When the sentinel is NOT intersecting (i.e., has scrolled off-screen),
                // the folder manager is now sticky.
                if (!entry.isIntersecting) {
                    mkpo_debug('Sticky state activated: hiding form and title.');
                    $newFolderForm.hide();
                    $managerTitle.hide();
                    $folderManager.css('padding-bottom', '10px'); // Reduce padding
                } else {
                    mkpo_debug('Sticky state deactivated: showing form and title.');
                    $newFolderForm.show();
                    $managerTitle.show();
                    $folderManager.css('padding-bottom', ''); // Restore original padding
                }
            }, {
                // The rootMargin is calculated based on the sticky `top` position.
                // This makes the observer fire exactly when the element sticks.
                rootMargin: '-<?php echo is_admin_bar_showing() ? '33px' : '1px'; ?> 0px 0px 0px',
                threshold: [0]
            });

            observer.observe(sentinel);
        }
    });
    </script>
    <?php
}

// =========================================================================
// 12. Print Custom JavaScript (Settings Page) (Unchanged from v1.12)
// =========================================================================

/**
 * Print the custom jQuery script directly into the admin footer
 * for the settings page.
 */
function mkpo_print_settings_js() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Initialize all color pickers on the settings page
        $('.mkpo-color-picker').wpColorPicker();
    });
    </script>
    <?php
}