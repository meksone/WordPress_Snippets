/* mk-admin-theme – settings.js
 * Initialises the WordPress colour picker on all .mk-color-picker inputs.
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        var options = {
            // Live preview: inject a temporary <style> tag whenever a colour changes.
            change: function (event, ui) {
                mkAdminThemeLivePreview();
            },
            clear: function () {
                mkAdminThemeLivePreview();
            }
        };

        // Use Elementor palette as swatches if available.
        if (
            typeof mkAdminTheme !== 'undefined' &&
            mkAdminTheme.elementorPalette &&
            mkAdminTheme.elementorPalette.length > 0
        ) {
            options.palettes = mkAdminTheme.elementorPalette;
        }

        $('.mk-color-picker').wpColorPicker(options);
    });

    function mkAdminThemeLivePreview() {
        // Build a minimal CSS var block from the current picker values so the
        // settings page itself reflects changes before saving.
        var vars = ':root {';
        var map = {
            'mk_bg_base':                   '--mk-bg-base',
            'mk_color_primary':             '--mk-color-primary',
            'mk_color_accent':              '--mk-color-accent',
            'mk_color_accent_text':         '--mk-color-accent-text',
            'mk_bg_topbar':                 '--mk-bg-topbar',
            'mk_text_topbar':               '--mk-text-topbar',
            'mk_bg_menu':                   '--mk-bg-menu',
            'mk_bg_menu_hover':             '--mk-bg-menu-hover',
            'mk_bg_menu_current':           '--mk-bg-menu-current',
            'mk_bg_menu_current_hover':    '--mk-bg-menu-current-hover',
            'mk_text_menu':                 '--mk-text-menu',
            'mk_text_menu_current':         '--mk-text-menu-current',
            'mk_color_link':                '--mk-color-link',
            'mk_color_button_primary_bg':   '--mk-btn-primary-bg',
            'mk_color_button_primary_text': '--mk-btn-primary-text',
            'mk_color_button_secondary_bg': '--mk-btn-secondary-bg',
            'mk_color_button_secondary_text':'--mk-btn-secondary-text',
        };

        $.each(map, function (inputId, cssVar) {
            var val = $('#' + inputId).val();
            if (val) {
                vars += cssVar + ':' + val + ';';
            }
        });

        vars += '}';

        var styleTag = document.getElementById('mk-live-preview');
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = 'mk-live-preview';
            document.head.appendChild(styleTag);
        }
        styleTag.textContent = vars;
    }

}(jQuery));
