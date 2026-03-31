# Changelog – MK Admin Theme

## [1.0.11] – 2026-03-29
### Fixed
- ACF Custom Styles: Fixed the real-time CSS preview in `settings.js` on the options page so the `custom_css` field is properly wrapped in the class selector, matching the actual generated output.

## [1.0.10] – 2026-03-29
### Changed
- ACF Custom Styles: The `custom_css` free text field properties are now automatically wrapped inside the current class selector (`.classname { ... }`) to avoid generating invalid un-scoped CSS globally.
- ACF Custom Styles: Updated the placeholder in the `custom_css` field to show an example of CSS properties instead of a full CSS rule.

## [1.0.9] – 2026-03-29
### Added
- Full i18n support: `Text Domain: mk-admin-theme`, `load_plugin_textdomain()`, all visible strings wrapped in `__()` / `esc_html_e()` / `esc_html__()` — ready for Poedit
- `custom_css` free-text column in ACF preset table for per-class manual CSS overrides
- CSS preview in `settings.js` now includes free-form custom CSS block per preset

### Changed
- All `border-radius` in ACF preset generation and base overrides now apply to all corners (removed asymmetric `6px 6px 0 0` variant)
- Color picker "Select color" label replaced with 🎨 emoji via `wp_color_picker` `l10n` option (localizable via Poedit)
- ACF preset table wrapped in `<div class="mk-acf-table-wrap">` with `overflow-x: auto` for responsive horizontal scroll
- Table uses `min-width: 900px` and per-column `width` constraints for compact layout
- `settings.js` refactored: shared picker options, i18n via `mkAdminTheme.i18n`, custom CSS textarea and radius input wired to live preview
- `wp_localize_script` now passes `i18n` object and `isAcfPage` flag
- Dark mode toggle labels (`Dark` / `Light`) localizable via `wp_json_encode(__(...))` in inline JS
- `mk_admin_theme_admin_scripts` moved to a single `$is_theme_page` / `$is_acf_page` check

## [1.0.8] – 2026-03-29
### Changed
- ACF preset columns: `header_bg` split into two independent pickers — `.acf-label BG` (wrapper div) and `label BG` (inner `label` element) — each targeted separately in generated CSS
- All hardcoded `border-radius: 6px` in base overrides and preset generation replaced with `var(--mk-acf-radius)`
- `--mk-acf-radius` injected as `:root` CSS variable on every page load (always, not only when base overrides are on)
### Added
- Global border radius field (`mk_acf_styles_radius`, default 6) in the base settings table; applies uniformly to `.acf-label`, `label`, `.acf-field`, `p.description`, and field containers
- Live CSS preview in `settings.js` now reads the radius input and includes it in the `:root` block

## [1.0.7] – 2026-03-29
### Added
- New subpage **Settings > ACF Custom Styles** (`mk-acf-styles`)
- Dynamic preset builder: add/remove CSS classes with per-class controls for header background, field background, title color, label color, description color
- Generated CSS injected via `acf/input/admin_head` — paste the class name into ACF's "CSS Class" field
- Toggle to enable base global ACF style overrides (field borders, label styling, description background, repeater icon sizing)
- Live CSS preview textarea updates in real-time as colors are picked or slug is typed
- Slug preview shows `.classname` below the input as you type
- Color pickers on the ACF styles page also use the Elementor palette swatches (if available)
- `wp_enqueue_media()` and `wp-color-picker` now shared between both settings pages

## [1.0.6] – 2026-03-29
### Added
- Resizable Gutenberg sidebar integration (drag from left edge, width persisted in `localStorage`)
- New settings section "Sidebar Gutenberg ridimensionabile": on/off toggle, post type list, and logo overlay URL with media picker button
- Logo shown as overlay on the sidebar while dragging; configurable via the WP media library
- `jquery-ui-resizable` enqueued only on post edit screens when the feature is enabled
- Polling strategy to attach resizable after Gutenberg finishes rendering; width restored after post save
- `wp_enqueue_media()` added to settings page for the media picker button
- Media uploader JS added to `settings.js`

## [1.0.5] – 2026-03-29
### Added
- Gutenberg title-only mode: disable all blocks and patterns for configurable post types
- New settings section "Gutenberg" with toggle + comma-separated post type field
- `allowed_block_types_all` filter returns empty array for matching post types (WP 5.8+)
- `remove_theme_support('core-block-patterns')` + `should_load_remote_block_patterns` filter to suppress all patterns
- Admin CSS collapses Gutenberg canvas to title-only view; uses stable class selectors instead of fragile React-generated IDs

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
