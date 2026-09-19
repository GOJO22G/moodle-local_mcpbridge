<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Shared library functions for local_mcpbridge.
 *
 * @package    local_mcpbridge
 * @copyright  2026 AlmaBay Networks Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Seed the moodle_mcp_read / moodle_mcp_write scope names into local_oauth2's
 * own scope catalog, if they don't already exist.
 *
 * Called from both db/install.php (fresh installs) and db/upgrade.php
 * (existing sites updating to a version that introduced this) - Moodle
 * runs install.php ONCE on a fresh install and jumps straight to the
 * current version, skipping every upgrade.php step entirely, so this
 * logic must exist in both places, not just upgrade.php.
 *
 * Safe to call any number of times: guarded by both table_exists() and
 * record_exists(), on top of local_oauth2_scope.scope's own unique key
 * at the DB level.
 *
 * @return void
 */
function local_mcpbridge_seed_oauth_scopes(): void {
    global $DB;

    $dbman = $DB->get_manager();
    $scopetable = new xmldb_table('local_oauth2_scope');

    if (!$dbman->table_exists($scopetable)) {
        debugging('local_mcpbridge: local_oauth2_scope table not found, skipping scope seeding. Is local_oauth2 installed?', DEBUG_DEVELOPER);
        return;
    }

    $requiredscopes = ['moodle_mcp_read', 'moodle_mcp_write'];

    foreach ($requiredscopes as $scopename) {
        if (!$DB->record_exists('local_oauth2_scope', ['scope' => $scopename])) {
            $scoperecord = new stdClass();
            $scoperecord->scope = $scopename;
            $scoperecord->is_default = 0;
            $DB->insert_record('local_oauth2_scope', $scoperecord);
        }
    }
}
