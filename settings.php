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
 * Admin settings for local_mcpbridge.
 *
 * @package    local_mcpbridge
 * @copyright  2026 AlmaBay Networks Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_mcpbridge', 'MCP OAuth Bridge');
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext(
        'local_mcpbridge/serviceid',
        'Web service ID to bridge',
        'The numeric ID of the existing external web service (e.g. your webservice_mcp custom service) that OAuth2 tokens should become valid for. Find this in the URL when editing the service under Site administration > Server > Web services > External services (e.g. .../service.php?id=2 means the ID is 2).',
        '',
        PARAM_INT
    ));
}