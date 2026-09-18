# local_mcpbridge

Bridges `local_oauth2` access tokens into Moodle webservice tokens, so a
student or staff member who logs in through OAuth (e.g. via a Gemini or
Claude connector) automatically gets a working Moodle webservice token,
scoped to whatever read/write permission was granted at login - without
an admin ever having to generate a token manually.

## What it does

1. **Auto-creates its own external service on install** (`db/services.php`),
   named "MCP Bridge Service" (shortname `mcpbridge_service`), pre-populated
   with the full set of Moodle functions this bridge exposes. No manual
   admin step (Site administration > Server > Web services > External
   services > Functions) is required on a fresh install.

2. **Listens for `local_oauth2`'s `access_token_created`/`access_token_updated`
   events** (`classes/observers.php`) and, for each one:
   - Mirrors the OAuth token into a new Moodle webservice token
     (`external_tokens`), scoped to the service above.
   - Records the OAuth scope that was granted (e.g. `moodle_mcp_read`,
     `moodle_mcp_write`) against that new token, in this plugin's own
     `local_mcpbridge_token_scope` table.

3. **Auto-grants the `webservice/mcp:use` capability** to the Authenticated
   user role on install (`db/install.php`), so any logged-in user can use
   the bridged token without a separate manual permission step.

## Why the scope-tracking table exists

The webservice token created by this plugin is a *different* token string
from the original OAuth token `local_oauth2` issued - they are not
interchangeable, and matching one against the other by value does not
work. `local_mcpbridge_token_scope` exists specifically so that
`webservice_mcp` (or any other consumer) can look up "what was this
*bridged* token actually granted" without needing to know anything about
`local_oauth2`'s own internal token storage.

## Settings

**Site administration > Plugins > Local plugins > MCP OAuth Bridge**

- **Web service ID to bridge (optional override)** - leave blank to use
  the auto-created "MCP Bridge Service". Only set this to bridge tokens
  into a different, already-existing external service instead.

## Dependencies

- `local_oauth2` - source of the access token events this plugin observes.
- Intended to be paired with `webservice_mcp`, which is what actually
  enforces read/write permission based on the scope this plugin records
  (see that plugin's `enforce_scope()`), though this plugin has no direct
  code dependency on it.

## Known limitations

- The scope-tracking table only ever contains scope for tokens created via
  the OAuth bridge. A manually-generated webservice token (not created
  through this plugin) has no corresponding row - callers that check scope
  should treat "no row found" as "not verified", not "fully trusted".
