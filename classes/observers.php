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
     * Resolve which external service this plugin should bridge tokens into.
     *
     * Prefers an explicit admin-configured serviceid (backward compatible
     * with existing installs), and falls back to the service auto-created
     * on install (see db/install.php) when no override is set.
     */
    private static function resolve_service_id() {
        global $DB;

        $configured = get_config('local_mcpbridge', 'serviceid');
        if (!empty($configured) && $DB->record_exists('external_services', ['id' => $configured])) {
            return $configured;
        }

        $service = $DB->get_record('external_services', ['shortname' => 'mcpbridge_service']);
        return $service ? $service->id : null;
    }

    /**
     * Fires when local_oauth2 creates or updates an access token for a user.
     * We mirror that same token string into external_tokens, scoped to
     * whichever web service this plugin is configured to bridge (see
     * settings.php) - so the OAuth token becomes a valid wstoken for that
     * service, without needing a separate admin-generated token per user.
     */
    public static function handle_access_token_created_or_updated($event) {
        global $DB;

        // Ensure the moodle_mcp_read/write scope names exist in local_oauth2's
        // own catalog before anything below tries to use them. Deliberately
        // NOT done in db/install.php/db/upgrade.php: Moodle does not guarantee
        // install order between plugins, so on a fresh site local_mcpbridge
        // could install before local_oauth2's own table even exists. This event
        // can only fire once local_oauth2 is already active, so that table is
        // guaranteed to exist here.
        require_once(__DIR__ . '/../lib.php');
        local_mcpbridge_seed_oauth_scopes();

        $data = $event->get_data();
        $userid     = $data['userid'];
        $token      = $data['other']['accesstoken'];
        $validuntil = $data['other']['expires'];
        $scope      = $data['other']['scope'] ?? '';

        $externalserviceid = self::resolve_service_id();
        if (empty($externalserviceid)) {
            debugging('local_mcpbridge: no serviceid configured or auto-created, skipping token bridge', DEBUG_DEVELOPER);
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

        // Record the OAuth scope granted for this bridged token, so
        // webservice_mcp can enforce read/write permissions later without
        // needing to match against local_oauth2's own token table (which
        // uses a different token string entirely).
        $scoperow = $DB->get_record('local_mcpbridge_token_scope', ['token' => $token]);

        if ($scoperow) {
            $scoperow->scope = $scope;
            $scoperow->timecreated = time();
            $DB->update_record('local_mcpbridge_token_scope', $scoperow);
        } else {
            $scoperecord = new \stdClass();
            $scoperecord->token = $token;
            $scoperecord->scope = $scope;
            $scoperecord->timecreated = time();
            $DB->insert_record('local_mcpbridge_token_scope', $scoperecord);
        }
    }
}
