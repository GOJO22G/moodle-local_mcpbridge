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
 * English language strings for local_mcpbridge.
 *
 * @package    local_mcpbridge
 * @copyright  2026 AlmaBay Networks Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'MCP OAuth Bridge';
$string['settingspagetitle'] = 'MCP OAuth Bridge';
$string['serviceidname'] = 'Web service ID to bridge (optional override)';
$string['serviceiddesc'] = 'Leave blank to use the "MCP Bridge Service" this plugin creates automatically on install. Only set this if you want OAuth tokens bridged into a different, already-existing external service instead - enter its numeric ID, found in the URL when editing the service under Site administration > Server > Web services > External services (e.g. .../service.php?id=2 means the ID is 2).';

$string['task_cleanup_orphaned_scope'] = 'Clean up orphaned MCP bridge token scope records';
