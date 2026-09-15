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
 * Event observer that mirrors local_oauth2 access tokens into
 * external_tokens, so an OAuth token becomes a valid wstoken.
 *
 * @package    local_mcpbridge
 * @copyright  2026 AlmaBay Networks Pvt. Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mcpbridge;

defined('MOODLE_INTERNAL') || die();

class observers {

    /**
     * Fires when local_oauth2 creates or updates an access token for a user.
     * We mirror that same token string into external_tokens, scoped to
     * whichever web service this plugin is configured to bridge (see
     * settings.php) - so the OAuth token becomes a valid wstoken for that
     * service, without needing a separate admin-generated token per user.
     */
    public static function handle_access_token_created_or_updated($event) {
        global $DB;

        $data = $event->get_data();
        $userid     = $data['userid'];
        $token      = $data['other']['accesstoken'];
        $validuntil = $data['other']['expires'];

        // Which web service this bridges to - configured in plugin settings
        // as a numeric external_services.id, since the shortname field on
        // an existing service can't always be edited after creation.
        $externalserviceid = get_config('local_mcpbridge', 'serviceid');
        if (empty($externalserviceid)) {
            debugging('local_mcpbridge: no serviceid configured, skipping token bridge', DEBUG_DEVELOPER);
            return;
        }

        if (!$DB->record_exists('external_services', ['id' => $externalserviceid])) {
            debugging("local_mcpbridge: service id '$externalserviceid' not found, skipping token bridge", DEBUG_DEVELOPER);
            return;
        }

        $existing = $DB->get_record('external_tokens', [
            'token' => $token,
            'externalserviceid' => $externalserviceid,
        ]);

        if ($existing) {
            $existing->token = $token;
            $existing->validuntil = $validuntil;
            $existing->timecreated = time();
            $DB->update_record('external_tokens', $existing);
        } else {
            $record = new \stdClass();
            $record->token = $token;
            $record->tokentype = EXTERNAL_TOKEN_PERMANENT;
            $record->userid = $userid;
            $record->externalserviceid = $externalserviceid;
            $record->contextid = \context_system::instance()->id;
            $record->creatorid = $userid;
            $record->validuntil = $validuntil;
            $record->timecreated = time();
            $record->iprestriction = '';
            $DB->insert_record('external_tokens', $record);
        }
    }
}