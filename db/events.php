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
 * Registers observers for local_oauth2's access token events.
 * Whenever a student/teacher completes OAuth login and gets an access
 * token, we catch that event and mirror the token into external_tokens
 * so it becomes valid for calling our MCP web service directly.
 *
 * @package    local_mcpbridge
 * @copyright  2026 AlmaBay Networks Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\local_oauth2\event\access_token_created',
        'callback'  => '\local_mcpbridge\observers::handle_access_token_created_or_updated',
        'priority'  => 200,
        'internal'  => true,
    ],
    [
        'eventname' => '\local_oauth2\event\access_token_updated',
        'callback'  => '\local_mcpbridge\observers::handle_access_token_created_or_updated',
        'priority'  => 200,
        'internal'  => true,
    ],
];