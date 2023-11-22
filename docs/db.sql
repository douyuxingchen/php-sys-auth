CREATE TABLE `sys_auth_app` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `app_key` varchar(32) NOT NULL DEFAULT '' COMMENT '应用key',
  `secret_key` varchar(128) NOT NULL DEFAULT '' COMMENT '应用私钥',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '应用名称',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '应用描述',
  `api_limit` int unsigned NOT NULL DEFAULT '0' COMMENT 'API限制（0:无限制、其他数字代表每秒限制数量）',
  `ip_limit_type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'IP限制类型（0:白名单、1:黑名单）',
  `ip_white_list` varchar(200) NOT NULL DEFAULT '' COMMENT 'IP白名单',
  `ip_black_list` varchar(200) NOT NULL DEFAULT '' COMMENT 'IP黑名单',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '状态（0: 待审核、1: 通过、2: 拒绝）',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_app_key` (`app_key`) USING BTREE
) ENGINE=InnoDB COMMENT='Auth应用管理';

CREATE TABLE `sys_auth_api` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `system_id` int unsigned NOT NULL DEFAULT '0' COMMENT '系统ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '应用名称',
  `route` varchar(100) NOT NULL DEFAULT '' COMMENT '路由',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='Auth路由列表';

CREATE TABLE `sys_auth_app_route` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `app_id` int unsigned NOT NULL DEFAULT '0' COMMENT '应用ID',
  `api_id` int unsigned NOT NULL DEFAULT '0' COMMENT '路由ID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `udx_app_route` (`app_id`,`api_id`) USING BTREE
) ENGINE=InnoDB COMMENT='Auth应用路由关联表';

CREATE TABLE `sys_auth_system` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `key` varchar(32) NOT NULL DEFAULT '' COMMENT '系统标识',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '系统名称',
  `domain` varchar(100) NOT NULL DEFAULT '' COMMENT '系统域名',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='Auth系统表';
