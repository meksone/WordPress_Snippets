<?php
/**
 * Plugin Name: Global Settings Manager
 * Description: Basic WordPress plugin to manage plugin visibility, user options, Capital P dangit, and Post Types visibility.
 * Version: 1.1.0
 * Author: meksONE
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Meksone_Global_Settings {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'init', array( $this, 'execute_front_end_functions' ) );
        
        // Disable Capital P dangit
        if ( get_option( 'mgs_disable_capital_p', 1 ) ) {
            remove_filter( 'the_title', 'capital_P_dangit', 11 );
            remove_filter( 'the_content', 'capital_P_dangit', 11 );
            remove_filter( 'comment_text', 'capital_P_dangit', 31 );
        }
    }

    public function add_admin_menu() {
        // Add options page under Settings
        add_options_page(
            'Global Settings Manager',
            'Global Settings',
            'manage_options',
            'meksone-global-settings',
            array( $this, 'options_page_html' )
        );
    }

    public function register_settings() {
        register_setting( 'mgs_settings_group', 'mgs_excluded_users_roles' );
        
        register_setting( 'mgs_settings_group', 'mgs_plugins_to_hide', array(
            'type' => 'array',
            'sanitize_callback' => array( $this, 'sanitize_array_input' )
        ));

        register_setting( 'mgs_settings_group', 'mgs_post_types_to_hide', array(
            'type' => 'array',
            'sanitize_callback' => array( $this, 'sanitize_array_input' )
        ));

        register_setting( 'mgs_settings_group', 'mgs_remove_color_picker' );
        register_setting( 'mgs_settings_group', 'mgs_default_color_scheme' );
        register_setting( 'mgs_settings_group', 'mgs_disable_capital_p' );
    }

    public function sanitize_array_input( $input ) {
        return is_array( $input ) ? array_map( 'sanitize_text_field', $input ) : array();
    }

    public function options_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Get all installed plugins
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();

        // Get all public post types meant to show in UI
        $post_types = get_post_types( array( 'show_ui' => true ), 'objects' );

        ?>
        <div class="wrap">
            <h1>Global Settings Manager</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'mgs_settings_group' ); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Excluded Usernames or Roles (comma separated)</th>
                        <td>
                            <input type="text" name="mgs_excluded_users_roles" value="<?php echo esc_attr( get_option( 'mgs_excluded_users_roles', 'bim-adm, administrator' ) ); ?>" class="regular-text" />
                            <p class="description">These users/roles will be completely ignored by the rules below (e.g. they will still see the hidden plugins and menus).</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">Post Types to Hide</th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span>Post Types to Hide</span></legend>
                                <?php 
                                $post_types_to_hide = get_option( 'mgs_post_types_to_hide', array() );
                                if ( ! is_array( $post_types_to_hide ) ) {
                                    $post_types_to_hide = array();
                                }

                                foreach ( $post_types as $pt_slug => $pt_obj ) {
                                    $is_checked = in_array( $pt_slug, $post_types_to_hide, true );
                                    ?>
                                    <label style="display:inline-block; margin-bottom: 5px; margin-right: 15px;">
                                        <input type="checkbox" name="mgs_post_types_to_hide[]" value="<?php echo esc_attr( $pt_slug ); ?>" <?php checked( $is_checked, true ); ?> />
                                        <?php echo esc_html( $pt_obj->labels->name ); ?> 
                                    </label>
                                    <?php
                                }
                                ?>
                                <p class="description">Selecting a post type here will hide its menu item from the WordPress sidebar for the restricted users.</p>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Plugins to Hide</th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span>Plugins to Hide</span></legend>
                                <?php 
                                $plugins_to_hide = get_option( 'mgs_plugins_to_hide', array() );
                                if ( ! is_array( $plugins_to_hide ) ) {
                                    $plugins_to_hide = array();
                                }

                                foreach ( $all_plugins as $plugin_path => $plugin_data ) {
                                    $is_checked = in_array( $plugin_path, $plugins_to_hide, true );
                                    // Don't allow hiding this very plugin to prevent locking yourself out of the settings
                                    $is_disabled = ( $plugin_path === 'meksone-global-settings/meksone-global-settings.php' );
                                    ?>
                                    <label style="display:block; margin-bottom: 5px;">
                                        <input type="checkbox" name="mgs_plugins_to_hide[]" value="<?php echo esc_attr( $plugin_path ); ?>" <?php checked( $is_checked, true ); ?> <?php disabled( $is_disabled, true ); ?> />
                                        <?php echo esc_html( $plugin_data['Name'] ); ?> 
                                        <span style="color: #888; font-size: 12px;">(<?php echo esc_html( $plugin_path ); ?>)</span>
                                    </label>
                                    <?php
                                }
                                ?>
                                <p class="description">Selecting a plugin here will hide it from the plugin list for the users configured above.</p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Remove Color Picker for users</th>
                        <td>
                            <input type="checkbox" name="mgs_remove_color_picker" value="1" <?php checked( 1, get_option( 'mgs_remove_color_picker', 1 ) ); ?> />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Default Color Scheme</th>
                        <td>
                            <input type="text" name="mgs_default_color_scheme" value="<?php echo esc_attr( get_option( 'mgs_default_color_scheme', '80s-kid' ) ); ?>" class="regular-text" />
                            <p class="description">Leave empty to use WordPress default.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Disable "Capital P dangit"</th>
                        <td>
                            <input type="checkbox" name="mgs_disable_capital_p" value="1" <?php checked( 1, get_option( 'mgs_disable_capital_p', 1 ) ); ?> />
                            <p class="description">Removes the filter that changes "Wordpress" to "WordPress" in titles and content.</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function execute_front_end_functions() {
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            
            $excluded_input_str = get_option( 'mgs_excluded_users_roles', 'bim-adm, administrator' );
            $excluded_input = array_filter( array_map( 'trim', explode( ',', $excluded_input_str ) ) );

            $is_excluded = false;
            foreach ( $excluded_input as $exclusion ) {
                if ( $current_user->user_login === $exclusion || in_array( strtolower( $exclusion ), (array) $current_user->roles ) ) {
                    $is_excluded = true;
                    break;
                }
            }

            // If the user isn't excluded, apply the restrictions
            if ( ! $is_excluded ) {
                
                // 1. Hide specific plugins
                $plugins_to_hide = get_option( 'mgs_plugins_to_hide', array() );

                if ( ! empty( $plugins_to_hide ) && is_array( $plugins_to_hide ) ) {
                    $this->hide_plugins( $plugins_to_hide );
                }

                // 2. Hide specific post types from admin menu
                $post_types_to_hide = get_option( 'mgs_post_types_to_hide', array() );
                if ( ! empty( $post_types_to_hide ) && is_array( $post_types_to_hide ) ) {
                    add_action( 'admin_menu', function() use ( $post_types_to_hide ) {
                        foreach ( $post_types_to_hide as $pt ) {
                            if ( $pt === 'post' ) {
                                remove_menu_page( 'edit.php' );
                            } elseif ( $pt === 'attachment' ) {
                                remove_menu_page( 'upload.php' ); // Removes specifically the Media menu
                            } else {
                                remove_menu_page( 'edit.php?post_type=' . $pt );
                            }
                        }
                    }, 999 );
                }

                // 3. Remove color scheme picker
                if ( get_option( 'mgs_remove_color_picker', 1 ) ) {
                    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
                }
                
                // 4. Set default admin color scheme
                $default_color = get_option( 'mgs_default_color_scheme', '80s-kid' );
                if ( ! empty( $default_color ) ) {
                    add_filter( 'get_user_option_admin_color', function( $color_scheme ) use ( $default_color ) {
                        return $default_color;
                    }, 5 );
                }
            }
        }
    }

    private function hide_plugins( $hiddenPlugins ) {
        add_filter( 'all_plugins', function ( $plugins ) use ( $hiddenPlugins ) {
            $shouldHide = ! array_key_exists( 'show_all', $_GET );

            if ( $shouldHide ) {
                foreach ( $hiddenPlugins as $hiddenPlugin ) {
                    unset( $plugins[$hiddenPlugin] );
                }
            }
            return $plugins;
        });
    }

}

new Meksone_Global_Settings();
