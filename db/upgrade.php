<?php
// This file is part of Moodle - http://moodle.org/
//
// local_mcpbridge upgrade steps.

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade steps for local_mcpbridge.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool
 */
function xmldb_local_mcpbridge_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026091304) {
        $table = new xmldb_table('local_mcpbridge_token_scope');

        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('token', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->add_field('scope', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
            $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_index('token', XMLDB_INDEX_UNIQUE, ['token']);

            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026091304, 'local', 'mcpbridge');
    }

    if ($oldversion < 2026091308) {
        // Seed the two scope names this plugin relies on into local_oauth2's
        // own scope catalog, so a fresh install of local_mcpbridge does not
        // depend on an admin manually inserting these via SQL or a UI local_oauth2
        // does not actually expose for this purpose. Safe to run more than once:
        // local_oauth2_scope.scope has a unique key at the DB level, and we also
        // check record_exists() first so no error is ever thrown either way.
        $scopetable = new xmldb_table('local_oauth2_scope');

        if ($dbman->table_exists($scopetable)) {
            $requiredscopes = ['moodle_mcp_read', 'moodle_mcp_write'];

            foreach ($requiredscopes as $scopename) {
                if (!$DB->record_exists('local_oauth2_scope', ['scope' => $scopename])) {
                    $scoperecord = new stdClass();
                    $scoperecord->scope = $scopename;
                    $scoperecord->is_default = 0;
                    $DB->insert_record('local_oauth2_scope', $scoperecord);
                }
            }
        } else {
            debugging('local_mcpbridge upgrade: local_oauth2_scope table not found, skipping scope seeding. Is local_oauth2 installed?', DEBUG_DEVELOPER);
        }

        upgrade_plugin_savepoint(true, 2026091308, 'local', 'mcpbridge');
    }

    return true;
}
