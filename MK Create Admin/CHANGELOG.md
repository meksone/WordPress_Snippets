# Changelog — mk-create-admin

## [1.0.3] - 2026-05-08
- Fix activation URL in header comment — must include filename, not just query string

## [1.0.2] - 2026-05-08
- Remove debug file_put_contents line added in 1.0.1

## [1.0.1] - 2026-05-08
- Add debug logging to mk-debug.txt for troubleshooting server execution

## [1.0.0] - 2026-05-08
- Initial release
- Password-protected admin user creation script
- Auto-detects wp-load.php up to 3 levels up
- Self-deletes after successful execution
