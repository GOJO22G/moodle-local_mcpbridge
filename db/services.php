<?php
// This file is part of Moodle - http://moodle.org/
//
// local_mcpbridge - external service definition.
// On plugin install/upgrade, Moodle reads this file automatically and
// creates (or updates) the declared service with its function list
// already attached - no manual admin UI step required.

defined('MOODLE_INTERNAL') || die();

$functions = [
    // No new functions are defined by this plugin itself - everything
    // below already exists in Moodle core or other mod/block plugins.
    // This array intentionally stays empty.
];

// ---------------------------------------------------------------------
// The function list is NOT defined here. It is read directly from
// webservice_mcp\local\approved_functions::LIST - the single canonical
// list also used by that plugin's own tools/list filter and its
// enforce_scope() execution gate. Keeping one array in one place means
// there is no second, separately-maintained copy that could drift out
// of sync with what webservice_mcp actually allows.
//
// To add/remove a function: edit approved_functions::LIST in
// webservice_mcp/classes/local/approved_functions.php - NOT here.
//
// IMPORTANT: after that list changes, bump $plugin->version in this
// plugin's version.php. Moodle only re-syncs a plugin's declared service
// when it detects a version change and runs the upgrade step - the list
// changing alone, with no version bump here, has no effect on an
// already-installed site.
// ---------------------------------------------------------------------
$mcpbridge_functions = \webservice_mcp\local\approved_functions::LIST;

$services = [
    'MCP Bridge Service' => [
        'functions'       => $mcpbridge_functions,
        'restrictedusers' => 0,   // any authorised user's bridged token works - no per-user manual authorisation step
        'enabled'         => 1,   // on immediately after install, no manual toggle
        'shortname'       => 'mcpbridge_service',
        'downloadfiles'   => 1,   // matches earlier "Can download files" fix needed for resource extraction
        'uploadfiles'     => 0,
    ],
];
