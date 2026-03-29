# Changelog – MK Admin Theme

## [1.0.4] – 2026-03-29
### Fixed
- Postbox headers in Gutenberg editor no longer flash grey on hover; WP/Gutenberg hover rule overriding our `background` is suppressed with `!important`
- Separate `.hndle` text-node hover (Gutenberg) neutralised so the entire header stays uniform on mouse-over

## [1.0.3] – 2026-03-29
### Fixed
- Elementor palette passed to Iris color pickers now excludes transparent colors (`rgba`, `hsla`, 8-digit hex) — Iris does not support transparency and would render them incorrectly

## [1.0.2] – 2026-03-29
### Fixed
- Iris color pickers on the admin theme settings page now show the Elementor palette as swatches instead of Iris defaults
- Elementor colors are passed to `settings.js` via `wp_localize_script()` and forwarded as `palettes` to `wpColorPicker()` — falls back to Iris defaults gracefully when Elementor is unavailable

## [1.0.1] – 2026-03-29
### Added
- Elementor palette sync integration (Gutenberg + ACF color picker)
- On/off toggles in Settings > Impostazioni tema admin under new "Integrazioni" section
- `mk_admin_theme_get_elementor_colors()` reads system + custom colors from active Elementor kit
- Fallback to default WP colors if Elementor is unavailable
- Admin notice when sync is enabled but Elementor cannot be reached
- All sync functions prefixed `mk_admin_theme_` to avoid conflicts with standalone snippets

## [1.0.0] – initial release
### Added
- Custom WP admin theme with Poppins font, rounded corners, blue/yellow palette
- Fully customizable color palette via Settings > Impostazioni tema admin
- CSS custom properties injected dynamically from saved options
- Dark / Light mode toggle in admin bar with localStorage persistence and anti-FOUC script
- Styles for admin bar, sidebar menu, content area, tables, buttons, forms, postboxes, login page, Gutenberg
- Reset to defaults button
