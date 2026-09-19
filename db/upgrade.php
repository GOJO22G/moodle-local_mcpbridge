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
        // Seeding logic lives in lib.php (local_mcpbridge_seed_oauth_scopes),
        // shared with db/install.php so fresh installs and upgrades both
        // seed the same way from one place, not two copies that could drift.
        require_once(__DIR__ . '/../lib.php');
        local_mcpbridge_seed_oauth_scopes();

        upgrade_plugin_savepoint(true, 2026091308, 'local', 'mcpbridge');
    }

    return true;
}
