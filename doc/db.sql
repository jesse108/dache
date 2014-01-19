create table if not exists `user`(
	`id` bigint(20) not null primary key auto_increment,
	`username` varchar(32),
	`email` varchar(64),
	`sex` tinyint(1) not null default 0 comment '0:未知 1:男 2:女',
	`mobile` varchar(24),
	`create_time` int(10) not null default 0,
	`update_time` int(10) not null default 0,
	`login_time` int(10) not null default 0,
	`login_ip` varchar(32),
	`status` tinyint(2) not null default 0 comment '0:正常',
	key(`mobile`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


create table if not exists `location`(
	`id` bigint(20) not null primary key auto_increment,
	`name` varchar(32) not null default '',
	`ename` varchar(32) not null default '',
	`level` tinyint(2) not null default 1,
	`parent_id` bigint(20) not null default 0,
	`status` tinyint(2) not null default 0,
	key(`parent_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



create table if not exists `order`(
	`id` bigint(20) not null primary key auto_increment,
	`user_id` bigint(20) not null,
	`departure` bigint(20) not null,
	`destination` bigint(20) not null,
	`status` tinyint(2) not null default 0,
	`time` int(10) not null comment '出发时间',
	`num` int(5) not null comment '人数',
	`contact_username` varchar(32),
	`contact_mobile` varchar(24) not null,
	`create_time` int(10) not null default 0,
	`update_time` int(10) not null default 0,
	`comment` varchar(256),
	key(`user_id`),
	key(`departure`,`destination`),
	key(`create_time`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


create table if not exists `company`(
	`id` bigint(20) not null primary key auto_increment,
	`name` varchar(32) not null default '',
	`phone` varchar(24) not null default '',
	`status` tinyint(2) not null default 0,
	`comment` varchar(256),
	`create_time` int(10) not null default 0,
	`update_time` int(10) not null default 0
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


create table if not exists `company_route`(
	`id` bigint(20) not null primary key auto_increment,
	`company_id` bigint(20) not null,
	`departure` bigint(20) not null,
	`destination` bigint(20) not null,
	`status` tinyint(2) not null default 0,
	key(`company_id`),
	key(`departure`,`destination`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


create table if not exists `order_track`(
	`id` bigint(20) not null primary key auto_increment,
	`order_id` bigint(20) not null,
	`company_id` bigint(20) not null,
	`create_time` int(10) not null default 0,
	`status` tinyint(2) not null default 0,
	key(`order_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



create table if not exists `log`(
	`id` bigint(20) not null primary key auto_increment,
	`type` tinyint(2) not null default 1 comment '1:normal',
	`url` varchar(256),
	`post_data` varchar(2000),
	`content` varchar(2000),
	`create_time` int(10) not null default 0,
	key(`create_time`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


