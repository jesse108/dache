-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2014 年 02 月 15 日 11:42
-- 服务器版本: 5.5.27-log
-- PHP 版本: 5.4.6


--
-- 表的结构 `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '单位名',
  `phone` varchar(24) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '电话',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `comment` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '注释',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='单位' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `company_route`
--

CREATE TABLE IF NOT EXISTS `company_route` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL COMMENT '公司Id',
  `departure` bigint(20) NOT NULL COMMENT '出站',
  `destination` bigint(20) NOT NULL COMMENT '目的地',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `weight` int(10) NOT NULL DEFAULT '1' COMMENT '权重',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `departure` (`departure`,`destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='公司路线' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `ename` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '英文名',
  `level` tinyint(2) NOT NULL DEFAULT '0' COMMENT '层级',
  `parent_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '上级ID',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='位置' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1:normal',
  `url` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `post_data` varchar(2000) COLLATE utf8_bin DEFAULT NULL,
  `content` varchar(2000) COLLATE utf8_bin DEFAULT NULL,
  `create_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='日志' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `departure` bigint(20) NOT NULL COMMENT '出发ID',
  `destination` bigint(20) NOT NULL COMMENT '目的地ID',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL COMMENT '出发时间',
  `num` int(5) NOT NULL COMMENT '人数',
  `contact_username` varchar(32) COLLATE utf8_bin DEFAULT NULL COMMENT '联系人名',
  `contact_mobile` varchar(24) COLLATE utf8_bin NOT NULL COMMENT '联系电话',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `comment` varchar(256) COLLATE utf8_bin DEFAULT NULL COMMENT '备注',
  `call_status` tinyint(2) not null default 0 commnet '呼叫状态 0:无呼叫  1: 呼叫中',
  `last_call_time` int(10) not null default 0 comment '最后呼叫时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `departure` (`departure`,`destination`),
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='订单' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `order_track`
--

CREATE TABLE IF NOT EXISTS `order_track` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `company_id` bigint(20) NOT NULL COMMENT '公司ID',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `finish_time` int(10) not null default '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='订单跟踪' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未知 1:男 2:女',
  `mobile` varchar(24) COLLATE utf8_bin DEFAULT NULL,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `login_time` int(10) NOT NULL DEFAULT '0',
  `login_ip` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0:正常',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_user`
--

CREATE TABLE IF NOT EXISTS `weixin_user` (
  `user_id` bigint(20) NOT NULL,
  `open_id` varchar(64) COLLATE utf8_bin NOT NULL,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `nickname` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `sex` tinyint(2) DEFAULT NULL,
  `city` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `province` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `language` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `headimgurl` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `subscribe_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `open_id` (`open_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE IF NOT EXISTS `router_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) not null default 0,
  `departure` bigint(20) not null default 0,
  `destination` bigint(20) not null default 0,
  `company_id` bigint(20) not null default 0,
  `company_route_id` bigint(20) not null default 0,
  `order_id` bigint(20) not null default 0,
  `user_id` bigint(20) not null default 0,
  PRIMARY KEY (`id`),
  key(`departure`,`destination`),
  key(`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户' AUTO_INCREMENT=1 ;
