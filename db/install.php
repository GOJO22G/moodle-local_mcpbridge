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
 * Post-install hook: auto-grants the webservice/mcp:use capability
 * to the Authenticated user role, so a fresh install needs no manual
 * capability assignment. The external service this plugin bridges
 * OAuth tokens into is created separately, by db/services.php.
 *
 * @package    local_mcpbridge
 * @copyright  2026 AlmaBay Networks Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_mcpbridge_install() {
    global $DB;

    $role = $DB->get_record('role', ['archetype' => 'user'], '*', IGNORE_MISSING);

    if (!$role) {
        debugging('local_mcpbridge install: could not find Authenticated user role, skipping auto-grant', DEBUG_DEVELOPER);
    } else {
        $context = context_system::instance();

        assign_capability(
            'webservice/mcp:use',
            CAP_ALLOW,
            $role->id,
            $context->id,
            true
        );

        $context->mark_dirty();
    }

    // Service creation is handled by db/services.php (Moodle's own
    // external_update_services() mechanism), which correctly sets
    // 'component' so the service is recognised as owned by this plugin.
    // Creating it here too caused a collision on fresh installs: this
    // hook would insert the service first (with no component set),
    // then db/services.php would find a shortname collision against a
    // service it doesn't recognise as its own.

    // Seed the moodle_mcp_read/write scope names local_oauth2 needs to
    // know about. This must happen here too, not just in db/upgrade.php -
    // Moodle runs install.php ONCE on a fresh install and jumps straight
    // to the current version, skipping every upgrade.php step entirely,
    // so upgrade.php's own seeding step would never run for a new install.
    require_once(__DIR__ . '/../lib.php');
    local_mcpbridge_seed_oauth_scopes();

    return true;
}
