# Changelog – MK Admin Theme

## [1.0.18] – 2026-04-05
### Fixed
- ACF toolbar background: moved output to `admin_footer` (body end) so the `<style>` block physically appears after all `<head>` stylesheets; added `body.acf-admin-page` specificity prefix to match/beat ACF PRO's own two-class selectors

## [1.0.17] – 2026-04-05
### Added
- ACF admin toolbar (`.acf-admin-toolbar`) now inherits theme colors: background from `--mk-bg-topbar`, tab text from `--mk-text-topbar`, active tab uses accent color, hover uses menu hover; dropdown panel also themed; injected via `acf/input/admin_head` and `acf/field_group/admin_head`

## [1.0.16] – 2026-04-05
### Fixed
- Postbox header on ACF pages: switched from WordPress `admin_head` (which still loses to ACF's own inline styles) to ACF's dedicated hooks `acf/input/admin_head` and `acf/field_group/admin_head`, which fire after ACF outputs its own CSS
- On ACF pages the override CSS now uses `.acf-admin-page #poststuff` specificity prefix to match ACF's own selector weight, ensuring our `!important` declarations beat ACF's `color: #344054 !important` on inner `h2`/`h3` elements

## [1.0.15] – 2026-04-05
### Fixed
- Postbox header text color now explicitly targets inner `h2`/`h3`/`.hndle`/`span` elements — ACF sets `color: #344054 !important` directly on the `h2` inside `.postbox-header`; a `color` on the parent container can never override `!important` on a child, so explicit child selectors with `!important` are required
- Inner `h2`/`h3` padding and margin reset to `0` to avoid ACF's per-element padding rules shifting the layout

## [1.0.14] – 2026-04-05
### Fixed
- Postbox header: moved styling to `admin_head` at priority 9999 — guarantees the `<style>` block is injected after all enqueued stylesheets (including ACF's), fixing the header colour on ACF field group editor and other plugin screens where specificity/load-order battles couldn't be won from an enqueued stylesheet
- Admin bar: removed `#wpadminbar *` wildcard from the text-color rule — it was forcing all elements inside the admin bar (including JAMP popup notes) to the topbar text colour; now only `.ab-item` and `a` are targeted

## [1.0.13] – 2026-04-05
### Fixed
- Postbox header `background` and `color` now use `!important` on the base rule (not only on hover) — fixes ACF field group editor and other plugin screens where ACF's stylesheet was loaded after ours and overriding the colors

## [1.0.12] – 2026-04-03
### Added
- New **Intestazione Postbox** section in settings page: color pickers for header background and text color, plus a numeric input for vertical padding
- `--mk-postbox-header-bg`, `--mk-postbox-header-text`, `--mk-postbox-header-padding` CSS vars added as static fallback defaults in `admin-style.css` `:root` block

### Fixed
- Postbox header: all hardcoded `#fff` / `var(--mk-color-primary)` / `10px` replaced with the new `--mk-postbox-header-*` CSS variables (header bg, text color, padding) — fully customizable, no hardcoded values remaining
- `#wpfooter` hardcoded `color: #666` replaced with `var(--mk-text-content-muted)`
- `.postbox` border replaced from hardcoded `#dde0e4` to `var(--mk-border-color)`
- `sanitize`: `postbox_header_padding` now handled as numeric (like `border_radius`) via `absint()`

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
