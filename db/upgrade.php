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

    // A previous version of this file had an upgrade step here (version
    // 2026091308) that seeded moodle_mcp_read/write scope names directly.
    // Removed: that logic is order-fragile the same way db/install.php's
    // copy was (Moodle does not guarantee this plugin upgrades/installs
    // after local_oauth2), so it now lives in classes/observers.php
    // instead, run on every OAuth login rather than at install/upgrade
    // time. See lib.php's local_mcpbridge_seed_oauth_scopes().

    return true;
}
