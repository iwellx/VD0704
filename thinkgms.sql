/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : thinkckb

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-07-21 08:48:09
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ckb_action
-- ----------------------------
DROP TABLE IF EXISTS `ckb_action`;
CREATE TABLE `ckb_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL COMMENT '标识',
  `title` char(80) NOT NULL COMMENT '标题',
  `remark` char(140) NOT NULL COMMENT '描述',
  `rule` text NOT NULL COMMENT '行为规则',
  `log` text NOT NULL COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表';

-- ----------------------------
-- Records of ckb_action
-- ----------------------------
INSERT INTO `ckb_action` VALUES ('1', 'Admin_Login', '管理员登录', '系统记录', '', '[user|get_nickname]在[time|time_format]登录了后台', '1', '1', '1444460123');
INSERT INTO `ckb_action` VALUES ('2', 'Admin_Logout', '管理员退出', '系统记录', '', '[user|get_nickname]在[time|time_format]退出系统', '1', '1', '1444460123');

-- ----------------------------
-- Table structure for ckb_action_log
-- ----------------------------
DROP TABLE IF EXISTS `ckb_action_log`;
CREATE TABLE `ckb_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` varchar(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表';

-- ----------------------------
-- Records of ckb_action_log
-- ----------------------------
INSERT INTO `ckb_action_log` VALUES ('1', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468315329');
INSERT INTO `ckb_action_log` VALUES ('2', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468315408');
INSERT INTO `ckb_action_log` VALUES ('3', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468376352');
INSERT INTO `ckb_action_log` VALUES ('4', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468378628');
INSERT INTO `ckb_action_log` VALUES ('5', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468380475');
INSERT INTO `ckb_action_log` VALUES ('6', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468380479');
INSERT INTO `ckb_action_log` VALUES ('7', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468381465');
INSERT INTO `ckb_action_log` VALUES ('8', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468381483');
INSERT INTO `ckb_action_log` VALUES ('9', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468390390');
INSERT INTO `ckb_action_log` VALUES ('10', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468390464');
INSERT INTO `ckb_action_log` VALUES ('11', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468391032');
INSERT INTO `ckb_action_log` VALUES ('12', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468391073');
INSERT INTO `ckb_action_log` VALUES ('13', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468391081');
INSERT INTO `ckb_action_log` VALUES ('14', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468392002');
INSERT INTO `ckb_action_log` VALUES ('15', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468392039');
INSERT INTO `ckb_action_log` VALUES ('16', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468392079');
INSERT INTO `ckb_action_log` VALUES ('17', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468392088');
INSERT INTO `ckb_action_log` VALUES ('18', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468392203');
INSERT INTO `ckb_action_log` VALUES ('19', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468392251');
INSERT INTO `ckb_action_log` VALUES ('20', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468405771');
INSERT INTO `ckb_action_log` VALUES ('21', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468405798');
INSERT INTO `ckb_action_log` VALUES ('22', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468457193');
INSERT INTO `ckb_action_log` VALUES ('23', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468486880');
INSERT INTO `ckb_action_log` VALUES ('24', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468487220');
INSERT INTO `ckb_action_log` VALUES ('25', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468499383');
INSERT INTO `ckb_action_log` VALUES ('26', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468499393');
INSERT INTO `ckb_action_log` VALUES ('27', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468551282');
INSERT INTO `ckb_action_log` VALUES ('28', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468551357');
INSERT INTO `ckb_action_log` VALUES ('29', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468551511');
INSERT INTO `ckb_action_log` VALUES ('30', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468551526');
INSERT INTO `ckb_action_log` VALUES ('31', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468801778');
INSERT INTO `ckb_action_log` VALUES ('32', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1468812616');
INSERT INTO `ckb_action_log` VALUES ('33', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468812966');
INSERT INTO `ckb_action_log` VALUES ('34', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468818886');
INSERT INTO `ckb_action_log` VALUES ('35', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1468974514');
INSERT INTO `ckb_action_log` VALUES ('36', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1469061002');
INSERT INTO `ckb_action_log` VALUES ('37', '2', '1', '0.0.0.0', 'User', '1', '超级管理员在退出系统', '1', '1469061026');
INSERT INTO `ckb_action_log` VALUES ('38', '1', '1', '0.0.0.0', 'User', '1', '超级管理员在登录了后台', '1', '1469061034');

-- ----------------------------
-- Table structure for ckb_addons
-- ----------------------------
DROP TABLE IF EXISTS `ckb_addons`;
CREATE TABLE `ckb_addons` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '插件名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '插件中文名称',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `disabled` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `isconfig` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许配置',
  `config` text,
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '安装时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `author` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '0.0.0' COMMENT '版本号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='已安装模块列表';

-- ----------------------------
-- Records of ckb_addons
-- ----------------------------
INSERT INTO `ckb_addons` VALUES ('12', 'SiteStat', '站点统计信息', '统计站点的基础信息', '1', '1', '0', '\"\"', '0', '0', '管侯杰', '0.1');

-- ----------------------------
-- Table structure for ckb_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `ckb_auth_group`;
CREATE TABLE `ckb_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '上级',
  `title` varchar(80) NOT NULL COMMENT '用户组标题',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '用户组状态',
  `rules` text NOT NULL COMMENT '用户权限',
  `sort` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  `del` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_auth_group
-- ----------------------------
INSERT INTO `ckb_auth_group` VALUES ('1', '0', '超级管理组', null, '1', '1,41,42,4,33,36,35,34,45,46,47,48,61,49,52,51,50,29,32,31,30,5,62,38,37,40,39,2', '1', '0');
INSERT INTO `ckb_auth_group` VALUES ('6', '0', '普通用户', '', '1', '', '1', '0');

-- ----------------------------
-- Table structure for ckb_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `ckb_auth_rule`;
CREATE TABLE `ckb_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单',
  `name` varchar(200) DEFAULT NULL COMMENT '节点',
  `title` char(20) NOT NULL COMMENT '标题',
  `icon` varchar(100) NOT NULL DEFAULT 'iconfont icon-other' COMMENT '图表',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '菜单类型',
  `hide` tinyint(2) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `condition` char(100) NOT NULL DEFAULT '',
  `del` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=241 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_auth_rule
-- ----------------------------
INSERT INTO `ckb_auth_rule` VALUES ('1', '0', '', '系统', 'iconfont icon-computer', '2', '0', '1', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('2', '0', '', '用户', 'iconfont icon-user', '2', '0', '1', '3', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('3', '0', '', '扩展', 'iconfont icon-all', '2', '0', '0', '9', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('4', '1', '', '系统设置', 'iconfont icon-computer', '2', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('5', '1', '', '数据库管理', 'iconfont icon-associated', '2', '0', '0', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('6', '2', '', '用户管理', 'iconfont icon-user', '2', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('7', '2', '', '行为管理', 'iconfont icon-monitoring', '2', '0', '0', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('8', '3', '', '在线平台', 'iconfont icon-cloud', '2', '1', '0', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('9', '3', '', '模块管理', 'iconfont icon-data', '2', '0', '0', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('10', '3', '', '插件管理', 'iconfont icon-keyboard', '2', '0', '0', '3', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('11', '8', 'Admin/Cloud/index?type=1', '模块商店', 'iconfont icon-cart', '2', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('12', '8', 'Admin/Cloud/index?type=2', '插件商店', 'iconfont icon-cart', '2', '0', '1', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('13', '7', 'Admin/Action/index', '行为管理', 'iconfont icon-monitoring', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('14', '13', 'Admin/Action/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('15', '13', 'Admin/Action/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('16', '13', 'Admin/Action/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('17', '7', 'Admin/ActionLog/index', '日志管理', 'iconfont icon-survey', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('20', '17', 'Admin/ActionLog/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('25', '6', 'Admin/AuthGroup/index', '用户组管理', 'iconfont icon-members', '1', '0', '1', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('26', '25', 'Admin/AuthGroup/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('27', '25', 'Admin/AuthGroup/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('28', '25', 'Admin/AuthGroup/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('29', '4', 'Admin/AuthRule/index', '菜单管理', 'iconfont icon-viewlist', '1', '0', '1', '5', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('30', '29', 'Admin/AuthRule/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('31', '29', 'Admin/AuthRule/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('32', '29', 'Admin/AuthRule/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('33', '4', 'Admin/Config/index', '配置管理', 'iconfont icon-set', '1', '0', '1', '9', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('34', '33', 'Admin/Config/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('35', '33', 'Admin/Config/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('36', '33', 'Admin/Config/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('37', '5', 'Admin/Database/index?type=export', '备份数据库', 'iconfont icon-indentation-right', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('38', '62', 'Admin/Database/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('39', '37', 'Admin/Database/repair', '修复表', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('40', '37', 'Admin/Database/optimize', '优化表', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('41', '1', 'Admin/Index/index', '后台首页', 'iconfont icon-other', '1', '1', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('45', '4', 'Admin/Model/index', '模型管理', 'iconfont icon-box-empty', '1', '0', '0', '3', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('46', '45', 'Admin/Model/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('47', '45', 'Admin/Model/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('48', '45', 'Admin/Model/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('49', '4', 'Admin/ModelField/index', '字段管理', 'iconfont icon-other', '1', '1', '1', '4', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('50', '49', 'Admin/ModelField/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('51', '49', 'Admin/ModelField/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('52', '49', 'Admin/ModelField/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('61', '4', 'Admin/Config/group', '系统设置', 'iconfont icon-shezhi', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('57', '6', 'Admin/User/index', '用户管理', 'iconfont icon-account', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('58', '57', 'Admin/User/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('59', '57', 'Admin/User/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('60', '57', 'Admin/User/del', '删除', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('62', '5', 'Admin/Database/index?type=import', '还原数据库', 'iconfont icon-indentation-left', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('79', '10', 'Admin/Addons/index', '插件管理', 'iconfont icon-other', '1', '0', '1', '44', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('80', '4', 'Admin/Hooks/index', '钩子管理', 'iconfont icon-other', '1', '0', '0', '20', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('81', '80', 'Admin/Hooks/add', '新增', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('82', '80', 'Admin/Hooks/edit', '编辑', 'iconfont icon-other', '1', '0', '1', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('83', '80', 'Admin/Hooks/del', '删除', 'iconfont icon-other', '1', '1', '1', '3', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('84', '17', 'Admin/ActionLog/edit', '查看', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('85', '8', 'Admin/Cloud/index?type=3', '开发者', 'iconfont icon-member', '1', '0', '1', '3', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('90', '9', 'Admin/Module/index', '模块管理', 'iconfont icon-project-solid', '1', '0', '1', '0', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('91', '90', 'Admin/Module/add', '安装', 'iconfont icon-other', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('92', '90', 'Admin/Module/disabled', '启用', 'iconfont icon-other', '1', '0', '1', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('93', '90', 'Admin/Module/del', '卸载', 'iconfont icon-other', '1', '1', '1', '3', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('207', '0', '', '企业管理', 'sitemap', '2', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('208', '211', 'Admin/Company/iist', '企业列表', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('209', '211', 'Admin/Company/add', '添加企业', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('210', '211', 'Admin/Company/edit', '修改', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('211', '207', 'Admin/Company/index', '企业管理', '', '2', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('212', '211', 'Admin/Company/del', '删除企业', '', '2', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('213', '0', '', '客户管理', 'users', '2', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('214', '213', 'Admin/Custom/index', '客户管理', '', '1', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('215', '214', 'Admin/Custom/index', '客户列表', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('216', '214', 'Admin/Custom/add', '添加客户', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('217', '214', 'Admin/Custom/edit', '修改客户', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('218', '214', 'Admin/Custom/del', '删除客户', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('219', '214', 'Admin/Custom/view', '查看客户信息', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('220', '0', '', '套餐管理', 'sliders', '2', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('221', '220', 'Admin/Package/index', '套餐管理', '', '1', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('222', '221', 'Admin/Package/index', '套餐列表', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('223', '221', 'Admin/Package/add', '添加套餐', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('224', '221', 'Admin/Package/edit', '修改套餐', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('225', '221', 'Admin/Package/edit', '删除套餐', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('226', '221', 'Admin/Package/view', '查看套餐信息', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('227', '0', '', '订单管理', 'th-large', '2', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('228', '227', 'Admin/Order/index', '订单管理', '', '1', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('229', '228', 'Admin/Order/index', '订单列表', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('230', '228', 'Admin/Order/view', '查看订单', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('231', '0', '', '续费管理', 'files-o', '2', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('232', '231', 'Admin/Expenses/index', '续费管理', '', '1', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('233', '232', 'Admin/Expenses/index', '续费列表', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('234', '232', 'Admin/Expenses/view', '查看续费信息', '', '1', '1', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('235', '231', 'Admin/Expenses/batch', '批量续费', '', '1', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('236', '0', '', '统计管理', 'bar-chart-o', '2', '0', '1', '255', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('237', '236', 'Admin/Counts/company', '企业统计', '', '1', '0', '1', '1', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('238', '236', 'Admin/Counts/package', '套餐包统计', '', '1', '0', '1', '2', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('239', '236', 'Admin/Counts/tracingCharge', '充值追踪表', '', '1', '0', '1', '3', '', '0');
INSERT INTO `ckb_auth_rule` VALUES ('240', '236', 'Admin/Counts/tracingData', '流量追踪表', '', '1', '0', '1', '4', '', '0');

-- ----------------------------
-- Table structure for ckb_cache
-- ----------------------------
DROP TABLE IF EXISTS `ckb_cache`;
CREATE TABLE `ckb_cache` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增长ID',
  `key` char(100) NOT NULL DEFAULT '' COMMENT '缓存key值',
  `name` char(100) NOT NULL DEFAULT '' COMMENT '名称',
  `module` char(20) NOT NULL DEFAULT '' COMMENT '模块名称',
  `model` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `action` char(30) NOT NULL DEFAULT '' COMMENT '方法名',
  `param` char(255) NOT NULL DEFAULT '' COMMENT '参数',
  `system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统',
  PRIMARY KEY (`id`),
  KEY `ckey` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='缓存更新列队';

-- ----------------------------
-- Records of ckb_cache
-- ----------------------------
INSERT INTO `ckb_cache` VALUES ('1', 'Config', '网站配置', 'Admin', 'Config', 'cache', '', '1');
INSERT INTO `ckb_cache` VALUES ('2', 'Action', '行为列表', 'Admin', 'Action', 'cache', '', '0');
INSERT INTO `ckb_cache` VALUES ('3', 'ActionLog', '行为日志', 'Admin', 'ActionLog', 'cache', '', '0');

-- ----------------------------
-- Table structure for ckb_company
-- ----------------------------
DROP TABLE IF EXISTS `ckb_company`;
CREATE TABLE `ckb_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '企业ID',
  `name` varchar(50) NOT NULL COMMENT '公司名称',
  `wx_number` varchar(50) DEFAULT NULL COMMENT '微信账号',
  `bank_number` varchar(50) DEFAULT NULL COMMENT '银行卡号',
  `alipay_number` varchar(50) DEFAULT NULL COMMENT '支付宝号',
  `contact` varchar(30) DEFAULT NULL COMMENT '联系人',
  `tel` varchar(20) DEFAULT NULL COMMENT '联系方式',
  `address` varchar(100) DEFAULT NULL COMMENT '公司地址',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `remark` text COMMENT '备注',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态（1正常，2禁用）',
  `del` tinyint(2) DEFAULT '0' COMMENT '删除（1是，0否）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_company
-- ----------------------------
INSERT INTO `ckb_company` VALUES ('1', '福信富通测100', '微信账号', '银行卡号', '支付宝号', '联系人', '手机', '公司地址', '1@1.com', '备注', '1468803975', '1468804343', '1', '0');
INSERT INTO `ckb_company` VALUES ('2', '企业名', '', '银行卡号', '', '', '', '', '1@1.com', '', '1468978174', '1468978174', '1', '0');

-- ----------------------------
-- Table structure for ckb_config
-- ----------------------------
DROP TABLE IF EXISTS `ckb_config`;
CREATE TABLE `ckb_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置标题',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` text NOT NULL COMMENT '配置参数',
  `remark` varchar(100) NOT NULL COMMENT '说明',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` int(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` int(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_config
-- ----------------------------
INSERT INTO `ckb_config` VALUES ('1', 'WEB_SITE_TITLE', '1', '网站标题', '1', '', '网站标题前台显示标题', '1378898976', '1379235274', '1', '车控宝企业管理后台1', '0');
INSERT INTO `ckb_config` VALUES ('2', 'WEB_SITE_DESCRIPTION', '2', '网站描述', '1', '', '网站搜索引擎描述', '1378898976', '1379235841', '1', 'Thinkckb内置：thinkphp,EasyUI,AmazeUI,KE编辑器', '1');
INSERT INTO `ckb_config` VALUES ('4', 'WEB_SITE_CLOSE', '4', '关闭站点', '1', '0:关闭|1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', '1378898976', '1379235296', '1', '1', '1');
INSERT INTO `ckb_config` VALUES ('9', 'CONFIG_TYPE_LIST', '3', '配置类型', '4', '', '主要用于数据解析和页面表单的生成', '1378898976', '1379235348', '0', '0:数字|1:字符|2:文本|3:数组|4:枚举|5:编辑器', '2');
INSERT INTO `ckb_config` VALUES ('10', 'WEB_SITE_ICP', '1', '网站备案号', '1', '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', '1378900335', '1379235859', '0', '000-11', '9');
INSERT INTO `ckb_config` VALUES ('20', 'CONFIG_GROUP_LIST', '3', '配置分组', '4', '', '用于系统配置中批量更改的分组', '1379228036', '1384418383', '1', '1:基本|2:内容|3:用户|4:系统', '4');
INSERT INTO `ckb_config` VALUES ('28', 'DATA_BACKUP_PATH', '1', '数据库备份根路径', '4', '', '路径必须以 / 结尾', '1381482411', '1381482411', '1', './Data/', '5');
INSERT INTO `ckb_config` VALUES ('29', 'DATA_BACKUP_PART_SIZE', '0', '数据库备份卷大小', '4', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '1381482488', '1381729564', '1', '20971520', '7');
INSERT INTO `ckb_config` VALUES ('30', 'DATA_BACKUP_COMPRESS', '4', '数据库备份文件是否启用压缩', '4', '0:不压缩|1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '1381713345', '1381729544', '1', '1', '9');
INSERT INTO `ckb_config` VALUES ('58', 'ACTION_TYPE', '3', '行为类型', '3', '', '行为的类型', '0', '0', '1', '1:系统|2:用户', '0');
INSERT INTO `ckb_config` VALUES ('59', 'USER_STATUS_TYPE', '3', '用户状态类型', '3', '', '用户状态类型', '0', '0', '1', '0:禁用|1:启用', '0');
INSERT INTO `ckb_config` VALUES ('60', 'USERGROUP_STATUS_TYPE', '3', '用户组状态', '3', '', '用户组状态', '0', '0', '1', '0:禁用|1:启用|2:暂停使用|3:废弃', '0');
INSERT INTO `ckb_config` VALUES ('61', 'ADMIN_QQ', '1', '管理员QQ', '4', '管理员的QQ号码', '', '0', '0', '1', '91252463912', '0');
INSERT INTO `ckb_config` VALUES ('62', 'LEFT_MENU_STYLE', '4', '左侧导航风格', '4', '1:Metro|2:列表', '', '0', '0', '1', '1', '0');
INSERT INTO `ckb_config` VALUES ('63', 'ADMIN_LOGIN_BG_TYPE', '4', '后台登录背景类型', '4', '0:纯色|1:根据值|2:随机（1-5）', '', '0', '0', '1', '2', '0');
INSERT INTO `ckb_config` VALUES ('64', 'ADMIN_LOGIN_BG_IMG', '2', '后台登录背景图片路径', '4', '', '', '0', '0', '1', './Public/Admin/images/Login/bg_1.jpg', '0');
INSERT INTO `ckb_config` VALUES ('65', 'ADMIN_REME', '0', '后台记住密码时间', '4', '', '', '0', '0', '1', '3600', '0');

-- ----------------------------
-- Table structure for ckb_custom
-- ----------------------------
DROP TABLE IF EXISTS `ckb_custom`;
CREATE TABLE `ckb_custom` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户信息表',
  `custom_no` varchar(20) NOT NULL COMMENT '客户编号',
  `name` varchar(20) NOT NULL COMMENT '客户名称',
  `plate_number` varchar(20) NOT NULL COMMENT '车牌号',
  `imsi` varchar(30) NOT NULL,
  `carrieroperator` tinyint(2) NOT NULL COMMENT '运营商（10联通，11移动，12电信）',
  `card_number` varchar(30) NOT NULL COMMENT '卡号',
  `card_from` varchar(30) NOT NULL COMMENT '卡来源（运营商编号）',
  `iccid` varchar(30) NOT NULL,
  `company` int(11) NOT NULL COMMENT '所属公司',
  `tag` varchar(20) NOT NULL COMMENT '特殊标签',
  `custom_state` tinyint(2) NOT NULL COMMENT '用户状态（10测试期、11沉默期、12服务期、13已注销）',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '状态（1正常，0禁用）',
  `del` tinyint(1) NOT NULL COMMENT '删除（1是，0否）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_custom
-- ----------------------------
INSERT INTO `ckb_custom` VALUES ('1', 'No00000000001', '11111111111', '车牌号', '222222222222222', '12', '3333333333333', '来源', '44444444444444444444', '1', '特殊标签', '0', '备注', '1468823885', '1468827373', '1', '0');

-- ----------------------------
-- Table structure for ckb_hooks
-- ----------------------------
DROP TABLE IF EXISTS `ckb_hooks`;
CREATE TABLE `ckb_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` varchar(255) NOT NULL DEFAULT '' COMMENT '钩子挂载的插件 ''，''分割',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_hooks
-- ----------------------------
INSERT INTO `ckb_hooks` VALUES ('1', 'pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', '1', '0', '', '1');
INSERT INTO `ckb_hooks` VALUES ('2', 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', '1', '0', '', '1');
INSERT INTO `ckb_hooks` VALUES ('3', 'AdminIndex', '首页小格子个性化显示', '1', '1382596073', 'SiteStat', '1');
INSERT INTO `ckb_hooks` VALUES ('4', 'app_begin', '应用开始', '2', '1384481614', '', '1');

-- ----------------------------
-- Table structure for ckb_model
-- ----------------------------
DROP TABLE IF EXISTS `ckb_model`;
CREATE TABLE `ckb_model` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` char(30) NOT NULL COMMENT '标识',
  `title` char(30) NOT NULL COMMENT '名称',
  `table_name` varchar(50) NOT NULL COMMENT '表名',
  `is_extend` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '允许子模型',
  `extend` int(6) unsigned NOT NULL DEFAULT '0' COMMENT '继承的模型',
  `list_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '列表类型',
  `list_edit` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许行编辑',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` tinyint(2) NOT NULL DEFAULT '1',
  `engine_type` varchar(25) NOT NULL DEFAULT 'MyISAM' COMMENT '数据库引擎',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文档模型表';

-- ----------------------------
-- Records of ckb_model
-- ----------------------------

-- ----------------------------
-- Table structure for ckb_model_field
-- ----------------------------
DROP TABLE IF EXISTS `ckb_model_field`;
CREATE TABLE `ckb_model_field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '字段名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '字段注释',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '数据类型',
  `field` varchar(100) NOT NULL COMMENT '字段定义',
  `value` varchar(100) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `extra` text NOT NULL COMMENT '参数',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `list_edit` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许行编辑',
  `sort_l` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '列表',
  `sort_s` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '搜索',
  `sort_a` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '新增',
  `sort_e` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '修改',
  `is_sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序关键字',
  `l_width` varchar(10) NOT NULL DEFAULT '100' COMMENT '列表宽度',
  `field_group` varchar(5) NOT NULL DEFAULT '1' COMMENT '字段分组',
  `validate_rule` text NOT NULL COMMENT '验证规则',
  `auto_rule` text NOT NULL COMMENT '完成规则',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `model_id` (`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模型字段表';

-- ----------------------------
-- Records of ckb_model_field
-- ----------------------------

-- ----------------------------
-- Table structure for ckb_module
-- ----------------------------
DROP TABLE IF EXISTS `ckb_module`;
CREATE TABLE `ckb_module` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '模块名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '模块中文名称',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `disabled` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `isconfig` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许配置',
  `config` text,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `author` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '0.0.0' COMMENT '版本号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='已安装模块列表';

-- ----------------------------
-- Records of ckb_module
-- ----------------------------

-- ----------------------------
-- Table structure for ckb_package
-- ----------------------------
DROP TABLE IF EXISTS `ckb_package`;
CREATE TABLE `ckb_package` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '套餐信息ID',
  `package_sn` varchar(30) NOT NULL COMMENT '套餐编号（套餐ID）',
  `tag` tinyint(2) NOT NULL COMMENT '标签（10流量，11短信）',
  `name` varchar(50) NOT NULL COMMENT '套餐名',
  `price` varchar(10) NOT NULL COMMENT '价格',
  `package_value` varchar(10) NOT NULL COMMENT '套餐价值（流量M，短信数）',
  `carrieroperator` tinyint(2) NOT NULL COMMENT '运营商(10联通，11移动，12电信)',
  `card_from` varchar(100) NOT NULL COMMENT '卡来源（运营商编号）',
  `cycle_unit` varchar(10) NOT NULL COMMENT '套餐周期单位',
  `cycle_value` varchar(50) NOT NULL COMMENT '套餐周期值',
  `value` varchar(50) NOT NULL COMMENT '值',
  `unit` varchar(10) NOT NULL COMMENT '单位（year年,month月,day日）',
  `extra` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL COMMENT '套餐描述',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '删除时间',
  `status` tinyint(1) NOT NULL COMMENT '状态（1正常，0禁用）',
  `del` tinyint(1) NOT NULL COMMENT '删除（1是，0否）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_package
-- ----------------------------
INSERT INTO `ckb_package` VALUES ('1', '', '10', '测试套餐', '1', '', '10', '', '0', '2', '3', '0', '', '', '0', '0', '1', '1');
INSERT INTO `ckb_package` VALUES ('2', 'YD_10M_2MONTH', '10', '测试套餐001', '1', '10', '11', '', 'month', '2', '32', 'month', '', '', '0', '1468839531', '1', '0');
INSERT INTO `ckb_package` VALUES ('3', 'DX_10M_2MONTH', '10', '测试套餐002', '1', '10', '12', '运营商编号', 'month', '2', '3', 'month', '', '套餐描述', '1468839411', '1468980118', '1', '0');

-- ----------------------------
-- Table structure for ckb_user
-- ----------------------------
DROP TABLE IF EXISTS `ckb_user`;
CREATE TABLE `ckb_user` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL COMMENT '用户名',
  `nickname` varchar(50) NOT NULL COMMENT '昵称/姓名',
  `password` char(32) NOT NULL COMMENT '密码',
  `last_login_time` int(11) unsigned DEFAULT '0' COMMENT '上次登录时间',
  `last_login_ip` varchar(40) DEFAULT NULL COMMENT '上次登录IP',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `phone` varchar(20) DEFAULT '' COMMENT '手机',
  `head_img` varchar(255) DEFAULT '/Updatas/d_head.jpg' COMMENT '头像',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `info` text COMMENT '信息',
  `group_ids` varchar(255) DEFAULT NULL COMMENT '用户组ID',
  `del` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='后台用户表';

-- ----------------------------
-- Records of ckb_user
-- ----------------------------
INSERT INTO `ckb_user` VALUES ('1', 'admin', '超级管理员', '21232f297a57a5a743894a0e4a801fc3', '1458905437', '127.0.0.1', '912524639@qq.com', '00000000000', '/Updatas/d_head.jpg', '哈哈', '1458034376', '1458034376', '1', '', '1,2', '0');
INSERT INTO `ckb_user` VALUES ('2', 'user', '测试', 'd41d8cd98f00b204e9800998ecf8427e', '0', null, '1@1.com', '12315646', '/Updatas/d_head.jpg', '这只是一个测试', '1468568531', '1468569406', '1', '信息', '1,6', '0');

-- ----------------------------
-- Table structure for ckb_user_batch_expense
-- ----------------------------
DROP TABLE IF EXISTS `ckb_user_batch_expense`;
CREATE TABLE `ckb_user_batch_expense` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '批量续费ID',
  `sn` varchar(20) NOT NULL COMMENT '批量续费批号',
  `imsi` varchar(50) NOT NULL,
  `package_sn` varchar(30) NOT NULL COMMENT '续费套餐ID',
  `package_name` varchar(255) NOT NULL,
  `expense_time` varchar(5) NOT NULL COMMENT '续费周期（月）',
  `carrieroperator` tinyint(2) NOT NULL,
  `service_type` tinyint(2) NOT NULL COMMENT '服务方式',
  `plate_number` varchar(50) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_user_batch_expense
-- ----------------------------
INSERT INTO `ckb_user_batch_expense` VALUES ('1', '10000000000', '222222222222222', '3', '', '1', '0', '0', '', '', '0', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('2', '10000000000', '222222222222222', '2', '', '2', '0', '0', '', '', '0', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('3', '20160720164803-6532', '', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469004483', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('4', '20160720164840-5466', '', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469004520', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('5', '20160720165008-3438', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469004608', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('6', '20160720165132-8773', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469004692', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('7', '20160720170156-1460', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005316', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('8', '20160720170207-8412', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005327', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('9', '20160720170239-6033', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005359', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('10', '20160720170304-6845', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005384', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('11', '20160720170358-7861', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005438', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('12', '20160720170402-3838', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005442', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('13', '20160720170432-3891', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005472', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('14', '20160720170539-3772', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005539', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('15', '20160720170649-8522', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005609', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('16', '20160720170658-8054', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005618', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('17', '20160720170707-8731', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005627', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('18', '20160720170727-8529', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005647', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('19', '20160720170736-9212', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005656', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('20', '20160720170826-3733', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469005706', '0');
INSERT INTO `ckb_user_batch_expense` VALUES ('21', '20160721083449-1487', '222222222222222', 'YD_10M_2MONTH', '10M流量包', '2', '11', '10', '闽ABC123', '13459444044', '1469061289', '0');

-- ----------------------------
-- Table structure for ckb_user_order
-- ----------------------------
DROP TABLE IF EXISTS `ckb_user_order`;
CREATE TABLE `ckb_user_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(30) NOT NULL COMMENT '订单编号',
  `package_id` int(11) NOT NULL COMMENT '套餐ID',
  `new_package_id` int(11) NOT NULL COMMENT '新套餐ID',
  `imsi` varchar(30) NOT NULL COMMENT 'imsi',
  `pay_type` tinyint(2) NOT NULL COMMENT '支付方式（10微信支付，11支付宝，12银行转账）',
  `service_type` tinyint(2) NOT NULL COMMENT '服务方式(10续费、11开通、12更换)',
  `pay_sn` varchar(30) NOT NULL COMMENT '支付流水号',
  `pay_account` varchar(50) NOT NULL COMMENT '支付账号',
  `pay_value` varchar(10) NOT NULL COMMENT '支付金额',
  `pay_time` int(11) NOT NULL COMMENT '支付时间',
  `pay_status` tinyint(2) NOT NULL COMMENT '支付状态（10已支付，11未支付）',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `del` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_user_order
-- ----------------------------
INSERT INTO `ckb_user_order` VALUES ('1', '1111111111111', '3', '0', '222222222222222', '10', '11', '3333333333333333', '4444444444444444444444', '15000', '1468893444', '10', '0', '0');

-- ----------------------------
-- Table structure for ckb_user_package
-- ----------------------------
DROP TABLE IF EXISTS `ckb_user_package`;
CREATE TABLE `ckb_user_package` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户套餐ID',
  `sn` varchar(10) NOT NULL COMMENT '绑定号',
  `custom_no` varchar(30) NOT NULL COMMENT '客户编号',
  `package_sn` varchar(30) NOT NULL COMMENT '套餐SN',
  `service_start_time` int(11) NOT NULL COMMENT '服务开始时间',
  `service_end_time` int(11) NOT NULL COMMENT '服务结束时间',
  `del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除（1是，0否）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_user_package
-- ----------------------------
INSERT INTO `ckb_user_package` VALUES ('1', '100000', 'No00000000001', '3', '1468892479', '1475251200', '1');
INSERT INTO `ckb_user_package` VALUES ('2', '100000', 'No00000000001', '3', '1468893444', '1475251200', '1');
INSERT INTO `ckb_user_package` VALUES ('3', '100000', 'No00000000001', '3', '1468893453', '1475251200', '1');
INSERT INTO `ckb_user_package` VALUES ('4', '100000', 'No00000000001', '3', '1468944000', '1469116800', '1');
INSERT INTO `ckb_user_package` VALUES ('5', '100000', 'No00000000001', '2', '1469376000', '1469721600', '1');
INSERT INTO `ckb_user_package` VALUES ('6', '100000', 'No00000000001', '2', '1469376000', '1469721600', '1');
INSERT INTO `ckb_user_package` VALUES ('7', '100000', 'No00000000001', '2', '1469376000', '1469721600', '1');
INSERT INTO `ckb_user_package` VALUES ('8', '100000', 'No00000000001', '2', '1469376000', '1469721600', '1');
INSERT INTO `ckb_user_package` VALUES ('9', '100000', 'No00000000001', '', '1468998881', '1469116800', '1');
INSERT INTO `ckb_user_package` VALUES ('10', '100000', 'No00000000001', 'YD_10M_2MONTH', '1468998952', '1469116800', '1');
INSERT INTO `ckb_user_package` VALUES ('11', '100000', 'No00000000001', 'YD_10M_2MONTH', '1468944000', '1469116800', '1');
INSERT INTO `ckb_user_package` VALUES ('12', '100000', 'No00000000001', 'DX_10M_2MONTH', '1468999260', '1469116800', '1');
INSERT INTO `ckb_user_package` VALUES ('13', '100000', 'No00000000001', 'DX_10M_2MONTH', '1468999413', '1469116800', '1');
INSERT INTO `ckb_user_package` VALUES ('14', '', 'No00000000001', 'YD_10M_2MONTH', '1468999592', '1530374400', '0');

-- ----------------------------
-- Table structure for ckb_user_package_cost
-- ----------------------------
DROP TABLE IF EXISTS `ckb_user_package_cost`;
CREATE TABLE `ckb_user_package_cost` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `custom_no` varchar(30) NOT NULL COMMENT '客户编号',
  `type` tinyint(2) NOT NULL COMMENT '类型（10流量，11短信）',
  `cost_value` varchar(10) NOT NULL COMMENT '已用值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ckb_user_package_cost
-- ----------------------------
INSERT INTO `ckb_user_package_cost` VALUES ('1', 'No00000000001', '10', '2');
