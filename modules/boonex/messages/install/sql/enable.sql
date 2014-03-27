
-- SETTINGS

SET @iTypeOrder = (SELECT MAX(`order`) FROM `sys_options_types` WHERE `group` = 'modules');
INSERT INTO `sys_options_types`(`group`, `name`, `caption`, `icon`, `order`) VALUES 
('modules', 'bx_messages', '_bx_msg', 'bx_messages@modules/boonex/messages/|std-mi.png', IF(ISNULL(@iTypeOrder), 1, @iTypeOrder + 1));
SET @iTypeId = LAST_INSERT_ID();

INSERT INTO `sys_options_categories` (`type_id`, `name`, `caption`, `order`)
VALUES (@iTypeId, 'bx_messages', '_bx_msg', 1);
SET @iCategId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`name`, `value`, `category_id`, `caption`, `type`, `check`, `check_error`, `extra`, `order`) VALUES
('bx_messages_per_page_browse', '12', @iCategId, '_bx_msg_option_per_page_browse', 'digit', '', '', '', 1);

-- STORAGES & TRANSCODERS

SET @iTotalFilesSize = (SELECT SUM(`size`) FROM `bx_messages_photos`);
SET @iTotalFilesNum = (SELECT COUNT(*) FROM `bx_messages_photos`);
SET @iTotalResizedSize = (SELECT SUM(`size`) FROM `bx_messages_photos_resized`);
SET @iTotalResizedNum = (SELECT COUNT(*) FROM `bx_messages_photos_resized`);

INSERT INTO `sys_objects_storage` (`object`, `engine`, `params`, `token_life`, `cache_control`, `levels`, `table_files`, `ext_mode`, `ext_allow`, `ext_deny`, `quota_size`, `current_size`, `quota_number`, `current_number`, `max_file_size`, `ts`) VALUES
('bx_messages_photos', 'Local', '', 360, 2592000, 3, 'bx_messages_photos', 'allow-deny', 'jpg,jpeg,jpe,gif,png', '', 0, @iTotalFilesSize, 0, @iTotalFilesNum, 0, 0),
('bx_messages_photos_resized', 'Local', '', 360, 2592000, 3, 'bx_messages_photos_resized', 'allow-deny', 'jpg,jpeg,jpe,gif,png', '', 0, @iTotalResizedSize, 0, @iTotalResizedNum, 0, 0);

INSERT INTO `sys_objects_transcoder_images` (`object`, `storage_object`, `source_type`, `source_params`, `private`, `atime_tracking`, `atime_pruning`, `ts`) VALUES 
('bx_messages_preview', 'bx_messages_photos_resized', 'Storage', 'a:1:{s:6:"object";s:18:"bx_messages_photos";}', 'no', '1', '2592000', '0');

INSERT INTO `sys_transcoder_images_filters` (`transcoder_object`, `filter`, `filter_params`, `order`) VALUES 
('bx_messages_preview', 'Resize', 'a:4:{s:1:"w";s:3:"300";s:1:"h";s:3:"200";s:11:"crop_resize";s:1:"1";s:10:"force_type";s:3:"jpg";}', '0');


-- PAGE: create entry

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_messages_create_entry', '_bx_msg_page_title_sys_create_entry', '_bx_msg_page_title_create_entry', 'bx_messages', 5, 2147483647, 1, 'compose-message', 'page.php?i=compose-message', '', '', '', 0, 1, 0, '', '');

INSERT INTO `sys_pages_blocks` (`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES
('bx_messages_create_entry', 1, 'bx_messages', '_bx_msg_page_block_title_create_entry', 11, 2147483647, 'service', 'a:2:{s:6:"module";s:11:"bx_messages";s:6:"method";s:13:"entity_create";}', 0, 1, 1);


-- PAGE: view entry

INSERT INTO `sys_objects_page`(`object`, `uri`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_messages_view_entry', 'view-message', '_bx_msg_page_title_sys_view_entry', '_bx_msg_page_title_view_entry', 'bx_messages', 11, 2147483647, 1, '', '', '', '', 0, 1, 0, 'BxMsgPageEntry', 'modules/boonex/messages/classes/BxMsgPageEntry.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `active`, `order`) VALUES 
('bx_messages_view_entry', 1, 'bx_messages', '_bx_msg_page_block_title_entry_actions', 0, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_messages\";s:6:\"method\";s:14:\"entity_actions\";}', 0, 0, 1, 0),
('bx_messages_view_entry', 2, 'bx_messages', '_bx_msg_page_block_title_entry_author', 0, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_messages\";s:6:\"method\";s:13:\"entity_author\";}', 0, 0, 1, 0),
('bx_messages_view_entry', 3, 'bx_messages', '_bx_msg_page_block_title_entry_collaborators', 0, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_messages\";s:6:\"method\";s:20:\"entity_collaborators\";}', 0, 0, 1, 0),
('bx_messages_view_entry', 1, 'bx_messages', '_bx_msg_page_block_title_entry_text', 13, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_messages\";s:6:\"method\";s:17:\"entity_text_block\";}', 0, 0, 1, 1),
('bx_messages_view_entry', 1, 'bx_messages', '_bx_msg_page_block_title_entry_comments', 11, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_messages\";s:6:\"method\";s:15:\"entity_comments\";}', 0, 0, 1, 2);


-- PAGE: module home

INSERT INTO `sys_objects_page`(`object`, `title_system`, `title`, `module`, `layout_id`, `visible_for_levels`, `visible_for_levels_editable`, `uri`, `url`, `meta_description`, `meta_keywords`, `meta_robots`, `cache_lifetime`, `cache_editable`, `deletable`, `override_class_name`, `override_class_file`) VALUES 
('bx_messages_home', '_bx_msg_page_title_sys_folder', '_bx_msg_page_title_folder', 'bx_messages', 5, 2147483647, 1, 'messages-folder', 'modules/?r=messages/folder/1', '', '', '', 0, 1, 0, 'BxMsgPageBrowse', 'modules/boonex/messages/classes/BxMsgPageBrowse.php');

INSERT INTO `sys_pages_blocks`(`object`, `cell_id`, `module`, `title`, `designbox_id`, `visible_for_levels`, `type`, `content`, `deletable`, `copyable`, `order`) VALUES 
('bx_messages_home', 1, 'bx_messages', '_bx_msg_page_block_title_folder', 0, 2147483647, 'service', 'a:2:{s:6:\"module\";s:11:\"bx_messages\";s:6:\"method\";s:4:\"home\";}', 0, 1, 0);


-- MENU: actions menu for view entry 

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_messages_view', '_bx_msg_menu_title_view_entry', 'bx_messages_view', 'bx_messages', 9, 0, 1, 'BxMsgMenuView', 'modules/boonex/messages/classes/BxMsgMenuView.php');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_messages_view', 'bx_messages', '_bx_msg_menu_set_title_view_entry', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_messages_view', 'bx_messages', 'delete-message', '_bx_msg_menu_item_title_system_delete_entry', '_bx_msg_menu_item_title_delete_entry', 'javascript:void(0);', 'bx_msg_delete(this, \'{content_id}\')', '', 'remove', '', 0, 1, 0, 2);


-- MENU: module sub-menu

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_messages_submenu', '_bx_msg_menu_title_submenu', 'bx_messages_submenu', 'bx_messages', 8, 0, 1, '', '');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_messages_submenu', 'bx_messages', '_bx_msg_menu_set_title_submenu', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_messages_submenu', 'bx_messages', 'messages-folder-primary', '_bx_msg_menu_item_title_system_folder_primary', '_bx_msg_menu_item_title_folder_primary', 'modules/?r=messages/folder/1', '', '', '', '', 2147483647, 1, 1, 1),
('bx_messages_submenu', 'bx_messages', 'messages-folder-more', '_bx_msg_menu_item_title_system_folder_more', '_bx_msg_menu_item_title_folder_more', 'javascript:void(0);', 'bx_menu_popup(''bx_messages_menu_folders_more'', this);', '', '', '', 2147483647, 1, 1, 2);


-- MENU: more folders

INSERT INTO `sys_objects_menu`(`object`, `title`, `set_name`, `module`, `template_id`, `deletable`, `active`, `override_class_name`, `override_class_file`) VALUES 
('bx_messages_menu_folders_more', '_bx_msg_menu_title_folders_more', 'bx_messages_menu_folders_more', 'bx_messages', 4, 0, 1, '', '');

INSERT INTO `sys_menu_sets`(`set_name`, `module`, `title`, `deletable`) VALUES 
('bx_messages_menu_folders_more', 'bx_messages', '_bx_msg_menu_set_title_folders_more', 0);

INSERT INTO `sys_menu_items`(`set_name`, `module`, `name`, `title_system`, `title`, `link`, `onclick`, `target`, `icon`, `submenu_object`, `visible_for_levels`, `active`, `copyable`, `order`) VALUES 
('bx_messages_menu_folders_more', 'bx_messages', 'messages-drafts', '_bx_msg_menu_item_title_system_folder_drafts', '_bx_msg_menu_item_title_folder_drafts', 'modules/?r=messages/folder/2', '', '', '', '', 2147483647, 1, 1, 1),
('bx_messages_menu_folders_more', 'bx_messages', 'messages-spam', '_bx_msg_menu_item_title_system_folder_spam', '_bx_msg_menu_item_title_folder_spam', 'modules/?r=messages/folder/3', '', '', '', '', 2147483647, 1, 1, 2),
('bx_messages_menu_folders_more', 'bx_messages', 'messages-trash', '_bx_msg_menu_item_title_system_folder_trash', '_bx_msg_menu_item_title_folder_trash', 'modules/?r=messages/folder/4', '', '', '', '', 2147483647, 1, 1, 3);


-- GRID

INSERT INTO `sys_objects_grid` (`object`, `source_type`, `source`, `table`, `field_id`, `field_order`, `paginate_url`, `paginate_per_page`, `paginate_simple`, `paginate_get_start`, `paginate_get_per_page`, `filter_fields`, `filter_mode`, `sorting_fields`, `visible_for_levels`, `override_class_name`, `override_class_file`) VALUES
('bx_messages', 'Sql', 'SELECT `c`.`id`, `c`.`author`, `c`.`text`, `c`.`added`, `c`.`comments`, `f`.`read_comments`, `last_reply_timestamp`, `last_reply_profile_id` FROM `bx_messages_conversations` AS `c` INNER JOIN `bx_messages_conv2folder` AS `f` ON (`c`.`id` = `f`.`conv_id` AND `f`.`folder_id` = {folder_id} AND `f`.`collaborator` = {profile_id})', 'bx_messages_conversations', 'id', 'last_reply_timestamp', '', 10, NULL, 'start', '', 'text', 'auto', 'comments,last_reply_timestamp', 64, 'BxMsgGrid', 'modules/boonex/messages/classes/BxMsgGrid.php');

INSERT INTO `sys_grid_fields` (`object`, `name`, `title`, `width`, `params`, `order`) VALUES
('bx_messages', 'checkbox', '_sys_select', '2%', '', 1),
('bx_messages', 'collaborators', '_bx_msg_field_collaborators', '25%', '', 2),
('bx_messages', 'preview', '_bx_msg_field_preview', '50%', '', 3),
('bx_messages', 'comments', '_bx_msg_field_comments', '10%', '', 4),
('bx_messages', 'last_reply_timestamp', '_bx_msg_field_updated', '13%', '', 5);

INSERT INTO `sys_grid_actions` (`object`, `type`, `name`, `title`, `icon`, `confirm`, `order`) VALUES
('bx_messages', 'bulk', 'delete', '_bx_msg_grid_action_delete', '', 1, 1),
('bx_messages', 'independent', 'add', '_bx_msg_grid_action_compose', '', 0, 1);


-- ACL

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_messages', 'create entry', NULL, '_bx_msg_acl_action_create_entry', '', 1, 1);
SET @iIdActionEntryCreate = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_messages', 'delete entry', NULL, '_bx_msg_acl_action_delete_entry', '', 1, 1);
SET @iIdActionEntryDelete = LAST_INSERT_ID();

INSERT INTO `sys_acl_actions` (`Module`, `Name`, `AdditionalParamName`, `Title`, `Desc`, `Countable`, `DisabledForLevels`) VALUES
('bx_messages', 'view entry', NULL, '_bx_msg_acl_action_view_entry', '', 1, 1);
SET @iIdActionEntryView = LAST_INSERT_ID();


SET @iUnauthenticated = 1;
SET @iStandard = 2;
SET @iUnconfirmed = 3;
SET @iPending = 4;
SET @iSuspended = 5;
SET @iModerator = 6;
SET @iAdministrator = 7;
SET @iPremium = 8;

INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES

-- entry create
(@iStandard, @iIdActionEntryCreate),
(@iModerator, @iIdActionEntryCreate),
(@iAdministrator, @iIdActionEntryCreate),
(@iPremium, @iIdActionEntryCreate),

-- entry delete
(@iStandard, @iIdActionEntryDelete),
(@iModerator, @iIdActionEntryDelete),
(@iAdministrator, @iIdActionEntryDelete),
(@iPremium, @iIdActionEntryDelete),

-- entry view
(@iUnauthenticated, @iIdActionEntryView),
(@iStandard, @iIdActionEntryView),
(@iUnconfirmed, @iIdActionEntryView),
(@iPending, @iIdActionEntryView),
(@iModerator, @iIdActionEntryView),
(@iAdministrator, @iIdActionEntryView),
(@iPremium, @iIdActionEntryView);


-- COMMENTS
INSERT INTO `sys_objects_cmts` (`Name`, `Table`, `CharsPostMin`, `CharsPostMax`, `CharsDisplayMax`, `Nl2br`, `PerView`, `PerViewReplies`, `BrowseType`, `IsBrowseSwitch`, `PostFormPosition`, `NumberOfLevels`, `IsDisplaySwitch`, `IsRatable`, `ViewingThreshold`, `IsOn`, `RootStylePrefix`, `BaseUrl`, `ObjectVote`, `TriggerTable`, `TriggerFieldId`, `TriggerFieldTitle`, `TriggerFieldComments`, `ClassName`, `ClassFile`) VALUES
('bx_messages', 'bx_messages_cmts', 1, 5000, 1000, 1, 5, 3, 'tail', 1, 'bottom', 1, 1, 0, -3, 1, 'cmt', 'page/view-message&id={object_id}', '', 'bx_messages_conversations', 'id', '', 'comments', 'BxMsgCmts', 'modules/boonex/messages/classes/BxMsgCmts.php');

-- VIEWS
INSERT INTO `sys_objects_view` (`name`, `table_track`, `period`, `is_on`, `trigger_table`, `trigger_field_id`, `trigger_field_count`, `class_name`, `class_file`) VALUES 
('bx_messages', 'bx_messages_views_track', '86400', '1', 'bx_messages_conversations', 'id', 'views', '', '');

-- ALERTS

INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `eval`) VALUES 
('bx_messages', 'BxMsgAlertsResponse', 'modules/boonex/messages/classes/BxMsgAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('bx_messages', 'commentPost', @iHandler),
('bx_messages', 'commentRemoved', @iHandler);

