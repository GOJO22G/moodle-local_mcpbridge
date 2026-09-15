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
 * to the Authenticated user role, so bridged tokens work without a
 * manual per-student capability grant.
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
        return true;
    }

    $context = context_system::instance();

    assign_capability(
        'webservice/mcp:use',
        CAP_ALLOW,
        $role->id,
        $context->id,
        true
    );

    $context->mark_dirty();

    return true;
}
