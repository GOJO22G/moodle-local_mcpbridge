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
// SINGLE SOURCE OF TRUTH for what the MCP Bridge Service exposes.
// To add/remove a function: edit ONLY this array, in the matching
// category block below. Do not touch the $services block further down -
// it just references this list.
//
// IMPORTANT: after editing this list, bump $plugin->version in
// version.php. Moodle only re-syncs a plugin's declared service
// (adds/removes functions on the real external_services/
// external_functions tables) when it detects a version change and
// runs the upgrade step - editing this file alone, with no version
// bump, has no effect on an already-installed site.
// ---------------------------------------------------------------------
$mcpbridge_functions = [
    // Site
    'core_webservice_get_site_info',

    // Courses & Catalog
    'core_course_get_courses_by_field',
    'core_course_get_categories',
    'core_course_search_courses',
    'core_course_get_contents',
    'core_course_get_course_module',
    'core_course_get_course_module_by_instance',
    'core_course_get_updates_since',
    'core_enrol_get_users_courses',
    'core_course_get_enrolled_courses_by_timeline_classification',
    'core_course_get_enrolled_courses_with_action_events_by_timeline_classification',
    'core_course_get_recent_courses',

    // Groups
    'core_group_get_course_groups',
    'core_group_get_course_user_groups',
    'core_group_get_group_members',

    // Quizzes (view-only)
    'mod_quiz_get_quizzes_by_courses',
    'mod_quiz_get_quiz_access_information',
    'mod_quiz_get_user_attempts',
    'mod_quiz_get_attempt_data',
    'mod_quiz_get_attempt_summary',
    'mod_quiz_get_attempt_review',
    'mod_quiz_get_combined_review_options',
    'mod_quiz_get_user_best_grade',

    // Forums (view-only)
    'mod_forum_get_forums_by_courses',
    'mod_forum_get_forum_discussions',
    'mod_forum_get_discussion_posts',

    // Calendar & Notifications
    'core_calendar_get_action_events_by_timesort',
    'core_calendar_get_action_events_by_course',
    'core_calendar_get_action_events_by_courses',
    'core_calendar_get_calendar_upcoming_view',
    'core_fetch_notifications',
    'message_popup_get_unread_popup_notification_count',
    'core_message_get_unread_notification_count',

    // Completion & Progress (self only)
    'core_completion_get_activities_completion_status',
    'core_completion_get_course_completion_status',

    // Badges
    'core_badges_get_user_badges',

    // Files
    'core_files_get_files',

    // Lesson
    'mod_lesson_get_lessons_by_courses',
    'mod_lesson_get_user_attempt',

    // Glossary
    'mod_glossary_get_entries_by_search',

    // Resources
    'mod_page_get_pages_by_courses',
    'mod_url_get_urls_by_courses',

    // Blocks
    'block_recentlyaccesseditems_get_recent_items',
    'block_starredcourses_get_starred_courses',

    // Utility
    'core_get_user_dates',
    'core_get_string',
    'core_get_strings',
    'core_get_component_strings',
    'core_user_view_user_profile',

    // Assignments (view-only)
    'mod_assign_get_assignments',
    'mod_assign_get_submission_status',

    // Write functions (validated end-to-end in earlier testing via TO_FETCH_DATA)
    'mod_forum_add_discussion',
    'core_course_create_courses',
    'enrol_manual_enrol_users',
];

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
