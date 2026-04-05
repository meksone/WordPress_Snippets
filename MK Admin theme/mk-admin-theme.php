<?php
/**
 * Plugin Name: MK Admin Theme
 * Plugin URI:  https://meksone.com
 * Description: Custom WordPress admin theme with Poppins font, rounded corners, and a blue/yellow palette. Fully customizable via Settings > Impostazioni tema admin.
 * Version:     1.0.18
 * Author:      Manuel Serrenti (meksONE)
 * Author URI:  https://meksone.com
 * License:     GPL-2.0+
 * Text Domain: mk-admin-theme
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MK_ADMIN_THEME_VERSION', '1.0.18' );
define( 'MK_ADMIN_THEME_URL',     plugin_dir_url( __FILE__ ) );
define( 'MK_ADMIN_THEME_PATH',    plugin_dir_path( __FILE__ ) );

function mk_admin_theme_load_textdomain() {
    load_plugin_textdomain( 'mk-admin-theme', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'mk_admin_theme_load_textdomain' );

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
        // Postbox header
        'postbox_header_bg'      => '',       // empty = inherit --mk-color-primary
        'postbox_header_text'    => '#ffffff',
        'postbox_header_padding' => '6',      // px vertical
        // Integrations – Elementor sync
        'sync_elementor_gutenberg'  => '0',
        'sync_elementor_acf'        => '0',
        // Integrations – Gutenberg
        'gutenberg_title_only'       => '0',
        'gutenberg_title_only_types' => 'post',
        // Integrations – Sidebar resize
        'sidebar_resize'             => '0',
        'sidebar_resize_types'       => 'post, page',
        'sidebar_resize_logo'        => '',
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
    $r   = mk_admin_theme_get( 'border_radius' );
    $r   = is_numeric( $r ) ? (int) $r : 5;

    $pbg  = mk_admin_theme_get( 'postbox_header_bg' );
    $ptxt = mk_admin_theme_get( 'postbox_header_text' );
    $ppad = mk_admin_theme_get( 'postbox_header_padding' );
    $ppad = is_numeric( $ppad ) ? (int) $ppad : 6;

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
    --mk-postbox-header-bg:        ' . ( $pbg  ?: 'var(--mk-color-primary)' ) . ';
    --mk-postbox-header-text:      ' . ( $ptxt ?: '#ffffff' ) . ';
    --mk-postbox-header-padding:   ' . $ppad . 'px;
}';
    return $vars;
}

// Postbox header override CSS block — shared output function.
function mk_admin_theme_postbox_css_block( $include_acf_selector = false ) {
    $pbg  = mk_admin_theme_get( 'postbox_header_bg' );
    $ptxt = mk_admin_theme_get( 'postbox_header_text' );
    $ppad = mk_admin_theme_get( 'postbox_header_padding' );
    $ppad = is_numeric( $ppad ) ? (int) $ppad : 6;
    $bg   = esc_attr( $pbg ?: 'var(--mk-color-primary)' );
    $txt  = esc_attr( $ptxt ?: '#ffffff' );

    // On ACF pages, mirror ACF's own specificity prefix so we win.
    $pfx = $include_acf_selector ? '.acf-admin-page #poststuff ' : '';
    ?>
    <style id="mk-postbox-header-override">
    <?php echo $pfx; ?>.postbox .postbox-header,
    <?php echo $pfx; ?>.postbox h2.hndle {
        background: <?php echo $bg; ?> !important;
        color: <?php echo $txt; ?> !important;
        padding: <?php echo $ppad; ?>px 14px !important;
    }
    <?php echo $pfx; ?>.postbox .postbox-header h2,
    <?php echo $pfx; ?>.postbox .postbox-header h3,
    <?php echo $pfx; ?>.postbox .postbox-header .hndle,
    <?php echo $pfx; ?>.postbox .postbox-header .hndle span,
    <?php echo $pfx; ?>.postbox h2.hndle,
    <?php echo $pfx; ?>.postbox h2.hndle span {
        color: <?php echo $txt; ?> !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    <?php echo $pfx; ?>.postbox .postbox-header:hover,
    <?php echo $pfx; ?>.postbox h2.hndle:hover {
        background: <?php echo $bg; ?> !important;
        color: <?php echo $txt; ?> !important;
    }
    <?php echo $pfx; ?>.postbox .postbox-header h2:hover,
    <?php echo $pfx; ?>.postbox .postbox-header h3:hover,
    <?php echo $pfx; ?>.postbox .postbox-header .hndle:hover,
    <?php echo $pfx; ?>.postbox .postbox-header .hndle:focus {
        background: transparent !important;
        color: <?php echo $txt; ?> !important;
        box-shadow: none !important;
    }
    </style>
    <?php
}

// Non-ACF admin pages — fires after enqueued styles are printed.
add_action( 'admin_head', function () { mk_admin_theme_postbox_css_block( false ); }, 9999 );

// ACF input pages (post edit, options pages with ACF fields) —
// acf/input/admin_head fires after ACF outputs its own inline CSS.
add_action( 'acf/input/admin_head', function () { mk_admin_theme_postbox_css_block( true ); } );

// ACF field group editor — separate hook, same approach.
add_action( 'acf/field_group/admin_head', function () { mk_admin_theme_postbox_css_block( true ); } );

// ACF admin toolbar override.
// Output in admin_footer (body end) — runs after all <head> stylesheets
// and after any ACF inline styles injected via admin_head hooks.
function mk_admin_theme_acf_toolbar_css() {
    if ( ! ( function_exists( 'get_current_screen' ) ) ) {
        return;
    }
    $screen = get_current_screen();
    if ( ! $screen || strpos( $screen->id, 'acf' ) === false ) {
        return;
    }

    $topbar_bg  = esc_attr( mk_admin_theme_get( 'bg_topbar' )         ?: '#013162' );
    $topbar_txt = esc_attr( mk_admin_theme_get( 'text_topbar' )       ?: '#e8ecf0' );
    $accent     = esc_attr( mk_admin_theme_get( 'color_accent' )      ?: '#fdc513' );
    $accent_txt = esc_attr( mk_admin_theme_get( 'color_accent_text' ) ?: '#013162' );
    $menu_hover = esc_attr( mk_admin_theme_get( 'bg_menu_hover' )     ?: '#01234a' );
    ?>
    <style id="mk-acf-toolbar-override">
    /* Two-class prefix to beat .acf-admin-page .acf-admin-toolbar specificity */
    .acf-admin-page .acf-admin-toolbar,
    body.acf-admin-page .acf-admin-toolbar {
        background: <?php echo $topbar_bg; ?> !important;
        border-bottom-color: <?php echo $menu_hover; ?> !important;
    }
    .acf-admin-page .acf-admin-toolbar .acf-nav-wrap h2 {
        color: <?php echo $topbar_txt; ?> !important;
    }
    .acf-admin-page .acf-admin-toolbar .acf-tab {
        color: <?php echo $topbar_txt; ?> !important;
        background: transparent !important;
        border-color: transparent !important;
    }
    .acf-admin-page .acf-admin-toolbar .acf-tab:hover {
        color: <?php echo $accent; ?> !important;
        background: <?php echo $menu_hover; ?> !important;
    }
    .acf-admin-page .acf-admin-toolbar .acf-tab.is-active {
        color: <?php echo $accent_txt; ?> !important;
        background: <?php echo $accent; ?> !important;
        border-color: <?php echo $accent; ?> !important;
    }
    /* "Altro" dropdown */
    .acf-admin-page .acf-admin-toolbar .acf-more > ul {
        background: <?php echo $topbar_bg; ?> !important;
        border-color: <?php echo $menu_hover; ?> !important;
    }
    .acf-admin-page .acf-admin-toolbar .acf-more > ul .acf-tab {
        color: <?php echo $topbar_txt; ?> !important;
    }
    .acf-admin-page .acf-admin-toolbar .acf-more > ul .acf-tab:hover {
        color: <?php echo $accent; ?> !important;
        background: <?php echo $menu_hover; ?> !important;
    }
    </style>
    <?php
}
// admin_footer runs in <body> — after every stylesheet in <head>.
add_action( 'admin_footer', 'mk_admin_theme_acf_toolbar_css', 9999 );

// ──────────────────────────────────────────────────────────────────────────────
// Settings page
// ──────────────────────────────────────────────────────────────────────────────
function mk_admin_theme_add_settings_page() {
    add_options_page(
        __( 'Impostazioni tema admin', 'mk-admin-theme' ),
        __( 'Impostazioni tema admin', 'mk-admin-theme' ),
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
    $bool_fields  = [ 'sync_elementor_gutenberg', 'sync_elementor_acf', 'gutenberg_title_only', 'sidebar_resize' ];
    $text_fields  = [ 'gutenberg_title_only_types', 'sidebar_resize_types' ];
    $url_fields   = [ 'sidebar_resize_logo' ];
    $output       = [];

    foreach ( $defaults as $key => $default ) {
        if ( in_array( $key, [ 'border_radius', 'postbox_header_padding' ], true ) ) {
            $output[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : absint( $default );
        } elseif ( in_array( $key, $bool_fields, true ) ) {
            $output[ $key ] = ! empty( $input[ $key ] ) ? '1' : '0';
        } elseif ( in_array( $key, $text_fields, true ) ) {
            $output[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : $default;
        } elseif ( in_array( $key, $url_fields, true ) ) {
            $output[ $key ] = isset( $input[ $key ] ) ? esc_url_raw( $input[ $key ] ) : $default;
        } else {
            $output[ $key ] = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) ?? $default : $default;
        }
    }
    return $output;
}

// Colour picker asset
function mk_admin_theme_admin_scripts( $hook ) {
    // Enqueue jquery-ui-resizable on post edit screens when sidebar resize is enabled.
    if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
        if ( mk_admin_theme_get( 'sidebar_resize' ) === '1' ) {
            wp_enqueue_script( 'jquery-ui-resizable' );
        }
        return;
    }

    $is_theme_page = ( $hook === 'settings_page_mk-admin-theme' );
    $is_acf_page   = ( $hook === 'settings_page_mk-acf-styles' );

    if ( ! $is_theme_page && ! $is_acf_page ) {
        return;
    }

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_media();
    wp_enqueue_script(
        'mk-admin-theme-settings',
        MK_ADMIN_THEME_URL . 'settings.js',
        [ 'wp-color-picker' ],
        MK_ADMIN_THEME_VERSION,
        true
    );

    // Pass Elementor palette to the settings color pickers (if available).
    // Only solid hex colors — Iris does not support transparency.
    $palette = [];
    $colors  = mk_admin_theme_get_elementor_colors();
    if ( ! is_wp_error( $colors ) ) {
        foreach ( array_column( $colors, 'color' ) as $hex ) {
            if ( preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $hex ) ) {
                $palette[] = $hex;
            }
        }
    }
    wp_localize_script( 'mk-admin-theme-settings', 'mkAdminTheme', [
        'elementorPalette' => $palette,
        'isAcfPage'        => $is_acf_page,
        'i18n'             => [
            'selectColor'  => __( '🎨', 'mk-admin-theme' ),
            'mediaTitle'   => __( 'Seleziona immagine', 'mk-admin-theme' ),
            'mediaButton'  => __( 'Usa questa immagine', 'mk-admin-theme' ),
            'noClass'      => __( 'Nessuna classe.', 'mk-admin-theme' ),
            'lightLabel'   => __( 'Light', 'mk-admin-theme' ),
            'darkLabel'    => __( 'Dark', 'mk-admin-theme' ),
        ],
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
        __( 'Generali', 'mk-admin-theme' ) => [
            'bg_base'           => __( 'Sfondo pagina (grigio base)', 'mk-admin-theme' ),
            'color_primary'     => __( 'Colore primario (blu)', 'mk-admin-theme' ),
            'color_accent'      => __( 'Colore accento (giallo)', 'mk-admin-theme' ),
            'color_accent_text' => __( 'Testo su sfondo accento', 'mk-admin-theme' ),
            'color_link'        => __( 'Colore link (area contenuto)', 'mk-admin-theme' ),
        ],
        __( 'Barra superiore (Toolbar)', 'mk-admin-theme' ) => [
            'bg_topbar'   => __( 'Sfondo toolbar', 'mk-admin-theme' ),
            'text_topbar' => __( 'Testo toolbar', 'mk-admin-theme' ),
        ],
        __( 'Menu laterale', 'mk-admin-theme' ) => [
            'bg_menu'               => __( 'Sfondo menu', 'mk-admin-theme' ),
            'bg_menu_hover'         => __( 'Hover voce menu', 'mk-admin-theme' ),
            'bg_menu_current'       => __( 'Sfondo voce attiva', 'mk-admin-theme' ),
            'bg_menu_current_hover' => __( 'Hover voce attiva', 'mk-admin-theme' ),
            'text_menu'             => __( 'Testo menu', 'mk-admin-theme' ),
            'text_menu_current'     => __( 'Testo voce attiva', 'mk-admin-theme' ),
        ],
        __( 'Bottoni', 'mk-admin-theme' ) => [
            'color_button_primary_bg'     => __( 'Bottone primario – sfondo', 'mk-admin-theme' ),
            'color_button_primary_text'   => __( 'Bottone primario – testo', 'mk-admin-theme' ),
            'color_button_secondary_bg'   => __( 'Bottone secondario – sfondo', 'mk-admin-theme' ),
            'color_button_secondary_text' => __( 'Bottone secondario – testo', 'mk-admin-theme' ),
        ],
    ];
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Impostazioni tema admin', 'mk-admin-theme' ); ?></h1>
        <p><?php esc_html_e( 'Personalizza colori e stile della dashboard WordPress.', 'mk-admin-theme' ); ?></p>

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
            <h2><?php esc_html_e( 'Integrazioni', 'mk-admin-theme' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Sincronizzazione palette Elementor', 'mk-admin-theme' ); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input
                                    type="checkbox"
                                    name="mk_admin_theme_options[sync_elementor_gutenberg]"
                                    value="1"
                                    <?php checked( mk_admin_theme_get( 'sync_elementor_gutenberg' ), '1' ); ?>
                                />
                                <?php printf( esc_html__( 'Sincronizza palette Elementor → %s', 'mk-admin-theme' ), '<strong>Gutenberg</strong>' ); ?>
                            </label>
                            <br>
                            <label>
                                <input
                                    type="checkbox"
                                    name="mk_admin_theme_options[sync_elementor_acf]"
                                    value="1"
                                    <?php checked( mk_admin_theme_get( 'sync_elementor_acf' ), '1' ); ?>
                                />
                                <?php printf( esc_html__( 'Sincronizza palette Elementor → %s', 'mk-admin-theme' ), '<strong>ACF color picker</strong>' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Richiede Elementor attivo. Se Elementor non è disponibile vengono usati i colori di fallback WordPress.', 'mk-admin-theme' ); ?></p>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <!-- Gutenberg title-only mode -->
            <h2><?php esc_html_e( 'Gutenberg', 'mk-admin-theme' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Modalità solo titolo', 'mk-admin-theme' ); ?></th>
                    <td>
                        <label>
                            <input
                                type="checkbox"
                                name="mk_admin_theme_options[gutenberg_title_only]"
                                value="1"
                                <?php checked( mk_admin_theme_get( 'gutenberg_title_only' ), '1' ); ?>
                            />
                            <?php esc_html_e( 'Disabilita tutti i blocchi e i pattern — mostra solo il campo titolo', 'mk-admin-theme' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mk_gutenberg_title_only_types"><?php esc_html_e( 'Post type', 'mk-admin-theme' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="mk_gutenberg_title_only_types"
                            name="mk_admin_theme_options[gutenberg_title_only_types]"
                            value="<?php echo esc_attr( mk_admin_theme_get( 'gutenberg_title_only_types' ) ); ?>"
                            class="regular-text"
                        />
                        <p class="description"><?php printf( esc_html__( 'Post type separati da virgola (es. %s). Attivo solo se la modalità è abilitata.', 'mk-admin-theme' ), '<code>post, film, prodotto</code>' ); ?></p>
                    </td>
                </tr>
            </table>

            <!-- Sidebar resize -->
            <h2><?php esc_html_e( 'Sidebar Gutenberg ridimensionabile', 'mk-admin-theme' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Abilita', 'mk-admin-theme' ); ?></th>
                    <td>
                        <label>
                            <input
                                type="checkbox"
                                name="mk_admin_theme_options[sidebar_resize]"
                                value="1"
                                <?php checked( mk_admin_theme_get( 'sidebar_resize' ), '1' ); ?>
                            />
                            <?php esc_html_e( 'Rendi ridimensionabile la sidebar di Gutenberg (drag dal bordo sinistro)', 'mk-admin-theme' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mk_sidebar_resize_types"><?php esc_html_e( 'Post type', 'mk-admin-theme' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="mk_sidebar_resize_types"
                            name="mk_admin_theme_options[sidebar_resize_types]"
                            value="<?php echo esc_attr( mk_admin_theme_get( 'sidebar_resize_types' ) ); ?>"
                            class="regular-text"
                        />
                        <p class="description"><?php printf( esc_html__( 'Post type separati da virgola (es. %s). Lascia vuoto per tutti.', 'mk-admin-theme' ), '<code>post, page, film</code>' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mk_sidebar_resize_logo"><?php esc_html_e( 'Logo overlay (durante il drag)', 'mk-admin-theme' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="mk_sidebar_resize_logo"
                            name="mk_admin_theme_options[sidebar_resize_logo]"
                            value="<?php echo esc_attr( mk_admin_theme_get( 'sidebar_resize_logo' ) ); ?>"
                            class="regular-text"
                        />
                        <button type="button" class="button mk-media-upload" data-target="#mk_sidebar_resize_logo">
                            <?php esc_html_e( 'Scegli immagine', 'mk-admin-theme' ); ?>
                        </button>
                        <p class="description"><?php esc_html_e( "URL dell'immagine mostrata come overlay mentre si ridimensiona la sidebar. Lascia vuoto per sfondo bianco.", 'mk-admin-theme' ); ?></p>
                    </td>
                </tr>
            </table>

            <!-- Border radius (numeric) -->
            <h2><?php esc_html_e( 'Bordi', 'mk-admin-theme' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="mk_border_radius"><?php esc_html_e( 'Raggio bordo (px)', 'mk-admin-theme' ); ?></label>
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
                        <p class="description"><?php esc_html_e( 'Valore predefinito: 5. Imposta 0 per angoli netti.', 'mk-admin-theme' ); ?></p>
                    </td>
                </tr>
            </table>

            <!-- Postbox header -->
            <h2><?php esc_html_e( 'Intestazione Postbox', 'mk-admin-theme' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="mk_postbox_header_bg"><?php esc_html_e( 'Sfondo intestazione', 'mk-admin-theme' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="mk_postbox_header_bg"
                            name="mk_admin_theme_options[postbox_header_bg]"
                            value="<?php echo esc_attr( mk_admin_theme_get( 'postbox_header_bg' ) ); ?>"
                            class="mk-color-picker"
                            data-default-color=""
                        />
                        <p class="description"><?php esc_html_e( 'Lascia vuoto per usare il colore primario.', 'mk-admin-theme' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mk_postbox_header_text"><?php esc_html_e( 'Colore testo intestazione', 'mk-admin-theme' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="mk_postbox_header_text"
                            name="mk_admin_theme_options[postbox_header_text]"
                            value="<?php echo esc_attr( mk_admin_theme_get( 'postbox_header_text' ) ); ?>"
                            class="mk-color-picker"
                            data-default-color="#ffffff"
                        />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mk_postbox_header_padding"><?php esc_html_e( 'Padding verticale (px)', 'mk-admin-theme' ); ?></label>
                    </th>
                    <td>
                        <input
                            type="number"
                            id="mk_postbox_header_padding"
                            name="mk_admin_theme_options[postbox_header_padding]"
                            value="<?php echo esc_attr( mk_admin_theme_get( 'postbox_header_padding' ) ); ?>"
                            min="0" max="50" step="1"
                            class="small-text"
                        />
                        <p class="description"><?php esc_html_e( 'Valore predefinito: 6.', 'mk-admin-theme' ); ?></p>
                    </td>
                </tr>
            </table>

            <p style="margin-top:24px;">
                <?php submit_button( __( 'Salva impostazioni', 'mk-admin-theme' ), 'primary', 'submit', false ); ?>
                &nbsp;
                <a href="<?php echo esc_url( add_query_arg( 'mk_reset', '1', admin_url( 'options-general.php?page=mk-admin-theme' ) ) ); ?>"
                   class="button"
                   onclick="return confirm('<?php echo esc_js( __( 'Ripristinare i valori predefiniti?', 'mk-admin-theme' ) ); ?>');">
                    <?php esc_html_e( 'Ripristina predefiniti', 'mk-admin-theme' ); ?>
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
// ACF Custom Styles – subpage
// ──────────────────────────────────────────────────────────────────────────────

function mk_acf_styles_add_page() {
    add_options_page(
        __( 'ACF Custom Styles', 'mk-admin-theme' ),
        __( 'ACF Custom Styles', 'mk-admin-theme' ),
        'manage_options',
        'mk-acf-styles',
        'mk_acf_styles_page'
    );
}
add_action( 'admin_menu', 'mk_acf_styles_add_page' );

function mk_acf_styles_register_settings() {
    register_setting( 'mk_acf_styles_group', 'mk_acf_styles_base',
        [ 'sanitize_callback' => function ( $v ) { return ! empty( $v ) ? '1' : '0'; } ]
    );
    register_setting( 'mk_acf_styles_group', 'mk_acf_styles_radius',
        [ 'sanitize_callback' => function ( $v ) { $n = absint( $v ); return ( $n >= 0 && $n <= 50 ) ? $n : 6; } ]
    );
    register_setting( 'mk_acf_styles_group', 'mk_acf_styles_presets',
        [ 'sanitize_callback' => 'mk_acf_styles_sanitize_presets' ]
    );
}
add_action( 'admin_init', 'mk_acf_styles_register_settings' );

function mk_acf_styles_sanitize_presets( $input ) {
    if ( ! is_array( $input ) ) {
        return [];
    }
    $output = [];
    foreach ( $input as $item ) {
        $slug = sanitize_html_class( $item['slug'] ?? '' );
        if ( ! $slug ) {
            continue;
        }
        $output[] = [
            'slug'         => $slug,
            'acf_label_bg' => sanitize_hex_color( $item['acf_label_bg'] ?? '' ) ?? '',
            'label_bg'     => sanitize_hex_color( $item['label_bg']     ?? '' ) ?? '',
            'field_bg'     => sanitize_hex_color( $item['field_bg']     ?? '' ) ?? '',
            'title_color'  => sanitize_hex_color( $item['title_color']  ?? '' ) ?? '',
            'label_color'  => sanitize_hex_color( $item['label_color']  ?? '' ) ?? '',
            'desc_color'   => sanitize_hex_color( $item['desc_color']   ?? '' ) ?? '',
            'custom_css'   => wp_strip_all_tags( $item['custom_css']    ?? '' ),
        ];
    }
    return $output;
}

/**
 * Inject base ACF overrides + dynamically generated preset classes.
 */
function mk_acf_styles_inject() {
    $base_on = get_option( 'mk_acf_styles_base', '0' );
    $presets  = get_option( 'mk_acf_styles_presets', [] );

    if ( $base_on !== '1' && empty( $presets ) ) {
        return;
    }

    $r = absint( get_option( 'mk_acf_styles_radius', 6 ) );

    echo '<style id="mk-acf-custom-styles">' . "\n";

    // Always inject the radius var so presets can reference it even without base overrides.
    echo ":root { --mk-acf-radius: {$r}px; }\n\n";

    if ( $base_on === '1' ) {
        echo '/* ── MK ACF – base overrides ── */
:root {
    --mk-acf-grey-01: #FAFAFA;
    --mk-acf-grey-02: #EAEAEA;
    --mk-acf-grey-03: #bdbdbd;
}

/* Description */
.acf-field p.description {
    background-color: var(--mk-acf-grey-02);
    padding: 5px;
    border-radius: var(--mk-acf-radius);
}

/* Groups */
.acf-field.acf-field-group { padding: 0; margin: 0; }
.acf-field-group > .acf-label { margin: 0 10px 10px; }

/* .acf-label wrapper */
.acf-field .acf-label {
    border-radius: var(--mk-acf-radius);
}

/* label element */
.acf-field .acf-label label {
    display: inline;
    margin: 0 0 3px;
    padding: 5px;
    background-color: var(--mk-acf-grey-02);
    border-radius: var(--mk-acf-radius);
}

/* Fields */
.acf-fields.-border { border: 0; background: unset; }
.acf-field { border: 0 !important; border-radius: var(--mk-acf-radius) !important; }

/* Repeater */
.acf-repeater .acf-row-handle .acf-icon { margin: 2px 0 0 0; }
.acf-icon.small, .acf-icon.-small { width: 16px; height: 16px; line-height: 0; font-size: 14px; }

td.acf-field.acf-field-text { padding: 4px; }

/* Utility */
.mk-acf-hide { display: none; }
';
    }

    foreach ( $presets as $p ) {
        $s = '.' . $p['slug'];

        // Field background (entire field container).
        if ( $p['field_bg'] ) {
            echo "$s {\n    background-color: {$p['field_bg']} !important;\n    border-radius: var(--mk-acf-radius);\n}\n";
        }

        // .acf-label wrapper background (independently controlled).
        if ( $p['acf_label_bg'] ) {
            echo "$s > .acf-label {\n    background-color: {$p['acf_label_bg']} !important;\n    border-radius: var(--mk-acf-radius);\n    padding: 2px 5px;\n}\n";
        }

        // label element background (independently controlled).
        if ( $p['label_bg'] ) {
            echo "$s > .acf-label label {\n    background-color: {$p['label_bg']} !important;\n    border-radius: var(--mk-acf-radius);\n    padding: 5px;\n    display: inline;\n}\n";
        }

        // Title color: only the direct .acf-label > label (group header).
        if ( $p['title_color'] ) {
            echo "$s > .acf-label label {\n    color: {$p['title_color']} !important;\n}\n";
        }

        // Label color: all nested .acf-label label elements.
        if ( $p['label_color'] ) {
            echo "$s .acf-label label {\n    color: {$p['label_color']} !important;\n}\n";
        }

        // Description color (+ optional background matching .acf-label).
        if ( $p['desc_color'] ) {
            echo "$s .acf-field p.description {\n    color: {$p['desc_color']} !important;\n}\n";
        }
        if ( $p['acf_label_bg'] ) {
            echo "$s .acf-field p.description {\n    background-color: {$p['acf_label_bg']} !important;\n    border-radius: var(--mk-acf-radius);\n}\n";
        }

        // Free-form custom CSS appended properties inside the current class scope.
        if ( ! empty( $p['custom_css'] ) ) {
            echo "$s {\n    " . trim( $p['custom_css'] ) . "\n}\n";
        }

        echo "\n";
    }

    echo '</style>' . "\n";
}
add_action( 'acf/input/admin_head', 'mk_acf_styles_inject' );

/**
 * Settings page HTML.
 */
function mk_acf_styles_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $presets  = get_option( 'mk_acf_styles_presets', [] );
    $base_on  = get_option( 'mk_acf_styles_base', '0' );
    $radius   = get_option( 'mk_acf_styles_radius', 6 );

    $color_cols = [
        'acf_label_bg' => '.acf-label BG',
        'label_bg'     => 'label BG',
        'field_bg'     => __( 'Field BG', 'mk-admin-theme' ),
        'title_color'  => __( 'Colore titolo', 'mk-admin-theme' ),
        'label_color'  => __( 'Colore label', 'mk-admin-theme' ),
        'desc_color'   => __( 'Colore descrizione', 'mk-admin-theme' ),
    ];
    ?>
    <style>
    /* ── ACF Styles page – responsive table ── */
    .mk-acf-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 12px; }
    #mk-acf-presets-table { min-width: 900px; border-collapse: collapse; }
    #mk-acf-presets-table th,
    #mk-acf-presets-table td { vertical-align: top; padding: 6px 8px; white-space: nowrap; }
    #mk-acf-presets-table td.mk-acf-custom-cell { white-space: normal; min-width: 160px; }
    #mk-acf-presets-table .mk-acf-color-picker-wrap .wp-color-result-text { font-size: 0 !important; }
    #mk-acf-presets-table .mk-acf-color-picker-wrap .wp-color-result-text::after { content: '🎨'; font-size: 14px; }
    </style>

    <div class="wrap">
        <h1><?php esc_html_e( 'ACF Custom Styles', 'mk-admin-theme' ); ?></h1>
        <p><?php printf( esc_html__( 'Crea classi CSS da incollare nel campo %s dei gruppi e dei campi ACF. Ogni classe controlla sfondo, colori del testo e della descrizione.', 'mk-admin-theme' ), '<strong>CSS Class</strong>' ); ?></p>

        <form method="post" action="options.php" id="mk-acf-styles-form">
            <?php settings_fields( 'mk_acf_styles_group' ); ?>

            <h2><?php esc_html_e( 'Stili base globali', 'mk-admin-theme' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Attiva override globali ACF', 'mk-admin-theme' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="mk_acf_styles_base" value="1" <?php checked( $base_on, '1' ); ?> />
                            <?php esc_html_e( 'Applica miglioramenti visivi di base a tutti i campi ACF', 'mk-admin-theme' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mk_acf_styles_radius"><?php esc_html_e( 'Raggio bordo globale (px)', 'mk-admin-theme' ); ?></label></th>
                    <td>
                        <input
                            type="number"
                            id="mk_acf_styles_radius"
                            name="mk_acf_styles_radius"
                            value="<?php echo esc_attr( $radius ); ?>"
                            min="0" max="50" step="1"
                            class="small-text"
                        />
                        <p class="description">
                            <?php printf( esc_html__( 'Applicato come %1$s a tutti gli elementi ACF: campi, label wrapper (%2$s), label elemento (%3$s), descrizioni e field container. Default: 6.', 'mk-admin-theme' ), '<code>--mk-acf-radius</code>', '<code>.acf-label</code>', '<code>label</code>' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e( 'Classi personalizzate', 'mk-admin-theme' ); ?></h2>
            <p class="description" style="margin-bottom:12px;">
                <?php printf( esc_html__( 'Le classi generate vengono iniettate nell\'editor ACF. Copia il nome classe e incollalo nel campo %s del gruppo o del campo in ACF.', 'mk-admin-theme' ), '<em>CSS Class</em>' ); ?>
            </p>

            <div class="mk-acf-table-wrap">
            <table class="widefat striped mk-acf-presets-table" id="mk-acf-presets-table">
                <thead>
                    <tr>
                        <th style="width:140px;"><?php esc_html_e( 'Nome classe', 'mk-admin-theme' ); ?></th>
                        <?php foreach ( $color_cols as $col_label ) : ?>
                            <th style="width:80px;"><?php echo esc_html( $col_label ); ?></th>
                        <?php endforeach; ?>
                        <th><?php esc_html_e( 'CSS libero', 'mk-admin-theme' ); ?></th>
                        <th style="width:36px;"></th>
                    </tr>
                </thead>
                <tbody id="mk-acf-presets-body">
                    <?php if ( empty( $presets ) ) : ?>
                        <tr class="mk-acf-empty-row">
                            <td colspan="<?php echo count( $color_cols ) + 3; ?>">
                                <em><?php esc_html_e( 'Nessuna classe. Clicca "+ Aggiungi classe" per iniziare.', 'mk-admin-theme' ); ?></em>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $presets as $i => $p ) : ?>
                        <tr class="mk-acf-preset-row">
                            <td>
                                <input type="text"
                                    name="mk_acf_styles_presets[<?php echo $i; ?>][slug]"
                                    value="<?php echo esc_attr( $p['slug'] ); ?>"
                                    placeholder="<?php esc_attr_e( 'nome-classe', 'mk-admin-theme' ); ?>"
                                    class="widefat mk-acf-slug-input"
                                    style="width:100%"
                                />
                                <small class="mk-acf-slug-preview" style="color:#888;font-size:10px;"><?php echo $p['slug'] ? '.' . esc_html( $p['slug'] ) : ''; ?></small>
                            </td>
                            <?php foreach ( array_keys( $color_cols ) as $col_key ) : ?>
                            <td class="mk-acf-color-picker-wrap">
                                <input type="text"
                                    name="mk_acf_styles_presets[<?php echo $i; ?>][<?php echo esc_attr( $col_key ); ?>]"
                                    value="<?php echo esc_attr( $p[ $col_key ] ?? '' ); ?>"
                                    class="mk-acf-color-picker"
                                    data-default-color=""
                                />
                            </td>
                            <?php endforeach; ?>
                            <td class="mk-acf-custom-cell">
                                <textarea
                                    name="mk_acf_styles_presets[<?php echo $i; ?>][custom_css]"
                                    rows="3"
                                    class="widefat code mk-acf-custom-css"
                                    placeholder="color: red; padding: 10px;"
                                    style="font-size:11px;font-family:monospace;resize:vertical;"
                                ><?php echo esc_textarea( $p['custom_css'] ?? '' ); ?></textarea>
                            </td>
                            <td>
                                <button type="button" class="button mk-acf-remove-row" title="<?php esc_attr_e( 'Rimuovi', 'mk-admin-theme' ); ?>">&#x2715;</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

            <!-- Row template (hidden, cloned by JS) -->
            <script type="text/html" id="mk-acf-row-template">
                <tr class="mk-acf-preset-row">
                    <td>
                        <input type="text"
                            name="mk_acf_styles_presets[__INDEX__][slug]"
                            value=""
                            placeholder="<?php esc_attr_e( 'nome-classe', 'mk-admin-theme' ); ?>"
                            class="widefat mk-acf-slug-input"
                            style="width:100%"
                        />
                        <small class="mk-acf-slug-preview" style="color:#888;font-size:10px;"></small>
                    </td>
                    <?php foreach ( array_keys( $color_cols ) as $col_key ) : ?>
                    <td class="mk-acf-color-picker-wrap">
                        <input type="text"
                            name="mk_acf_styles_presets[__INDEX__][<?php echo esc_attr( $col_key ); ?>]"
                            value=""
                            class="mk-acf-color-picker"
                            data-default-color=""
                        />
                    </td>
                    <?php endforeach; ?>
                    <td class="mk-acf-custom-cell">
                        <textarea
                            name="mk_acf_styles_presets[__INDEX__][custom_css]"
                            rows="3"
                            class="widefat code mk-acf-custom-css"
                            placeholder="color: red; padding: 10px;"
                            style="font-size:11px;font-family:monospace;resize:vertical;"
                        ></textarea>
                    </td>
                    <td>
                        <button type="button" class="button mk-acf-remove-row" title="<?php esc_attr_e( 'Rimuovi', 'mk-admin-theme' ); ?>">&#x2715;</button>
                    </td>
                </tr>
            </script>

            <p style="margin-top:12px;">
                <button type="button" class="button" id="mk-acf-add-row"><?php esc_html_e( '+ Aggiungi classe', 'mk-admin-theme' ); ?></button>
            </p>

            <?php submit_button( __( 'Salva stili', 'mk-admin-theme' ), 'primary', 'submit', false ); ?>
        </form>

        <!-- Live CSS preview -->
        <hr>
        <h2><?php esc_html_e( 'CSS generato', 'mk-admin-theme' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Copia e usa questi selettori come riferimento. Vengono iniettati automaticamente nell\'editor ACF.', 'mk-admin-theme' ); ?></p>
        <textarea id="mk-acf-css-preview" readonly class="large-text code" rows="10" style="font-family:monospace;background:#f6f7f7;"></textarea>
    </div>
    <?php
}

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
        printf( esc_html__( 'Sincronizzazione Elementor attiva ma impossibile leggere i colori: %s', 'mk-admin-theme' ), esc_html( $colors->get_error_message() ) );
        echo '</p></div>';
    }
}
add_action( 'admin_notices', 'mk_admin_theme_elementor_sync_notice' );

// ──────────────────────────────────────────────────────────────────────────────
// Gutenberg – resizable sidebar
// ──────────────────────────────────────────────────────────────────────────────

function mk_admin_theme_sidebar_resize_post_types() {
    $raw = mk_admin_theme_get( 'sidebar_resize_types' );
    return array_filter( array_map( 'trim', explode( ',', $raw ) ) );
}

function mk_admin_theme_sidebar_resize() {
    if ( mk_admin_theme_get( 'sidebar_resize' ) !== '1' ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || $screen->base !== 'post' ) {
        return;
    }

    $types = mk_admin_theme_sidebar_resize_post_types();
    if ( $types && ! in_array( $screen->post_type, $types, true ) ) {
        return;
    }

    $logo = mk_admin_theme_get( 'sidebar_resize_logo' );
    $logo_css = $logo
        ? "background:url('" . esc_url( $logo ) . "') #ffffff;background-size:300px auto;background-position:center;background-repeat:no-repeat;"
        : "background:#ffffff;";
    ?>
    <style id="mk-sidebar-resize-css">
    .interface-interface-skeleton__sidebar .interface-complementary-area,
    .interface-interface-skeleton__sidebar .interface-complementary-area__fill {
        width: 100% !important;
    }
    .edit-post-layout:not(.is-sidebar-opened) .interface-interface-skeleton__sidebar,
    .edit-site-layout:not(.is-sidebar-opened)  .interface-interface-skeleton__sidebar {
        display: none;
    }
    .is-sidebar-opened .interface-interface-skeleton__sidebar { width: 40%; }

    /* Drag overlay shown while resizing */
    .interface-interface-skeleton__sidebar.ui-resizable-resizing {
        position: relative !important;
    }
    .interface-interface-skeleton__sidebar.ui-resizable-resizing::after {
        content: '';
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        <?php echo $logo_css; ?>
    }

    /* jquery-ui resizable handle */
    .ui-resizable-handle { position: absolute; font-size: 0.1px; display: block; touch-action: none; }
    .ui-resizable-w { cursor: w-resize; width: 8px; left: 0; top: 0; height: 100%; background: transparent; }
    </style>

    <script>
    jQuery(window).ready(function () {
        var STORAGE_KEY = 'mk_rs_sidebar_width';

        function applyResize() {
            var sidebar = jQuery('.interface-interface-skeleton__sidebar');
            if ( ! sidebar.length ) { return; }

            var saved = localStorage.getItem(STORAGE_KEY);
            if (saved) { sidebar.width(saved); }

            if ( ! sidebar.hasClass('ui-resizable') ) {
                sidebar.resizable({
                    handles: 'w',
                    resize: function (event, ui) {
                        jQuery(this).css({ left: 0 });
                        localStorage.setItem(STORAGE_KEY, jQuery(this).width());
                    }
                });
            }

            determineSidebarOpen();
        }

        function determineSidebarOpen() {
            var open = false;
            jQuery('.interface-pinned-items button').each(function () {
                if (jQuery(this).hasClass('is-pressed')) { open = true; }
            });
            jQuery('.edit-post-layout, .edit-site-layout').toggleClass('is-sidebar-opened', open);
        }

        // Poll until Gutenberg has rendered the sidebar.
        var poll = setInterval(function () {
            if (jQuery('.interface-interface-skeleton__sidebar').length) {
                clearInterval(poll);
                applyResize();
            }
        }, 300);

        jQuery('body').on('click', '.interface-pinned-items button', function () {
            setTimeout(determineSidebarOpen, 50);
        });

        // Re-apply saved width after post save (Gutenberg re-renders).
        wp.data.subscribe(function () {
            var saving   = wp.data.select('core/editor').isSavingPost();
            var auto     = wp.data.select('core/editor').isAutosavingPost();
            if (saving && !auto) {
                setTimeout(function () {
                    var saved = localStorage.getItem(STORAGE_KEY);
                    if (saved) {
                        jQuery('.interface-interface-skeleton__sidebar').width(saved);
                    }
                }, 800);
            }
        });
    });
    </script>
    <?php
}
add_action( 'admin_head', 'mk_admin_theme_sidebar_resize' );

// ──────────────────────────────────────────────────────────────────────────────
// Gutenberg – title-only mode (disable all blocks + patterns)
// ──────────────────────────────────────────────────────────────────────────────

/**
 * Returns the list of post types that should use title-only mode.
 * Parsed from the comma-separated option value.
 *
 * @return string[]
 */
function mk_admin_theme_title_only_post_types() {
    $raw   = mk_admin_theme_get( 'gutenberg_title_only_types' );
    $types = array_filter( array_map( 'trim', explode( ',', $raw ) ) );
    return $types;
}

/**
 * Disable all blocks for configured post types.
 * Uses allowed_block_types_all (WP 5.8+) with fallback.
 */
function mk_admin_theme_disable_blocks( $allowed_blocks, $editor_context ) {
    if ( mk_admin_theme_get( 'gutenberg_title_only' ) !== '1' ) {
        return $allowed_blocks;
    }

    $post_type = $editor_context->post->post_type ?? '';
    if ( ! $post_type || ! in_array( $post_type, mk_admin_theme_title_only_post_types(), true ) ) {
        return $allowed_blocks;
    }

    return [];
}
add_filter( 'allowed_block_types_all', 'mk_admin_theme_disable_blocks', 10, 2 );

/**
 * Disable core block patterns and remote pattern fetching
 * for the configured post types.
 */
function mk_admin_theme_disable_patterns() {
    if ( mk_admin_theme_get( 'gutenberg_title_only' ) !== '1' ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || $screen->base !== 'post' ) {
        return;
    }

    if ( ! in_array( $screen->post_type, mk_admin_theme_title_only_post_types(), true ) ) {
        return;
    }

    remove_theme_support( 'core-block-patterns' );
    add_filter( 'should_load_remote_block_patterns', '__return_false' );
}
add_action( 'current_screen', 'mk_admin_theme_disable_patterns' );

/**
 * Inject CSS to collapse Gutenberg to a title-only view
 * for configured post types.
 */
function mk_admin_theme_title_only_css() {
    if ( mk_admin_theme_get( 'gutenberg_title_only' ) !== '1' ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || $screen->base !== 'post' ) {
        return;
    }

    if ( ! in_array( $screen->post_type, mk_admin_theme_title_only_post_types(), true ) ) {
        return;
    }
    ?>
    <style id="mk-title-only-css">
    /* ── MK Admin Theme: title-only editor ── */

    /* Collapse the canvas height so only the title shows */
    :root :where(.editor-styles-wrapper)::after { height: unset !important; }
    .editor-visual-editor.edit-post-visual-editor,
    .editor-visual-editor { max-height: 150px; overflow: hidden; }

    /* Title positioning */
    .editor-visual-editor__post-title-wrapper,
    .edit-post-visual-editor__post-title-wrapper { margin-top: 1px !important; }

    /* Hide the block inserter (+) button */
    .editor-document-tools__inserter-toggle { display: none !important; }

    /* Hide the block appender inside the canvas */
    .block-list-appender,
    .block-editor-default-block-appender,
    .block-editor-block-list__insertion-point { display: none !important; }

    /* Hide the slash-command / pattern inserter popover */
    .components-popover.block-editor-inserter__popover { display: none !important; }

    /* Hide the "Block" tab in the sidebar inspector */
    button#tabs-0-edit-post\/block { display: none !important; }

    /* Hide Document tools: command palette, drag handle */
    .editor-document-tools__command-center { display: none !important; }
    </style>
    <?php
}
add_action( 'admin_head', 'mk_admin_theme_title_only_css' );

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
            <span class="mk-toggle-wrap" aria-label="' . esc_attr__( 'Dark / Light mode', 'mk-admin-theme' ) . '" title="' . esc_attr__( 'Dark / Light mode', 'mk-admin-theme' ) . '">
                <span class="mk-toggle-track"><span class="mk-toggle-thumb"></span></span>
                <span class="mk-toggle-label ab-label">' . esc_html__( 'Dark', 'mk-admin-theme' ) . '</span>
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

        var labelLight = <?php echo wp_json_encode( __( 'Light', 'mk-admin-theme' ) ); ?>;
        var labelDark  = <?php echo wp_json_encode( __( 'Dark',  'mk-admin-theme' ) ); ?>;

        function applyMode(dark) {
            body.classList.toggle('mk-dark', dark);
            if (label) label.textContent = dark ? labelLight : labelDark;
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

