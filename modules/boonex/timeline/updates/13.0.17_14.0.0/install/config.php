<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Timeline',
    'version_from' => '13.0.17',
    'version_to' => '14.0.0',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '14.0.0-A1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/timeline/updates/update_13.0.17_14.0.0/',
    'home_uri' => 'timeline_update_13017_1400',

    'module_dir' => 'boonex/timeline/',
    'module_uri' => 'timeline',

    'db_prefix' => 'bx_timeline_',
    'class_prefix' => 'BxTimeline',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 1,
        'clear_db_cache' => 1,
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Timeline',

    /**
     * Files Section
     */
    'delete_files' => array(),
);
