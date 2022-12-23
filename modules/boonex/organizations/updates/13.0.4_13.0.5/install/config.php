<?php
/**
 * Copyright (c) UNA, Inc - https://una.io
 * MIT License - https://opensource.org/licenses/MIT
 */

$aConfig = array(
    /**
     * Main Section.
     */
    'title' => 'Organizations',
    'version_from' => '13.0.4',
    'version_to' => '13.0.5',
    'vendor' => 'BoonEx',

    'compatible_with' => array(
        '13.0.0-RC1'
    ),

    /**
     * 'home_dir' and 'home_uri' - should be unique. Don't use spaces in 'home_uri' and the other special chars.
     */
    'home_dir' => 'boonex/organizations/updates/update_13.0.4_13.0.5/',
    'home_uri' => 'orgs_update_1304_1305',

    'module_dir' => 'boonex/organizations/',
    'module_uri' => 'orgs',

    'db_prefix' => 'bx_organizations_',
    'class_prefix' => 'BxOrgs',

    /**
     * Installation/Uninstallation Section.
     */
    'install' => array(
        'execute_sql' => 1,
        'update_files' => 1,
        'update_languages' => 0,
        'clear_db_cache' => 1,
    ),

    /**
     * Category for language keys.
     */
    'language_category' => 'Organizations',

    /**
     * Files Section
     */
    'delete_files' => array(
        'template/css/categories.css',
        'template/css/manage_tools.css',
        'template/css/prices.css',
        'template/account_link.html',
        'template/badges.html',
        'template/category_list_inline.html',
        'template/entry-all-actions.html',
        'template/entry-location.html',
        'template/entry-share.html',
        'template/entry-text.html',
        'template/favorite-list-info.html',
        'template/favorite-lists.html',
        'template/form_categories.html',
        'template/form_ghost_template.html',
        'template/image_popup.html',
        'template/menu_main_submenu.html',
        'template/name_link.html',
        'template/picture_preview.html',
        'template/popup_invite.html',
        'template/popup_price.html',
        'template/search_extended_results.html',
        'template/set_acl_popup.html',
        'template/set_role_popup.html',
        'template/uploader_form_crop_cover.html',
    ),
);
