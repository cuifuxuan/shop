

CREATE TABLE `yoshop_wxapp_live_room` (
  `room_id` int(11) unsigned NOT NULL COMMENT '直播间id',
  `room_name` varchar(200) NOT NULL DEFAULT '' COMMENT '直播间名称',
  `cover_img` varchar(255) DEFAULT '' COMMENT '分享卡片封面',
  `share_img` varchar(255) DEFAULT '' COMMENT '直播间背景墙封面',
  `anchor_name` varchar(30) NOT NULL DEFAULT '' COMMENT '主播昵称',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开播时间',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `live_status` tinyint(3) unsigned NOT NULL DEFAULT '102' COMMENT '直播状态(101: 直播中, 102: 未开始, 103: 已结束, 104: 禁播, 105: 暂停中, 106: 异常, 107: 已过期)',
  `is_top` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '置顶状态(0未置顶 1已置顶)',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '软删除(0未删除 1已删除)',
  `wxapp_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信小程序直播间记录表';



INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10462', '小程序直播', 'apps.live', '10074', '125', '1585120375', '1585120375');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10463', '直播间管理', 'apps.live.room/index', '10462', '100', '1585120404', '1585120404');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10464', '同步刷新', 'apps.live.room/refresh', '10463', '100', '1585120404', '1585120404');
INSERT INTO `yoshop_store_access` (`access_id`, `name`, `url`, `parent_id`, `sort`, `create_time`, `update_time`) VALUES ('10465', '设置置顶状态', 'apps.live.room/refresh', '10463', '100', '1585120404', '1585120404');



