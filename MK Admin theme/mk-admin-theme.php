<?php
/**
 * Plugin Name: MK Admin Theme
 * Plugin URI:  https://meksone.com
 * Description: Custom WordPress admin theme with Poppins font, rounded corners, and a blue/yellow palette. Fully customizable via Settings > Impostazioni tema admin.
 * Version:     1.0.2
 * Author:      Manuel Serrenti (meksONE)
 * Author URI:  https://meksone.com
 * License:     GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MK_ADMIN_THEME_VERSION', '1.0.2' );
define( 'MK_ADMIN_THEME_URL',     plugin_dir_url( __FILE__ ) );
define( 'MK_ADMIN_THEME_PATH',    plugin_dir_path( __FILE__ ) );

// ──────────────────────────────────────────────────────────────────────────────
// Default colour palette
// ──────────────────────────────────────────────────────────────────────────────
function mk_admin_theme_defaults() {
    return [
        'bg_base'         => '#f0f0f1',   // page background (grey)
        'bg_menu'         => '#013162',   // sidebar background
        'bg_menu_hover'   => '#01234a',   // sidebar item hover
        'bg_menu_current' => '#fdc513',   // active menu item bg
        'bg_menu_current_hover' => '#e6b000', // active item hover
        'text_menu'       => '#e8ecf0',   // sidebar text
        'text_menu_current' => '#013162', // active menu item text
        'color_primary'   => '#013162',   // primary blue (buttons, headings…)
        'color_accent'    => '#fdc513',   // accent yellow
        'color_accent_text' => '#013162', // text on accent bg
        'bg_topbar'       => '#013162',   // top admin bar bg
        'text_topbar'     => '#e8ecf0',   // top admin bar text
        'border_radius'   => '5',         // px — not a colour but lives here
        'color_link'      => '#013162',   // content area links
        'color_button_primary_bg'   => '#013162',
        'color_button_primary_text' => '#ffffff',
        'color_button_secondary_bg' => '#fdc513',
        'color_button_secondary_text' => '#013162',
        // Integrations
        'sync_elementor_gutenberg'  => '0',
        'sync_elementor_acf'        => '0',
    ];
}

function mk_admin_theme_get( $key ) {
    $defaults = mk_admin_theme_defaults();
    $saved    = get_option( 'mk_admin_theme_options', [] );
    return isset( $saved[ $key ] ) ? sanitize_text_field( $saved[ $key ] ) : ( $defaults[ $key ] ?? '' );
}

// ──────────────────────────────────────────────────────────────────────────────
// Enqueue styles & fonts
// ──────────────────────────────────────────────────────────────────────────────
function mk_admin_theme_enqueue() {
    // Google Fonts – Poppins
    wp_enqueue_style(
        'mk-admin-poppins',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
        [],
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'mk-admin-theme',
        MK_ADMIN_THEME_URL . 'admin-style.css',
        [ 'mk-admin-poppins' ],
        MK_ADMIN_THEME_VERSION
    );

    // Inject CSS custom properties (dynamic colours from options)
    $css = mk_admin_theme_css_vars();
    wp_add_inline_style( 'mk-admin-theme', $css );
}
add_action( 'admin_enqueue_scripts', 'mk_admin_theme_enqueue' );

// Also apply to login page
add_action( 'login_enqueue_scripts', 'mk_admin_theme_enqueue' );

function mk_admin_theme_css_vars() {
    $r = mk_admin_theme_get( 'border_radius' );
    $r = is_numeric( $r ) ? (int) $r : 5;

    $vars = '
:root {
    --mk-bg-base:                  ' . mk_admin_theme_get('bg_base') . ';
    --mk-bg-menu:                  ' . mk_admin_theme_get('bg_menu') . ';
    --mk-bg-menu-hover:            ' . mk_admin_theme_get('bg_menu_hover') . ';
    --mk-bg-menu-current:          ' . mk_admin_theme_get('bg_menu_current') . ';
    --mk-bg-menu-current-hover:    ' . mk_admin_theme_get('bg_menu_current_hover') . ';
    --mk-text-menu:                ' . mk_admin_theme_get('text_menu') . ';
    --mk-text-menu-current:        ' . mk_admin_theme_get('text_menu_current') . ';
    --mk-color-primary:            ' . mk_admin_theme_get('color_primary') . ';
    --mk-color-accent:             ' . mk_admin_theme_get('color_accent') . ';
    --mk-color-accent-text:        ' . mk_admin_theme_get('color_accent_text') . ';
    --mk-bg-topbar:                ' . mk_admin_theme_get('bg_topbar') . ';
    --mk-text-topbar:              ' . mk_admin_theme_get('text_topbar') . ';
    --mk-color-link:               ' . mk_admin_theme_get('color_link') . ';
    --mk-btn-primary-bg:           ' . mk_admin_theme_get('color_button_primary_bg') . ';
    --mk-btn-primary-text:         ' . mk_admin_theme_get('color_button_primary_text') . ';
    --mk-btn-secondary-bg:         ' . mk_admin_theme_get('color_button_secondary_bg') . ';
    --mk-btn-secondary-text:       ' . mk_admin_theme_get('color_button_secondary_text') . ';
    --mk-radius:                   ' . $r . 'px;
}';
    return $vars;
}

// ──────────────────────────────────────────────────────────────────────────────
// Settings page
// ──────────────────────────────────────────────────────────────────────────────
function mk_admin_theme_add_settings_page() {
    add_options_page(
        'Impostazioni tema admin',
        'Impostazioni tema admin',
        'manage_options',
        'mk-admin-theme',
        'mk_admin_theme_settings_page'
    );
}
add_action( 'admin_menu', 'mk_admin_theme_add_settings_page' );

function mk_admin_theme_register_settings() {
    register_setting(
        'mk_admin_theme_group',
        'mk_admin_theme_options',
        [ 'sanitize_callback' => 'mk_admin_theme_sanitize' ]
    );
}
add_action( 'admin_init', 'mk_admin_theme_register_settings' );

function mk_admin_theme_sanitize( $input ) {
    $defaults     = mk_admin_theme_defaults();
    $bool_fields  = [ 'sync_elementor_gutenberg', 'sync_elementor_acf' ];
    $output       = [];

    foreach ( $defaults as $key => $default ) {
        if ( $key === 'border_radius' ) {
            $output[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : absint( $default );
        } elseif ( in_array( $key, $bool_fields, true ) ) {
            $output[ $key ] = ! empty( $input[ $key ] ) ? '1' : '0';
        } else {
            $output[ $key ] = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) ?? $default : $default;
        }
    }
    return $output;
}

// Colour picker asset
function mk_admin_theme_admin_scripts( $hook ) {
    if ( $hook !== 'settings_page_mk-admin-theme' ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script(
        'mk-admin-theme-settings',
        MK_ADMIN_THEME_URL . 'settings.js',
        [ 'wp-color-picker' ],
        MK_ADMIN_THEME_VERSION,
        true
    );

    // Pass Elementor palette to the settings color pickers (if available).
    $palette = [];
    $colors  = mk_admin_theme_get_elementor_colors();
    if ( ! is_wp_error( $colors ) ) {
        $palette = array_column( $colors, 'color' );
    }
    wp_localize_script( 'mk-admin-theme-settings', 'mkAdminTheme', [
        'elementorPalette' => $palette,
    ] );
}
add_action( 'admin_enqueue_scripts', 'mk_admin_theme_admin_scripts' );

// ──────────────────────────────────────────────────────────────────────────────
// Settings page HTML
// ──────────────────────────────────────────────────────────────────────────────
function mk_admin_theme_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $fields = [
        'Generali' => [
            'bg_base'       => 'Sfondo pagina (grigio base)',
            'color_primary' => 'Colore primario (blu)',
            'color_accent'  => 'Colore accento (giallo)',
            'color_accent_text' => 'Testo su sfondo accento',
            'color_link'    => 'Colore link (area contenuto)',
        ],
        'Barra superiore (Toolbar)' => [
            'bg_topbar'   => 'Sfondo toolbar',
            'text_topbar' => 'Testo toolbar',
        ],
        'Menu laterale' => [
            'bg_menu'          => 'Sfondo menu',
            'bg_menu_hover'    => 'Hover voce menu',
            'bg_menu_current'         => 'Sfondo voce attiva',
            'bg_menu_current_hover'  => 'Hover voce attiva',
            'text_menu'        => 'Testo menu',
            'text_menu_current' => 'Testo voce attiva',
        ],
        'Bottoni' => [
            'color_button_primary_bg'     => 'Bottone primario – sfondo',
            'color_button_primary_text'   => 'Bottone primario – testo',
            'color_button_secondary_bg'   => 'Bottone secondario – sfondo',
            'color_button_secondary_text' => 'Bottone secondario – testo',
        ],
    ];
    ?>
    <div class="wrap">
        <h1>Impostazioni tema admin</h1>
        <p>Personalizza colori e stile della dashboard WordPress.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'mk_admin_theme_group' ); ?>

            <?php foreach ( $fields as $section => $section_fields ) : ?>
                <h2><?php echo esc_html( $section ); ?></h2>
                <table class="form-table" role="presentation">
                    <?php foreach ( $section_fields as $key => $label ) : ?>
                        <tr>
                            <th scope="row">
                                <label for="mk_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
                            </th>
                            <td>
                                <input
                                    type="text"
                                    id="mk_<?php echo esc_attr( $key ); ?>"
                                    name="mk_admin_theme_options[<?php echo esc_attr( $key ); ?>]"
                                    value="<?php echo esc_attr( mk_admin_theme_get( $key ) ); ?>"
                                    class="mk-color-picker"
                                    data-default-color="<?php $d = mk_admin_theme_defaults(); echo esc_attr( $d[ $key ] ?? '#000000' ); ?>"
                                />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>

            <!-- Integrations -->
            <h2>Integrazioni</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Sincronizzazione palette Elementor</th>
                    <td>
                        <fieldset>
                            <label>
                                <input
                                    type="checkbox"
                                    name="mk_admin_theme_options[sync_elementor_gutenberg]"
                                    value="1"
                                    <?php checked( mk_admin_theme_get( 'sync_elementor_gutenberg' ), '1' ); ?>
                                />
                                Sincronizza palette Elementor → <strong>Gutenberg</strong>
                            </label>
                            <br>
                            <label>
                                <input
                                    type="checkbox"
                                    name="mk_admin_theme_options[sync_elementor_acf]"
                                    value="1"
                                    <?php checked( mk_admin_theme_get( 'sync_elementor_acf' ), '1' ); ?>
                                />
                                Sincronizza palette Elementor → <strong>ACF color picker</strong>
                            </label>
                            <p class="description">Richiede Elementor attivo. Se Elementor non è disponibile vengono usati i colori di fallback WordPress.</p>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <!-- Border radius (numeric) -->
            <h2>Bordi</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="mk_border_radius">Raggio bordo (px)</label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="mk_border_radius"
                            name="mk_admin_theme_options[border_radius]"
                            value="<?php echo esc_attr( mk_admin_theme_get( 'border_radius' ) ); ?>"
                            min="0" max="50" step="1"
                            class="small-text"
                        />
                        <p class="description">Valore predefinito: 5. Imposta 0 per angoli netti.</p>
                    </td>
                </tr>
            </table>

            <p style="margin-top:24px;">
                <?php submit_button( 'Salva impostazioni', 'primary', 'submit', false ); ?>
                &nbsp;
                <a href="<?php echo esc_url( add_query_arg( 'mk_reset', '1', admin_url( 'options-general.php?page=mk-admin-theme' ) ) ); ?>"
                   class="button"
                   onclick="return confirm('Ripristinare i valori predefiniti?');">
                    Ripristina predefiniti
                </a>
            </p>
        </form>
    </div>
    <?php
}

// Reset to defaults
function mk_admin_theme_maybe_reset() {
    if (
        isset( $_GET['mk_reset'] ) &&
        isset( $_GET['page'] ) && $_GET['page'] === 'mk-admin-theme' &&
        current_user_can( 'manage_options' )
    ) {
        delete_option( 'mk_admin_theme_options' );
        wp_redirect( admin_url( 'options-general.php?page=mk-admin-theme&mk_reset_done=1' ) );
        exit;
    }
}
add_action( 'admin_init', 'mk_admin_theme_maybe_reset' );

// ──────────────────────────────────────────────────────────────────────────────
// Elementor palette sync
// ──────────────────────────────────────────────────────────────────────────────

function mk_admin_theme_get_elementor_colors() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        return new WP_Error( 'elementor_missing', 'Elementor is not active' );
    }

    $kit_id = get_option( 'elementor_active_kit' );
    if ( ! $kit_id ) {
        return new WP_Error( 'no_active_kit', 'No active Elementor Kit found' );
    }

    $settings = get_post_meta( $kit_id, '_elementor_page_settings' );
    if ( empty( $settings ) || ! is_array( $settings ) ) {
        return new WP_Error( 'no_kit_settings', 'No Elementor Kit settings found' );
    }

    if ( ! isset( $settings[0]['system_colors'] ) || ! isset( $settings[0]['custom_colors'] ) ) {
        return new WP_Error( 'no_color_settings', 'No color settings found in Elementor Kit' );
    }

    $all = array_merge( $settings[0]['system_colors'], $settings[0]['custom_colors'] );

    foreach ( $all as $color ) {
        if ( ! isset( $color['title'], $color['color'], $color['_id'] ) ) {
            return new WP_Error( 'invalid_color_data', 'Invalid color data structure in Elementor Kit' );
        }
    }

    return $all;
}

function mk_admin_theme_elementor_fallback_colors() {
    return [
        [ 'title' => 'Black', '_id' => 'black', 'color' => '#000000' ],
        [ 'title' => 'White', '_id' => 'white', 'color' => '#ffffff' ],
        [ 'title' => 'Blue',  '_id' => 'blue',  'color' => '#0073aa' ],
        [ 'title' => 'Grey',  '_id' => 'grey',  'color' => '#767676' ],
    ];
}

/**
 * Sync Elementor palette → Gutenberg editor-color-palette.
 * Runs only when the option is enabled.
 */
function mk_admin_theme_sync_elementor_gutenberg() {
    if ( mk_admin_theme_get( 'sync_elementor_gutenberg' ) !== '1' ) {
        return;
    }

    $colors = mk_admin_theme_get_elementor_colors();
    if ( is_wp_error( $colors ) ) {
        error_log( 'MK Admin Theme – Elementor→Gutenberg sync: ' . $colors->get_error_message() );
        $colors = mk_admin_theme_elementor_fallback_colors();
    }

    $palette = [];
    foreach ( $colors as $c ) {
        $palette[] = [
            'name'  => $c['title'],
            'slug'  => $c['_id'],
            'color' => $c['color'],
        ];
    }

    add_theme_support( 'editor-color-palette', $palette );
}
add_action( 'after_setup_theme', 'mk_admin_theme_sync_elementor_gutenberg' );

/**
 * Sync Elementor palette → ACF color picker swatches.
 * Runs only when the option is enabled.
 */
function mk_admin_theme_sync_elementor_acf() {
    if ( mk_admin_theme_get( 'sync_elementor_acf' ) !== '1' ) {
        return;
    }

    if ( ! class_exists( 'ACF' ) ) {
        return;
    }

    $colors = mk_admin_theme_get_elementor_colors();
    if ( is_wp_error( $colors ) ) {
        error_log( 'MK Admin Theme – Elementor→ACF sync: ' . $colors->get_error_message() );
        $colors = mk_admin_theme_elementor_fallback_colors();
    }

    $values = array_column( $colors, 'color' );
    $names  = array_column( $colors, 'title' );
    ?>
    <script type="text/javascript">
    (function ($) {
        var colorNames  = <?php echo wp_json_encode( $names ); ?>;
        var colorValues = <?php echo wp_json_encode( $values ); ?>;

        acf.add_filter('color_picker_args', function (args, $field) {
            args.palettes = colorValues;
            return args;
        });
    }(jQuery));
    </script>
    <?php
}
add_action( 'acf/input/admin_footer', 'mk_admin_theme_sync_elementor_acf' );

/**
 * Admin notice when Elementor sync is enabled but Elementor is unavailable.
 */
function mk_admin_theme_elementor_sync_notice() {
    if (
        mk_admin_theme_get( 'sync_elementor_gutenberg' ) !== '1' &&
        mk_admin_theme_get( 'sync_elementor_acf' ) !== '1'
    ) {
        return;
    }

    $colors = mk_admin_theme_get_elementor_colors();
    if ( is_wp_error( $colors ) && current_user_can( 'manage_options' ) ) {
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo '<strong>MK Admin Theme:</strong> ';
        echo esc_html( 'Sincronizzazione Elementor attiva ma impossibile leggere i colori: ' . $colors->get_error_message() );
        echo '</p></div>';
    }
}
add_action( 'admin_notices', 'mk_admin_theme_elementor_sync_notice' );

// ──────────────────────────────────────────────────────────────────────────────
// Dark / Light mode toggle
// ──────────────────────────────────────────────────────────────────────────────

/**
 * Add the toggle as an admin bar node, right after the "My Account" user node.
 * Priority 0 puts it early; WP admin bar renders account at priority 7 by default.
 */
function mk_admin_theme_adminbar_toggle( $wp_admin_bar ) {
    $wp_admin_bar->add_node( [
        'id'    => 'mk-dark-mode',
        'title' => '
            <span class="mk-toggle-wrap" aria-label="Dark / Light mode" title="Dark / Light mode">
                <span class="mk-toggle-track"><span class="mk-toggle-thumb"></span></span>
                <span class="mk-toggle-label ab-label">Dark</span>
            </span>',
        'href'  => '#',
        'meta'  => [
            'class'   => 'mk-adminbar-toggle',
            'onclick' => 'return false;',
        ],
    ] );
}
add_action( 'admin_bar_menu', 'mk_admin_theme_adminbar_toggle', 99 );

/**
 * JS: wires the toggle + restores saved preference.
 * Injected at admin_footer so the DOM is ready.
 */
function mk_admin_theme_dark_toggle_js() {
    ?>
    <script>
    (function () {
        var STORAGE_KEY = 'mkAdminDarkMode';
        var body  = document.body;
        var node  = document.getElementById('wp-admin-bar-mk-dark-mode');
        var label = node ? node.querySelector('.mk-toggle-label') : null;
        var thumb = node ? node.querySelector('.mk-toggle-thumb') : null;

        function applyMode(dark) {
            body.classList.toggle('mk-dark', dark);
            if (label) label.textContent = dark ? 'Light' : 'Dark';
            if (node)  node.classList.toggle('mk-is-dark', dark);
        }

        applyMode(localStorage.getItem(STORAGE_KEY) === '1');

        if (node) {
            node.addEventListener('click', function (e) {
                e.preventDefault();
                var isDark = body.classList.contains('mk-dark');
                applyMode(!isDark);
                localStorage.setItem(STORAGE_KEY, isDark ? '0' : '1');
            });
        }
    }());
    </script>
    <?php
}
add_action( 'admin_footer', 'mk_admin_theme_dark_toggle_js' );

/**
 * Anti-FOUC: re-apply class before first paint.
 */
function mk_admin_theme_dark_head_script() {
    echo '<script>if(localStorage.getItem("mkAdminDarkMode")==="1"){document.body.classList.add("mk-dark");}</script>' . "\n";
}
add_action( 'admin_head', 'mk_admin_theme_dark_head_script' );

