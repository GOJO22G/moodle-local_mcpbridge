# Changelog

All notable changes to `local_mcpbridge` are documented here.

## 2026-09-18

### Fixed
- Removed a duplicate service-creation path in `db/install.php` that
  raced against `db/services.php`'s own creation of the same service.
  On a genuinely fresh install, `install.php` would create the service
  first (with no owning component set), then `db/services.php` would
  hit a shortname collision against a service it doesn't recognise as
  its own, and fail. `db/services.php` is now the sole creator;
  `install.php` only handles the capability grant it always needed to
  do regardless.
- Added `lang/en/local_mcpbridge.php`. The plugin previously had no
  `lang/` directory at all, and `settings.php` hardcoded its page
  title, setting name, and setting description directly in PHP instead
  of going through `get_string()`.

### Added
- `db/services.php`: auto-declares the "MCP Bridge Service" external
  service with 54 functions (51 read-only, 3 write) on plugin
  install/upgrade, so the service and its functions exist automatically
  with no manual admin step.
- `local_mcpbridge_token_scope` database table (`db/install.xml`,
  `db/upgrade.php`) and corresponding write in `classes/observers.php`:
  records the OAuth scope granted at the moment a token is bridged, so
  consumers (e.g. `webservice_mcp`) can look up a bridged token's
  granted permissions without needing to match against `local_oauth2`'s
  own token table (which uses a different token string entirely).
- `README.md`, `CHANGELOG.md`.

## Earlier

- Initial implementation: OAuth-to-webservice token bridging via
  `classes/observers.php`, capability auto-grant via `db/install.php`.
