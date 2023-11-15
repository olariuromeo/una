-- RECOMMENDATIONS
SET @iRecFans = (SELECT `id` FROM `sys_objects_recommendation` WHERE `name`='bx_groups_fans' LIMIT 1);

DELETE FROM `sys_recommendation_criteria` WHERE `object_id`=@iRecFans AND `name`='featured';
INSERT INTO `sys_recommendation_criteria` (`object_id`, `name`, `source_type`, `source`, `params`, `weight`, `active`) VALUES
(@iRecFans, 'featured', 'sql', 'SELECT `tp`.`id` AS `id`, {points} AS `value` FROM `bx_groups_data` AS `tg` INNER JOIN `sys_profiles` AS `tp` ON `tg`.`id`=`tp`.`content_id` AND `tp`.`type`=''bx_groups'' WHERE `tg`.`featured`<>''0'' AND `tg`.`status`=''active'' AND `tg`.`status_admin`=''active'' AND `tp`.`id` NOT IN (SELECT `content` FROM `bx_groups_fans` WHERE `initiator`={profile_id})', 'a:1:{s:6:"points";i:0;}', 0.1, 1);

UPDATE `sys_recommendation_criteria` SET `source`='SELECT `tgf`.`content` AS `id`, SUM({points}) AS `value` FROM `sys_profiles_conn_friends` AS `tf` INNER JOIN `bx_groups_fans` AS `tgf` ON `tf`.`content`=`tgf`.`initiator` AND `tgf`.`content` NOT IN (SELECT `content` FROM `bx_groups_fans` WHERE `initiator`={profile_id}) AND `tgf`.`mutual`=''1'' WHERE `tf`.`initiator`={profile_id} AND `tf`.`mutual`=''1'' GROUP BY `id`' WHERE `object_id`=@iRecFans AND `name`='by_friends';
UPDATE `sys_recommendation_criteria` SET `source`='SELECT `tgf`.`content` AS `id`, SUM({points}) AS `value` FROM `sys_profiles_conn_subscriptions` AS `ts` INNER JOIN `sys_profiles` AS `tp` ON `ts`.`content`=`tp`.`id` AND `tp`.`type` IN ({profile_types}) AND `tp`.`status`=''active'' INNER JOIN `bx_groups_fans` AS `tgf` ON `ts`.`content`=`tgf`.`initiator` AND `tgf`.`content` NOT IN (SELECT `content` FROM `bx_groups_fans` WHERE `initiator`={profile_id}) AND `tgf`.`mutual`=''1'' WHERE `ts`.`initiator`={profile_id} GROUP BY `id`', `weight`='0.2' WHERE `object_id`=@iRecFans AND `name`='by_subscriptions';
UPDATE `sys_recommendation_criteria` SET `source`='SELECT `tg2`.`content` AS `id`, SUM({points}) AS `value` FROM `bx_groups_fans` AS `tg1` INNER JOIN `bx_groups_fans` AS `tm` ON `tg1`.`content`=`tm`.`content` AND `tm`.`initiator`<>{profile_id} AND `tm`.`mutual`=''1'' INNER JOIN `bx_groups_fans` AS `tg2` ON `tm`.`initiator`=`tg2`.`initiator` AND `tg2`.`mutual`=''1'' AND `tg2`.`content` NOT IN (SELECT `content` FROM `bx_groups_fans` WHERE `initiator`={profile_id})  WHERE `tg1`.`initiator`={profile_id} AND `tg1`.`mutual`=''1'' GROUP BY `id`', `weight`='0.2' WHERE `object_id`=@iRecFans AND `name`='by_fans';
