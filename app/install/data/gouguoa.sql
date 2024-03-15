/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50644
 Source Host           : localhost:3306
 Source Schema         : house

 Target Server Type    : MySQL
 Target Server Version : 50644
 File Encoding         : 65001

 Date: 16/11/2021 15:16:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for oa_admin
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin`;
CREATE TABLE `oa_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '' COMMENT '登录用户名',
  `pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '登录密码',
  `salt` varchar(100) NOT NULL DEFAULT '' COMMENT '密码盐',
  `reg_pwd` varchar(100) NOT NULL DEFAULT '' COMMENT '初始密码',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '员工姓名',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `mobile` bigint(11) NOT NULL DEFAULT 0 COMMENT '手机号码',
  `sex` int(255) NOT NULL DEFAULT 0 COMMENT '性别1男,2女',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '昵称',
  `thumb` varchar(255) NOT NULL COMMENT '头像',
  `theme` varchar(255) NOT NULL DEFAULT 'white' COMMENT '系统主题',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '部门id',
  `position_id` int(11) NOT NULL DEFAULT 0 COMMENT '职位id',
  `type` int(1) NOT NULL DEFAULT 0 COMMENT '员工类型：0未设置,1正式,2试用,3实习',
  `age` int(3) NOT NULL DEFAULT 0 COMMENT '年龄',
  `native_place` varchar(255) NOT NULL DEFAULT '' COMMENT '籍贯',
  `idcard` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证',  
  `education` varchar(255) NOT NULL DEFAULT '' COMMENT '学历',
  `bank_account` varchar(255) NOT NULL DEFAULT '' COMMENT '银行账号',
  `bank_info` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡信息',
  `desc` mediumtext NULL COMMENT '员工个人简介',
  `is_hide` int(1) NOT NULL DEFAULT 0 COMMENT '是否隐藏联系方式:0否,1是',
  `entry_time` int(11) NOT NULL DEFAULT 0 COMMENT '员工入职日期',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '注册时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新信息时间',
  `last_login_time` int(11) NOT NULL DEFAULT 0 COMMENT '最后登录时间',
  `login_num` int(11) NOT NULL DEFAULT 0 COMMENT '登录次数',
  `last_login_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `is_lock` int(1) NOT NULL DEFAULT 0 COMMENT '是否锁屏:1是0否',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除,0禁止登录,1正常,2离职',  
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工表';

-- ----------------------------
-- Table structure for oa_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_log`;
CREATE TABLE `oa_admin_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `type` varchar(80) NOT NULL DEFAULT '' COMMENT '操作类型',
  `action` varchar(80) NOT NULL DEFAULT '' COMMENT '操作动作',
  `subject` varchar(80) NOT NULL DEFAULT '' COMMENT '操作主体',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(32) NOT NULL DEFAULT '' COMMENT '控制器',
  `function` varchar(32) NOT NULL DEFAULT '' COMMENT '方法',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT '登录ip',
  `param_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作数据id',
  `param` mediumtext NULL COMMENT '参数json格式',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工操作日志表';

-- ----------------------------
-- Table structure for oa_admin_module
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_module`;
CREATE TABLE `oa_admin_module`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '模块名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '模块目录，唯一，字母',
  `type` int(2) NOT NULL DEFAULT 1 COMMENT '状态:1系统模块,2普通模块',
  `sourse` int(2) NOT NULL DEFAULT 1 COMMENT '来源:1官方,2第三方',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '功能模块表';

-- ----------------------------
-- Records of oa_admin_module
-- ----------------------------
INSERT INTO `oa_admin_module` VALUES (1, '系统模块', 'home', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (2, '人事模块', 'user', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (3, '行政模块', 'adm', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (4, '公告模块', 'note', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (5, 'OA模块', 'oa', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (6, '财务模块', 'finance', 1, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (7, '客户模块', 'customer', 2, 1, 1639562910, 0);
INSERT INTO `oa_admin_module` VALUES (8, '合同模块', 'contract', 2, 1, 1656142368, 0);
INSERT INTO `oa_admin_module` VALUES (9, '项目模块', 'project', 2, 1, 1656142368, 0);
INSERT INTO `oa_admin_module` VALUES (10, '知识模块', 'article', 2, 1, 1656143065, 0);

-- ----------------------------
-- Table structure for oa_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_rule`;
CREATE TABLE `oa_admin_rule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `src` varchar(255) NOT NULL DEFAULT '' COMMENT 'url链接',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '日志操作名称',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '所属模块',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `menu` int(1) NOT NULL DEFAULT 0 COMMENT '是否是菜单,1是,2不是',
  `sort` int(11) NOT NULL DEFAULT 1 COMMENT '越小越靠前',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态,0禁用,1正常',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '菜单及权限表';

-- ----------------------------
-- Records of oa_admin_rule
-- ----------------------------
INSERT INTO `oa_admin_rule` VALUES (1, 0, '', '系统管理', '系统管理', 'home', 'icon-jichupeizhi', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (2, 0, '', '基础数据', '基础数据', 'home', 'icon-hetongshezhi', 1, 2, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (3, 0, '', '人力资源', '人力资源', 'user', 'icon-renshishezhi', 1, 3, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (4, 0, '', '行政管理', '行政管理', 'adm', 'icon-banjiguanli', 1, 4, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (5, 0, '', '企业公告', '企业公告', 'note', 'icon-zhaoshengbaobiao', 1, 5, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (6, 0, '', '办公审批', '办公审批', 'oa', 'icon-shenpishezhi', 1, 6, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (7, 0, '', '日常办公', '日常办公', 'oa', 'icon-kaoshijihua', 1, 7, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (8, 0, '', '财务管理', '财务管理', 'finance', 'icon-yuangongtidian', 1, 8, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (9, 1, 'home/conf/index', '系统配置', '系统配置', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (10, 9, 'home/conf/add', '新建/编辑', '配置项', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (11, 9, 'home/conf/delete', '删除', '配置项', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (12, 9, 'home/conf/edit', '编辑', '配置详情', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (13, 1, 'home/module/index', '功能模块', '功能模块', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (14, 13, 'home/module/install', '安装', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (15, 13, 'home/module/upgrade', '升级', '功能模块', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (16, 13, 'home/module/uninstall', '卸载', '功能模块', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (17, 1, 'home/rule/index', '功能节点', '功能节点', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (18, 17, 'home/rule/add', '新建/编辑', '功能节点', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (19, 17, 'home/rule/delete', '删除', '功能节点', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (20, 1, 'home/role/index', '角色权限', '角色权限', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (21, 20, 'home/role/add', '新建/编辑', '角色权限', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (22, 20, 'home/role/delete', '删除', '角色权限', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (23, 1, 'home/dataauth/index', '数据权限', '数据权限', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (24, 23, 'home/dataauth/edit', '编辑', '数据权限', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (25, 1, 'home/log/index', '操作日志', '操作日志', 'home', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (26, 1, 'home/database/database', '备份数据', '数据备份', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (27, 26, 'home/database/backup', '备份数据表', '数据', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (28, 26, 'home/database/optimize', '优化数据表', '数据表', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (29, 26, 'home/database/repair', '修复数据表', '数据表', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (30, 1, 'home/database/backuplist', '还原数据', '数据还原', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (31, 30, 'home/database/import', '还原数据表', '数据', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (32, 30, 'home/database/downfile', '下载备份数据', '备份数据', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (33, 30, 'home/database/del', '删除备份数据', '备份数据', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (34, 2, 'home/cate/flow_type', '审批类型', '审批类型', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (35, 34, 'home/cate/flow_type_add', '新建/编辑', '审批类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (36, 34, 'home/cate/flow_type_check', '设置', '审批类型', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (37, 2, 'home/flow/index', '审批流程', '审批流程', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (38, 37, 'home/flow/add', '新建/编辑', '审批流程', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (39, 37, 'home/flow/delete', '删除', '审批流程', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (40, 37, 'home/flow/check', '设置', '审批流程', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (41, 2, 'home/cate/expense_cate', '报销类型', '报销类型', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (42, 41, 'home/cate/expense_cate_add', '新建/编辑', '报销类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (43, 41, 'home/cate/expense_cate_check', '设置', '报销类型', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (44, 2, 'home/cate/cost_cate', '费用类型', '费用类型', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (45, 44, 'home/cate/cost_cate_add', '新建/编辑', '费用类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (46, 44, 'home/cate/cost_cate_check', '设置', '费用类型', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (47, 2, 'home/cate/subject', '企业主体', '企业主体', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (48, 47, 'home/cate/subject_add', '新建/编辑', '企业主体', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (49, 47, 'home/cate/subject_check', '设置', '企业主体', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (50, 2, 'home/cate/industry_cate', '行业类型', '行业类型', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (51, 50, 'home/cate/industry_cate_add', '新建/编辑', '行业类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (52, 50, 'home/cate/industry_cate_check', '设置', '行业类型', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (53, 2, 'home/cate/work_cate', '工作类别', '工作类别', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (54, 53, 'home/cate/work_cate_add', '新建/编辑', '工作类别', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (55, 53, 'home/cate/work_cate_check', '设置', '工作类别', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (56, 2, 'home/cate/services_cate', '服务类型', '服务类型', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (57, 56, 'home/cate/services_cate_add', '新建/编辑', '服务类型', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (58, 56, 'home/cate/services_cate_check', '设置', '服务类型', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (59, 2, 'home/keywords/index', '关 键 字','关键字', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (60, 59, 'home/keywords/add', '新建/编辑','关键字', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (61, 59, 'home/keywords/delete', '删除','关键字', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (62, 3, 'user/department/index', '部门架构', '部门', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (63, 62, 'user/department/add', '新建/编辑', '部门', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (64, 62, 'user/department/delete', '删除', '部门', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (65, 3, 'user/position/index', '岗位职称', '岗位职称', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (66, 65, 'user/position/add', '新建/编辑', '岗位职称', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (67, 65, 'user/position/delete', '删除', '岗位职称', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (68, 65, 'user/position/view', '查看', '岗位职称', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (69, 3, 'user/user/index', '企业员工', '员工', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (70, 69, 'user/user/add', '新建/编辑', '员工', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (71, 69, 'user/user/view', '查看', '员工信息', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (72, 69, 'user/user/set', '设置', '员工状态', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (73, 69, 'user/user/reset_psw', '重设密码', '员工密码', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (74, 3, 'user/personal/change', '人事调动', '人事调动', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (75, 74, 'user/personal/change_add', '新建/编辑', '人事调动', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (76, 3, 'user/personal/leave', '离职档案', '离职档案', 'user', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (77, 76, 'user/personal/leave_add', '新建/编辑', '离职档案', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (78, 76, 'user/personal/leave_delete', '删除', '离职档案', 'user', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (79, 4, 'adm/seal/seal_cate', '印章管理', '印章', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (80, 79, 'adm/seal/seal_cate_add', '新建/编辑', '印章', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (81, 79, 'adm/seal/seal_cate_check', '设置', '印章', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (82, 4, 'adm/car/car_cate', '车辆管理', '车辆', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (83, 82, 'adm/car/car_cate_add', '新建/编辑', '车辆', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (84, 82, 'adm/car/car_cate_check', '设置', '车辆', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (85, 4, 'adm/meeting/meeting_cate', '会议室管理', '会议室', 'adm', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (86, 85, 'adm/meeting/meeting_cate_add', '新建/编辑', '会议室', 'adm', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (87, 85, 'adm/meeting/meeting_cate_check', '设置', '会议室', 'adm', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (88, 5, 'note/index/note_cate', '公告类型', '公告类型', 'note', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (89, 88, 'note/index/note_cate_add', '新建/编辑', '公告类型', 'note', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (90, 88, 'note/index/note_cate_delete', '删除', '公告类型', 'note', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (91, 5, 'note/index/index', '公告列表', '公告', 'note', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (92, 91, 'note/index/add', '新建/编辑', '公告', 'note', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (93, 91, 'note/index/delete', '删除', '公告', 'note', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (94, 91, 'note/index/view', '查看', '公告', 'note', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (95, 6, 'oa/approve/index', '我发起的', '办公审批', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (96, 95, 'oa/approve/add', '新建/编辑', '办公审批', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (97, 95, 'oa/approve/view', '查看', '办公审批', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (98, 6, 'oa/approve/list', '我处理的', '办公审批', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (99, 6, 'oa/approve/copy', '抄送给我的', '办公审批', 'oa', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (100, 7, 'oa/plan/index', '日程安排', '日程安排', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (101, 100, 'oa/plan/add', '新建/编辑', '日程安排', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (102, 100, 'oa/plan/delete', '删除', '日程安排', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (103, 100, 'oa/plan/detail', '查看', '日程安排', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (104, 7, 'oa/plan/calendar', '日程日历', '日程安排', 'oa', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (105, 7, 'oa/schedule/index', '工作记录', '工作记录', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (106, 105, 'oa/schedule/add', '新建/编辑', '工作记录', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (107, 105, 'oa/schedule/delete', '删除', '工作记录', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (108, 105, 'oa/schedule/detail', '查看', '工作记录', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (109, 105, 'oa/schedule/update_labor_time', '更改工时', '工时', 'oa', '', 0, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (110, 7, 'oa/schedule/calendar', '工作日历', '工作日历', 'oa', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (111, 7, 'oa/work/index', '工作汇报', '工作汇报', 'oa', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (112, 111, 'oa/work/add', '新建/编辑', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (113, 111, 'oa/work/send', '发送', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (114, 111, 'oa/work/read', '查看', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (115, 111, 'oa/work/delete', '删除', '工作汇报', 'oa', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (116, 8, '', '报销管理', '报销', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (117, 116, 'finance/expense/index', '我申请的', '报销', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (118, 117, 'finance/expense/add', '新建/编辑', '报销', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (119, 117, 'finance/expense/delete', '删除', '报销', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (120, 117, 'finance/expense/view', '查看', '报销', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (121, 116, 'finance/expense/list', '我处理的', '报销', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (122, 116, 'finance/expense/copy', '抄送给我的', '报销', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (123, 116, 'finance/expense/checkedlist', '打款(管理专用)', '报销', 'finance', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (124, 8, '', '发票管理', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (125, 124, 'finance/invoice/index', '我申请的', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (126, 125, 'finance/invoice/add', '新建/编辑', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (127, 125, 'finance/invoice/delete', '删除', '发票', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (128, 125, 'finance/invoice/view', '查看', '发票', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (129, 124, 'finance/invoice/list', '我处理的', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (130, 124, 'finance/invoice/copy', '抄送给我的', '发票', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (131, 124, 'finance/invoice/checkedlist', '开票(管理专用)', '发票', 'finance', '', 1, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (132, 8, 'finance/income/index', '到账管理', '到账记录', 'finance', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (133, 132, 'finance/income/add', '新建/编辑', '到账记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (134, 132, 'finance/income/view', '查看', '到账记录', 'finance', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (135, 132, 'finance/income/delete', '删除', '到账记录', 'finance', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (136, 0, '', '客户管理', '客户管理', 'customer', 'icon-kehuguanli', 1, 9, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (137, 136, 'customer/grade/index', '客户等级', '客户等级', 'customer', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (138, 137, 'customer/grade/grade_add', '新建/编辑', '客户等级', 'customer', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (139, 137, 'customer/grade/grade_check', '设置', '客户等级', 'customer', '', 2,0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (140, 136, 'customer/source/index', '客户渠道', '客户渠道', 'customer', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (141, 140, 'customer/source/source_add', '新建/编辑', '客户渠道', 'customer', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (142, 140, 'customer/source/source_check', '设置', '客户渠道', 'customer', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (143, 136, 'customer/index/rush', '抢 客 宝', '抢客宝', 'customer', '', 1, 0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (144, 136, 'customer/index/index', '客户列表', '客户列表', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (145, 144, 'customer/index/add', '新建/编辑', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (146, 144, 'customer/index/view', '查看', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (147, 144, 'customer/index/get', '获取', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (148, 144, 'customer/index/to_sea', '转入公海', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (149, 136, 'customer/index/sea', '公海客户', '客户', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (150, 149, 'customer/index/distribute', '分配客户', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (151, 149, 'customer/index/to_trash', '转入废弃池', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (152, 136, 'customer/index/trash', '废弃客户', '客户', 'customer', '', 1, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (153, 152, 'customer/index/delete', '删除', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (154, 152, 'customer/index/revert', '还原', '客户', 'customer', '', 2, 0, 1, 1556143065, 0);
INSERT INTO `oa_admin_rule` VALUES (155, 136, 'customer/contact/index', '客户联系人', '联系人', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (156, 155, 'customer/contact/contact_add', '新建/编辑', '联系人', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (157, 155, 'customer/contact/contact_del', '删除', '联系人', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (158, 136, 'customer/chance/index', '销售机会', '销售机会', 'customer', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (159, 158, 'customer/chance/chance_add', '新建/编辑', '销售机会', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (160, 158, 'customer/chance/chance_view', '查看', '销售机会', 'customer', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (161, 158, 'customer/chance/chance_del', '删除', '销售机会', 'customer', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (162, 0, '', '合同协议', '合同协议', 'contract', 'icon-hetongyidong', 1, 10, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (163, 162, 'contract/cate/cate', '合同类别', '合同类别', 'contract', '', 1, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (164, 163, 'contract/cate/cate_add', '新建/编辑', '合同类别', 'contract', '', 2, 0, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (165, 163, 'contract/cate/cate_check', '设置', '合同类别', 'contract', '', 2,0, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (166, 162, 'contract/index/index', '合同列表', '合同列表', 'contract', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (167, 166, 'contract/index/add', '新建/编辑', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (168, 166, 'contract/index/view', '查看', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (169, 166, 'contract/index/delete', '删除', '合同', 'contract', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (170, 162, 'contract/index/archive', '合同归档', '合同归档', 'contract', '', 1, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (171, 0, '', '项目管理', '项目管理', 'project', 'icon-xiangmuguanli', 1, 11, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (172, 171, 'project/index/index', '项目列表', '项目', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (173, 172, 'project/index/add', '新建', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (174, 172, 'project/index/edit', '编辑', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (175, 172, 'project/index/view', '查看', '项目', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (176, 172, 'project/index/delete', '删除', '项目', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (177, 171, 'project/task/index', '任务列表', '任务', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (178, 177, 'project/task/add', '新建', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (179, 177, 'project/task/edit', '编辑', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (180, 177, 'project/task/view', '查看', '任务', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (181, 177, 'project/task/delete', '删除', '任务', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (182, 171, 'project/task/task_time', '任务工时', '工时', 'project', '', 1, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (183, 171, 'project/document/index', '文档列表', '文档', 'project', '', 1, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (184, 183, 'project/document/add', '新建/编辑', '文档', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (185, 183, 'project/document/view', '查看', '文档', 'project', '', 2, 0, 1, 1656142368, 0);
INSERT INTO `oa_admin_rule` VALUES (186, 183, 'project/document/delete', '删除', '文档', 'project', '', 2, 0, 1, 1656142368, 0);

INSERT INTO `oa_admin_rule` VALUES (187, 0, '', '知识文章', '知识文章', 'article', 'icon-kecheng', 1, 12, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (188, 187, 'article/cate/cate', '知识类型', '知识类型', 'article', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (189, 188, 'article/cate/cate_add', '新建/编辑', '知识类型', 'article', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (190, 188, 'article/cate/cate_delete', '删除', '知识类型', 'article', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (191, 187, 'article/index/index', '共享知识', '知识文章', 'article', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (192, 187, 'article/index/list', '个人知识', '知识文章', 'article', '', 1, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (193, 192, 'article/index/add', '新建/编辑', '知识文章', 'article', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (194, 192, 'article/index/view', '查看', '知识文章', 'article', '', 2, 0, 1, 1656143065, 0);
INSERT INTO `oa_admin_rule` VALUES (195, 192, 'article/index/delete', '删除', '知识文章', 'article', '', 2, 0, 1, 1656143065, 0);

INSERT INTO `oa_admin_rule` VALUES (196, 2, 'home/files/index', '附件管理','附件管理', 'home', '', 1, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (197, 196, 'home/files/edit', '编辑附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (198, 196, 'home/files/move', '移动附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (199, 196, 'home/files/delete', '删除附件','附件', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (200, 196, 'home/files/get_group', '附件分组','附件分组', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (201, 196, 'home/files/add_group', '新建/编辑','附件分组', 'home', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (202, 196, 'home/files/del_group', '删除附件分组','附件分组', 'home', '', 2, 1, 1, 0, 0);

INSERT INTO `oa_admin_rule` VALUES (203, 76, 'user/personal/leave_check', '资料交接', '离职资料', 'user', '', 2, 1, 1, 0, 0);
INSERT INTO `oa_admin_rule` VALUES (204, 136, 'customer/trace/index', '跟进记录', '跟进记录', 'customer', '', 1, 0, 1, 1656143065, 0);
-- ----------------------------
-- Table structure for oa_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_admin_group`;
CREATE TABLE `oa_admin_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT 1,
  `rules` mediumtext NULL COMMENT '用户组拥有的规则id',
  `layouts` mediumtext NULL COMMENT '首页展示模块',
  `desc` mediumtext NULL COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '员工权限分组表';

-- ----------------------------
-- Records of oa_admin_group
-- ----------------------------
INSERT INTO `oa_admin_group` VALUES (1, '超级员工权限', 1, '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204', '1,2,3,4,5,6,7,8,9,10,11,12','超级员工权限，拥有系统的最高权限，不可修改。', 0, 0);
INSERT INTO `oa_admin_group` VALUES (2, '总经理权限', 1, '1,9,13,17,20,23,25,26,30,2,34,37,41,44,47,50,53,56,59,3,62,65,68,69,71,74,76,4,79,82,85,5,88,91,92,93,94,6,95,96,97,98,99,7,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,8,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,140,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204','1,2,3,4,5,6,7,8,9,10,11,12', '总经理的管理权限，可根据公司的具体需求调整。', 0, 0);
INSERT INTO `oa_admin_group` VALUES (3, '普通员工权限', 1, '5,88,91,92,93,6,95,96,97,98,99,7,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,8,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,140,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204','1,2,3,4,5,6,7,8,9,10,11,12', '普通员工管理权限，可根据公司的具体需求调整。', 0, 0);

-- ----------------------------
-- Table structure for oa_data_auth
-- ----------------------------
DROP TABLE IF EXISTS `oa_data_auth`;
CREATE TABLE `oa_data_auth`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '权限名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '权限标识唯一，字母',
  `desc` mediumtext NULL COMMENT '备注描述',
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '所属模块，唯一，字母',
  `uids` mediumtext NULL COMMENT '权限用户，1,2,3',
  `expected_1` int(11) NOT NULL DEFAULT 0 COMMENT '预备字段1，可作为预备权限的控制',
  `expected_2` int(11) NOT NULL DEFAULT 0 COMMENT '预备字段2，可作为预备权限的控制',
  `expected_3` int(11) NOT NULL DEFAULT 0 COMMENT '预备字段3，可作为预备权限的控制',
  `expected_4` int(11) NOT NULL DEFAULT 0 COMMENT '预备字段4，可作为预备权限的控制',
  `expected_5` int(11) NOT NULL DEFAULT 0 COMMENT '预备字段5，可作为预备权限的控制',
  `conf_1` mediumtext NULL COMMENT '配置字段1，可作为预配置内容',
  `conf_2` mediumtext NULL COMMENT '配置字段2，可作为预配置内容',
  `conf_3` mediumtext NULL COMMENT '配置字段3，可作为预配置内容',
  `conf_4` mediumtext NULL COMMENT '配置字段4，可作为预配置内容',
  `conf_5` mediumtext NULL COMMENT '配置字段5，可作为预配置内容',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '数据权限表';

-- ----------------------------
-- Records of  oa_data_auth
-- ----------------------------
INSERT INTO `oa_data_auth` VALUES (1, '财务模块','finance_admin','开具发票、报销打款、财务到账相关数据权限配置。', 'finance', '',0,0,0,0,0,'','','','','',1656143065, 0);
INSERT INTO `oa_data_auth` VALUES (2, '客户模块','customer_admin','查看、转移客户等相关数据权限配置。', 'customer', '',10,100,0,0,0,'','','','','',1656143065, 0);
INSERT INTO `oa_data_auth` VALUES (3, '合同模块','contract_admin','查看、编辑、作废、中止合同等相关数据权限配置。', 'contract', '',1,1,0,0,0,'','','','','',1656143065, 0);
INSERT INTO `oa_data_auth` VALUES (4, '项目模块','project_admin','查看项目相关数据权限配置。', 'project', '',0,0,0,0,0,'立项阶段|实施阶段|验收阶段|交付阶段','','','','',1656143065, 0);

-- ----------------------------
-- Table structure for oa_config
-- ----------------------------
DROP TABLE IF EXISTS `oa_config`;
CREATE TABLE `oa_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置名称',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标识',
  `content` mediumtext NULL COMMENT '配置内容',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COMMENT = '系统配置表';

-- ----------------------------
-- Records of oa_config
-- ----------------------------
INSERT INTO `oa_config` VALUES (1, '网站配置', 'web', 'a:15:{s:2:"id";s:1:"1";s:11:"admin_title";s:8:"勾股OA";s:9:"menu_mode";s:9:"classical";s:6:"domain";s:24:"https://www.gougucms.com";s:4:"logo";s:31:"/static/home/images/syslogo.png";s:4:"file";s:0:"";s:10:"small_logo";s:37:"/static/home/images/syslogo_small.png";s:3:"icp";s:21:"粤ICP备xxxxxxx号-1";s:5:"beian";s:27:"粤公网安备xxxxxxx号-1";s:8:"keywords";s:8:"勾股OA";s:4:"desc";s:550:"勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理、消息管理、审批管理、日常办公、客户管理、合同管理、项目管理、财务管理、知识管理、附件管理等模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。";s:7:"version";s:6:"4.0.24";s:9:"copyright";s:36:"© 2023 gougucms.com GPL-3.0 license";s:9:"msg_sound";s:1:"1";s:9:"watermark";s:1:"1";}', 1, 1612514630, 1638010154);
INSERT INTO `oa_config` VALUES (2, '邮箱配置', 'email', 'a:8:{s:2:\"id\";s:1:\"2\";s:4:\"smtp\";s:11:\"smtp.qq.com\";s:9:\"smtp_port\";s:3:\"465\";s:9:\"smtp_user\";s:15:\"gougucms@qq.com\";s:8:\"smtp_pwd\";s:6:\"123456\";s:4:\"from\";s:24:\"勾股CMS系统管理员\";s:5:\"email\";s:18:\"admin@gougucms.com\";s:8:\"template\";s:485:\"<p>勾股办公是一款基于ThinkPHP6 + Layui + MySql打造的，简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。</p>\";}', 1, 1612521657, 1637075205);
INSERT INTO `oa_config` VALUES (3, 'Api Token配置', 'token', 'a:5:{s:2:\"id\";s:1:\"3\";s:3:\"iss\";s:15:\"oa.gougucms.com\";s:3:\"aud\";s:7:\"gouguoa\";s:7:\"secrect\";s:7:\"GOUGUOA\";s:7:\"exptime\";s:4:\"3600\";}', 1, 1627313142, 1638010233);
INSERT INTO `oa_config` VALUES (4, '其他配置', 'other', 'a:3:{s:2:\"id\";s:1:\"5\";s:6:\"author\";s:15:\"勾股工作室\";s:7:\"version\";s:13:\"v1.2021.07.28\";}', 1, 1613725791, 1635953640);

-- ----------------------------
-- Table structure for oa_department
-- ----------------------------
DROP TABLE IF EXISTS `oa_department`;
CREATE TABLE `oa_department`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级部门id',
  `leader_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人ID',
  `phone` varchar(60) NOT NULL DEFAULT '' COMMENT '部门联系电话',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序：越大越靠前',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '部门组织';

-- ----------------------------
-- Records of oa_department
-- ----------------------------
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (1, '董事会', 0, 0, '13688888888');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (2, '人事部', 1, 0, '13688888889');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (3, '财务部', 1, 0, '13688888898');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (4, '市场部', 1, 0, '13688888978');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (5, '销售部', 1, 0, '13688889868');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (6, '技术部', 1, 0, '13688898858');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (7, '客服部', 1, 0, '13688988848');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (8, '销售一部', 5, 0, '13688998838');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (9, '销售二部', 5, 0, '13688999828');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (10, '销售三部', 5, 0, '13688999918');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (11, '产品部', 6, 0, '13688888886');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (12, '设计部', 6, 0, '13688888876');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (13, '研发部', 6, 0, '13688888666');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (14, '客服一部', 7, 0, '13688888865');
INSERT INTO `oa_department`(`id`, `title`, `pid`, `leader_id`, `phone`) VALUES (15, '客服二部', 7, 0, '13688888855');

-- ----------------------------
-- Table structure for oa_department_change
-- ----------------------------
DROP TABLE IF EXISTS `oa_department_change`;
CREATE TABLE `oa_department_change`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `from_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '原部门id',
  `to_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '调到部门id',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `move_time` int(11) NOT NULL DEFAULT 0 COMMENT '调动时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人事调动部门记录表';

-- ----------------------------
-- Table structure for oa_personal_quit
-- ----------------------------
DROP TABLE IF EXISTS `oa_personal_quit`;
CREATE TABLE `oa_personal_quit`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `lead_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门负责人',
  `connect_uids` varchar(100) NOT NULL DEFAULT '' COMMENT '参与交接人,多',
  `connect_id` int(11) NOT NULL DEFAULT 0 COMMENT '资料交接人',
  `connect_time` int(11) NOT NULL DEFAULT 0 COMMENT '资料交接时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `quit_time` int(11) NOT NULL DEFAULT 0 COMMENT '离职时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '人事离职记录表';

-- ----------------------------
-- Table structure for oa_flow_type
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_type`;
CREATE TABLE `oa_flow_type`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1假勤,2行政,3财务,4人事,5其他,6报销,发票,合同',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '审批名称',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '审批标识',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `department_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '应用部门ID（空为全部）1,2,3',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批类型';

-- ----------------------------
-- Records of oa_flow_type
-- ----------------------------
INSERT INTO `oa_flow_type` VALUES (1, 1, '请假', 'qingjia', 'icon-kechengziyuanguanli','', 1, 1639896302, 0);
INSERT INTO `oa_flow_type` VALUES (2, 1, '出差', 'chuchai', 'icon-jiaoshiguanli','', 1, 1641802838, 0);
INSERT INTO `oa_flow_type` VALUES (3, 1, '外出', 'waichu', 'icon-tuiguangguanli','', 1, 1641802858, 0);
INSERT INTO `oa_flow_type` VALUES (4, 1, '加班', 'jiaban', 'icon-xueshengchengji','', 1, 1641802892, 0);
INSERT INTO `oa_flow_type` VALUES (5, 2, '会议室预定', 'huiyishi', 'icon-kehuguanli','', 1, 1641802939, 0);
INSERT INTO `oa_flow_type` VALUES (6, 2, '公文流转', 'gongwen', 'icon-jiaoxuejihua','', 1, 1641802976, 0);
INSERT INTO `oa_flow_type` VALUES (7, 2, '物品维修', 'weixiu', 'icon-chuangjianxitong','', 1, 1641803005, 0);
INSERT INTO `oa_flow_type` VALUES (8, 2, '资质借用', 'zizhi', 'icon-luquchengji', '', 1, 1677661531, 0);
INSERT INTO `oa_flow_type` VALUES (9, 2, '用章', 'yongzhang', 'icon-shenpishezhi','', 1, 1641804126, 0);
INSERT INTO `oa_flow_type` VALUES (10, 2, '用车', 'yongche', 'icon-dongtaiguanli','', 1, 1641804283, 0);
INSERT INTO `oa_flow_type` VALUES (11, 2, '用车归还', 'yongcheguihai', 'icon-kaoheguanli','', 1, 1641804411, 0);
INSERT INTO `oa_flow_type` VALUES (12, 3, '借款', 'jiekuan', 'icon-zhangbuguanli','', 1, 1641804537, 0);
INSERT INTO `oa_flow_type` VALUES (13, 3, '付款', 'fukuan', 'icon-gongziguanli','', 1, 1641804601, 0);
INSERT INTO `oa_flow_type` VALUES (14, 3, '奖励', 'jiangli', 'icon-bulujiesuan','', 1, 1641804711, 0);
INSERT INTO `oa_flow_type` VALUES (15, 3, '采购', 'caigou', 'icon-shoufeiguanli','', 1, 1641804917, 0);
INSERT INTO `oa_flow_type` VALUES (16, 3, '活动经费', 'huodong', 'icon-shoufeipeizhi','', 1, 1641805110, 0);
INSERT INTO `oa_flow_type` VALUES (17, 4, '入职', 'ruzhi', 'icon-xueshengdaoru','', 1, 1641893853, 0);
INSERT INTO `oa_flow_type` VALUES (18, 4, '转正', 'zhuanzheng', 'icon-wodeshenpi','', 1, 1641893926, 0);
INSERT INTO `oa_flow_type` VALUES (19, 4, '离职', 'lizhi', 'icon-xuexitongji','', 1, 1641894048, 0);
INSERT INTO `oa_flow_type` VALUES (20, 4, '转岗', 'zhuangang', 'icon-xueshengyidong','', 1, 1654681664, 0);
INSERT INTO `oa_flow_type` VALUES (21, 4, '招聘需求', 'zhaopin', 'icon-xiaoxizhongxin','', 1, 1641894080, 0);
INSERT INTO `oa_flow_type` VALUES (22, 5, '通用审批', 'tongyong', 'icon-zhaoshengzhunbei','', 1, 1654685923, 0);
INSERT INTO `oa_flow_type` VALUES (23, 6, '报销', 'baoxiao', 'icon-jizhang','', 1, 1641804488, 0);
INSERT INTO `oa_flow_type` VALUES (24, 7, '发票', 'fapiao', 'icon-fuwuliebiao','', 1, 1642904833, 0);
INSERT INTO `oa_flow_type` VALUES (25, 8, '合同', 'hetong', 'icon-hetongshezhi','', 1, 1654692083, 0);

-- ----------------------------
-- Table structure for oa_flow
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow`;
CREATE TABLE `oa_flow`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '审批流名称',
  `check_type` tinyint(4) NOT NULL COMMENT '1固定审批流,2自由审批流,3可回退的审批流',
  `type` tinyint(4) NOT NULL COMMENT '应用模块,1假勤,2行政,3财务,4人事,5其他,6报销,7发票,8合同',
  `flow_cate` tinyint(11) NOT NULL DEFAULT 0 COMMENT '应用审批类型id',
  `department_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '应用部门ID（0为全部）1,2,3',
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '流程说明',
  `flow_list` varchar(1000) NULL DEFAULT '' COMMENT '流程数据序列化',
  `admin_id` int(11) NOT NULL COMMENT '创建人ID',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态 1启用，0禁用',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `delete_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '删除人ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批流程表';

-- ----------------------------
-- Records of oa_flow
-- ----------------------------
INSERT INTO `oa_flow` VALUES (1, '请假审批', 2, 1, 1, '', '', '请假审批流程', '', 1, 1644401970, 1644402071, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (2, '出差审批', 2, 1, 2, '', '', '请假审批流程', '', 1, 1644402054, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (3, '外出审批', 2, 1, 3, '', '', '外出审批流程', '', 1, 1644402116, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (4, '加班申请审批', 2, 1, 4, '', '', '加班申请审批流程', '', 1, 1644402147, 1644456735, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (5, '会议室预定审批', 2, 2, 5, '', '', '会议室预定审批流程', '', 1, 1644402193, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (6, '公文流转审批', 2, 2, 6, '', '', '公文流转审批流程', '', 1, 1644402386, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (7, '物品维修审批', 2, 2, 7, '', '', '物品维修审批流程', '', 1, 1644402473, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (8, '资质借用审批', 2, 2, 8, '', '', '资质借用审批流程', '', 1, 1677661607, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (9, '用章审批', 2, 2, 9, '', '', '用章审批流程', '', 1, 1644402499, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (10, '用车审批', 2, 2, 10, '', '', '用车审批流程', '', 1, 1644402525, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (11, '用车归还审批', 2, 2, 11, '', '', '用车归还审批流程', '', 1, 1644402549, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (12, '借款申请审批', 2, 3, 12, '', '', '借款申请审批流程', '', 1, 1644402611, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (13, '付款申请审批', 2, 3, 13, '', '', '付款申请审批流程', '', 1, 1644402679, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (14, '奖励申请审批', 2, 3, 14, '', '', '奖励申请审批流程', '', 1, 1644402705, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (15, '采购申请审批', 2, 3, 15, '', '', '采购申请审批流程', '', 1, 1644402739, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (16, '活动经费审批', 2, 3, 16, '', '', '活动经费审批流程', '', 1, 1644402762, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (17, '入职申请审批', 2, 4, 17, '', '', '入职申请审批流程', '', 1, 1644402791, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (18, '转正申请审批', 2, 4, 18, '', '', '转正申请审批流程', '', 1, 1644402812, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (19, '离职申请审批', 2, 4, 19, '', '', '离职申请审批流程', '', 1, 1644402834, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (20, '转岗申请审批', 2, 4, 20, '', '', '转岗申请审核流程', '', 1, 1654681954, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (21, '招聘需求审批', 2, 4, 21, '', '', '招聘需求审批流程', '', 1, 1644402855, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (22, '通用审批', 2, 5, 22, '', '', '通用审批流程', '', 1, 1654686338, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (23, '报销审批', 2, 6, 23, '', '', '报销审批流程', '', 1, 1644490024, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (24, '发票审批', 2, 7, 24, '', '', '发票审批流程', '', 1, 1644490053, 0, 1, 0, 0);
INSERT INTO `oa_flow` VALUES (25, '合同审批', 2, 8, 25, '', '', '合同审批流程', '', 1, 1654692519, 0, 1, 0, 0);

-- ----------------------------
-- Table structure for oa_cost_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_cost_cate`;
CREATE TABLE `oa_cost_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '费用类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '费用类型';

-- ----------------------------
-- Records of oa_cost_cate
-- ----------------------------
INSERT INTO `oa_cost_cate` VALUES (1, '差旅费', 1, 1639898199, 0);
INSERT INTO `oa_cost_cate` VALUES (2, '办公费', 1, 1639898434, 0);
INSERT INTO `oa_cost_cate` VALUES (3, '招待费', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (4, '交通费', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (5, '通讯费', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (6, '采购付款', 1, 1639898564, 0);
INSERT INTO `oa_cost_cate` VALUES (7, '其他', 1, 1639898564, 0);

-- ----------------------------
-- Table structure for oa_seal_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_seal_cate`;
CREATE TABLE `oa_seal_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '印章类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '印章类型';

-- ----------------------------
-- Records of oa_seal_cate
-- ----------------------------
INSERT INTO `oa_seal_cate` VALUES (1, '公章', 1, 1639899124, 0);
INSERT INTO `oa_seal_cate` VALUES (2, '合同章', 1, 1639899140, 0);
INSERT INTO `oa_seal_cate` VALUES (3, '法人章', 1, 1639899148, 0);
INSERT INTO `oa_seal_cate` VALUES (4, '其他', 1, 1639899158, 0);

-- ----------------------------
-- Table structure for oa_meeting_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_meeting_cate`;
CREATE TABLE `oa_meeting_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '会议室名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '会议室';

-- ----------------------------
-- Records of oa_meeting_cate
-- ----------------------------
INSERT INTO `oa_meeting_cate` VALUES (1, '会议室一', 1, 1639899124, 0);
INSERT INTO `oa_meeting_cate` VALUES (2, '会议室二', 1, 1639899140, 0);
INSERT INTO `oa_meeting_cate` VALUES (3, '会议室三', 1, 1639899148, 0);

-- ----------------------------
-- Table structure for oa_car_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_car_cate`;
CREATE TABLE `oa_car_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '车辆名称',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '车辆号码',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '用车类型';

-- ----------------------------
-- Records of oa_car_cate
-- ----------------------------
INSERT INTO `oa_car_cate` VALUES (1, '宝马X5', '粤A55555', 1, 1639900555, 0);
INSERT INTO `oa_car_cate` VALUES (2, '哈弗H6', '粤A66666', 1, 1639900666, 0);
INSERT INTO `oa_car_cate` VALUES (3, '奥迪Q8', '粤A88888', 1, 1639900888, 0);

-- ----------------------------
-- Table structure for oa_industry
-- ----------------------------
DROP TABLE IF EXISTS `oa_industry`;
CREATE TABLE `oa_industry`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '行业名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '行业';

-- ----------------------------
-- Records of oa_industry
-- ----------------------------
INSERT INTO `oa_industry` VALUES (1, '工业品企业', 1, 1637987189, 0);
INSERT INTO `oa_industry` VALUES (2, '互联网企业', 1, 1637987199, 0);
INSERT INTO `oa_industry` VALUES (3, '服务行业', 1, 1637987199, 0);
INSERT INTO `oa_industry` VALUES (4, '消费品企业', 1, 1637987199, 0);
INSERT INTO `oa_industry` VALUES (5, '原材料企业', 1, 1637987199, 0);
INSERT INTO `oa_industry` VALUES (6, '农业企业', 1, 1637987199, 0);
INSERT INTO `oa_industry` VALUES (7, '科技企业', 1, 1637987199, 0);
INSERT INTO `oa_industry` VALUES (8, '其他行业', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_services
-- ----------------------------
DROP TABLE IF EXISTS `oa_services`;
CREATE TABLE `oa_services`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '服务名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '企业服务';

-- ----------------------------
-- Records of oa_services
-- ----------------------------
INSERT INTO `oa_services` VALUES (1, '定制服务', 1, 1637987189, 0);
INSERT INTO `oa_services` VALUES (2, '开店咨询', 1, 1637987199, 0);
INSERT INTO `oa_services` VALUES (3, '推广运营', 1, 1637987199, 0);
INSERT INTO `oa_services` VALUES (4, '财税咨询', 1, 1637987199, 0);
INSERT INTO `oa_services` VALUES (5, '代理记账', 1, 1637987199, 0);
INSERT INTO `oa_services` VALUES (6, '开卡服务', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_expense
-- ----------------------------
DROP TABLE IF EXISTS `oa_expense`;
CREATE TABLE `oa_expense`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '报销编码',
  `income_month` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '入账月份',
  `expense_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '原始单据日期',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报销人',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报销部门ID',
  `ptid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定字段:关联项目ID',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `flow_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态 0待审核,1审核中,2审核通过,3审核不通过,4撤销审核,5已打款',
  `last_admin_id` varchar(200) NOT NULL DEFAULT '0' COMMENT '上一审批人',
  `pay_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款人ID',
  `pay_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打款时间',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '报销表';

-- ----------------------------
-- Table structure for oa_expense_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_expense_cate`;
CREATE TABLE `oa_expense_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '报销类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '报销类型';

-- ----------------------------
-- Records of oa_expense_cate
-- ----------------------------
INSERT INTO `oa_expense_cate` VALUES (1, '交通费', 1, 1637987189, 0);
INSERT INTO `oa_expense_cate` VALUES (2, '住宿费', 1, 1637987199, 0);
INSERT INTO `oa_expense_cate` VALUES (3, '餐补费', 1, 1638088518, 0);
INSERT INTO `oa_expense_cate` VALUES (4, '招待费', 1, 1637987199, 0);
INSERT INTO `oa_expense_cate` VALUES (5, '汽油费', 1, 1637987199, 0);
INSERT INTO `oa_expense_cate` VALUES (6, '其他费', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_expense_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_expense_interfix`;
CREATE TABLE `oa_expense_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `exid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报销ID',
  `amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '金额',
  `cate_id` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '报销类型ID',
  `remarks` mediumtext NULL COMMENT '备注',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登记人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '报销关联数据表';

-- ----------------------------
-- Table structure for oa_file_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_file_group`;
CREATE TABLE `oa_file_group`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分组名',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '文件分组表';

-- ----------------------------
-- Table structure for oa_file
-- ----------------------------
DROP TABLE IF EXISTS `oa_file`;
CREATE TABLE `oa_file`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(15) NOT NULL DEFAULT '' COMMENT '所属模块',
  `sha1` varchar(60) NOT NULL COMMENT 'sha1',
  `md5` varchar(60) NOT NULL COMMENT 'md5',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `filename` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `filepath` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径+文件名',
  `filesize` int(10) NOT NULL DEFAULT 0 COMMENT '文件大小',
  `fileext` varchar(10) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT '文件类型',
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文件分组ID',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传会员ID',
  `uploadip` varchar(15) NOT NULL DEFAULT '' COMMENT '上传IP',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未审核1已审核-1不通过',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `admin_id` int(11) NOT NULL COMMENT '审核者id',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `audit_time` int(11) NOT NULL DEFAULT 0 COMMENT '审核时间',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '来源模块功能',
  `use` varchar(255) NULL DEFAULT NULL COMMENT '用处',
  `download` int(11) NOT NULL DEFAULT 0 COMMENT '下载量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '文件表';

-- ----------------------------
-- Records of oa_file
-- ----------------------------
INSERT INTO `oa_file` VALUES (1, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', 'f95982689eb222b84e999122a50b3780.jpg.jpg', 'f95982689eb222b84e999122a50b3780.jpg', 'https://blog.gougucms.com/storage/202202/f95982689eb222b84e999122a50b3780.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);
INSERT INTO `oa_file` VALUES (2, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', 'e729477de18e3be7e7eb4ec7fe2f821e.jpg', 'e729477de18e3be7e7eb4ec7fe2f821e.jpg', 'https://blog.gougucms.com/storage/202202/e729477de18e3be7e7eb4ec7fe2f821e.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);
INSERT INTO `oa_file` VALUES (3, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', '1193f7a1585b9f6e8a97ae17718018b3.jpg', 'images/1193f7a1585b9f6e8a97ae17718018b3.jpg', 'https://blog.gougucms.com/storage/202204/1193f7a1585b9f6e8a97ae17718018b3.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);
INSERT INTO `oa_file` VALUES (4, 'admin', '5125347886f07f48f7003825660117039eb8784f', '563e5e8f48e607ed54461796b0cb4844', '0f22a5ba4797b2fa22049ea73e6f779c.jpg', 'images/0f22a5ba4797b2fa22049ea73e6f779c.jpg', 'https://blog.gougucms.com/storage/202202/0f22a5ba4797b2fa22049ea73e6f779c.jpg', 62609, 'jpg', 'image/jpeg', 0, 1, '127.0.0.1', 1, 1645057433, 1, 0, 1645057433, 'upload', 'thumb', 0);

-- ----------------------------
-- Table structure for oa_invoice
-- ----------------------------
DROP TABLE IF EXISTS `oa_invoice`;
CREATE TABLE `oa_invoice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '发票号码',
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID',
  `contract_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联合同协议ID',
  `project_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目ID',
  `cash_type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '付款方式：1现金 2转账 3微信支付 4支付宝 5信用卡 6支票 7其他',
  `is_cash` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否到账：0未到账 1部分到账 2全部到账',
  `amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '发票金额',
  `enter_amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '到账金额',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开发票部门',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票申请人',
  `check_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票审核人',
  `check_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间',
  `open_admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票开具人',
  `open_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票开具时间',
  `delivery` varchar(100) NOT NULL DEFAULT '' COMMENT '快递单号',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '抬头类型：1企业2个人',
  `invoice_subject` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联发票主体ID',
  `invoice_type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '发票类型：1增值税专用发票,2普通发票,3专用发票',
  `invoice_title` varchar(100) NOT NULL DEFAULT '' COMMENT '开票抬头',
  `invoice_phone` varchar(100) NOT NULL DEFAULT '' COMMENT '电话号码',
  `invoice_tax` varchar(100) NOT NULL DEFAULT '' COMMENT '纳税人识别号',
  `invoice_bank` varchar(100) NOT NULL DEFAULT '' COMMENT '开户银行',
  `invoice_account` varchar(100) NOT NULL DEFAULT '' COMMENT '银行账号',
  `invoice_banking` varchar(100) NOT NULL DEFAULT '' COMMENT '银行营业网点',
  `invoice_address` varchar(100) NOT NULL DEFAULT '' COMMENT '地址',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `flow_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3',
  `other_file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '其他附件ID，如:1,2,3',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态 0待审核,1审核中,2审核通过,3审核不通过,4已撤销,5已开具,10已作废',
  `last_admin_id` varchar(200) NOT NULL DEFAULT '0' COMMENT '上一审批人',
  `check_remark` mediumtext NULL COMMENT '撤销的理由',
  `remark` mediumtext NULL COMMENT '备注',
  `enter_time` int(11) NOT NULL DEFAULT 0 COMMENT '最新到账时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '发票表';

-- ----------------------------
-- Table structure for oa_invoice_income
-- ----------------------------
DROP TABLE IF EXISTS `oa_invoice_income`;
CREATE TABLE `oa_invoice_income`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `inid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票ID',
  `amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '到账金额',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '到账登记人',
  `enter_time` int(11) NOT NULL DEFAULT 0 COMMENT '到账时间',
  `remarks` mediumtext NULL COMMENT '备注',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1正常 6作废',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '发票到账记录表';

-- ----------------------------
-- Table structure for oa_invoice_subject
-- ----------------------------
DROP TABLE IF EXISTS `oa_invoice_subject`;
CREATE TABLE `oa_invoice_subject`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '主体名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '发票主体名称';

-- ----------------------------
-- Records of oa_invoice_subject
-- ----------------------------
INSERT INTO `oa_invoice_subject` VALUES (1, '勾股信息科技有限公司', 1, 1638006751, 0);

-- ----------------------------
-- Table structure for oa_keywords
-- ----------------------------
DROP TABLE IF EXISTS `oa_keywords`;
CREATE TABLE `oa_keywords`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字名称',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '关键字表';

-- ----------------------------
-- Records of oa_keywords
-- ----------------------------
INSERT INTO `oa_keywords` VALUES (1, '勾股OA', 1, 1, 1638006730, 0);
INSERT INTO `oa_keywords` VALUES (2, '勾股CMS', 1, 1, 1638006730, 0);
INSERT INTO `oa_keywords` VALUES (3, '勾股BLOG', 1, 1, 1638006730, 0);
INSERT INTO `oa_keywords` VALUES (4, '勾股DEV', 1, 1, 1638006730, 0);

-- ----------------------------
-- Table structure for oa_message
-- ----------------------------
DROP TABLE IF EXISTS `oa_message`;
CREATE TABLE `oa_message`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '消息主题',
  `template` tinyint(2) NOT NULL DEFAULT 0 COMMENT '消息模板，用于前端拼接消息0私人消息,1公告,2办公审批,3报销审批,4发票审批,5合同审批',
  `content` mediumtext NULL COMMENT '消息内容',
  `file_ids` mediumtext NULL COMMENT '消息附件',
  `from_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送人id',
  `to_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '阅览人类型：1 人员 2部门 3岗位 4全部',
  `type_user` mediumtext NULL COMMENT '人员ID或部门ID或角色ID，全员则为空',
  `send_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送日期',
  `read_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '阅读时间',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源发件id',
  `fid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '转发或回复消息关联id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1已删除消息 0垃圾消息 1正常消息',
  `is_draft` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否是草稿：1正常消息 2草稿消息',
  `delete_source` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '垃圾消息来源： 1已发消息 2草稿消息 3已收消息',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `module_name` varchar(30) NOT NULL COMMENT '模块',
  `controller_name` varchar(30) NOT NULL COMMENT '控制器',
  `action_name` varchar(30) NOT NULL COMMENT '方法',
  `action_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作模块数据的id（针对系统消息）',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '消息表';

-- ----------------------------
-- Table structure for oa_note_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_note_cate`;
CREATE TABLE `oa_note_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父类ID',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '公告分类';

-- ----------------------------
-- Records of oa_note_cate
-- ----------------------------
INSERT INTO `oa_note_cate` VALUES (1, 0, 1, '普通公告', 1637984265, 1637984299);
INSERT INTO `oa_note_cate` VALUES (2, 0, 2, '紧急公告', 1637984283, 1637984310);
INSERT INTO `oa_note_cate` VALUES (3, 0, 3, '防疫公告', 1637984283, 1637984310);

-- ----------------------------
-- Table structure for oa_note
-- ----------------------------
DROP TABLE IF EXISTS `oa_note`;
CREATE TABLE `oa_note`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联分类ID',
  `title` varchar(225) NULL DEFAULT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '公告内容',
  `src` varchar(100) NULL DEFAULT NULL COMMENT '关联链接',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '1可用-1禁用',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '相关附件',
  `role_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '查看权限，0所有人,1部门,2人员',
  `role_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '可查看部门',
  `role_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '可查看用户',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '展示开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '展示结束时间',
  `admin_id` int(11) NOT NULL DEFAULT 0 COMMENT '发布人id',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '公告';

-- ----------------------------
-- Records of oa_note
-- ----------------------------
INSERT INTO `oa_note` VALUES (1, 1, '欢迎使用勾股OA办公系统', '<p>欢迎使用勾股OA办公系统，勾股办公是一款简单实用的开源免费的企业办公系统。系统集成了系统设置、人事管理、行政管理、消息管理、日常办公、财务管理、客户管理、项目管理、合同管理、知识管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。</p>', 'https://oa.gougucms.com', 1, 2,'',1,'','', 1635696000, 1924876800,1, 1637984962, 1637984975);
INSERT INTO `oa_note` VALUES (2, 1, '勾股OA支持定制开发', '<p>欢迎使用勾股OA办公系统，勾股办公是一款简单实用的开源免费的企业办公系统。系统集成了系统设置、人事管理、行政管理、消息管理、日常办公、财务管理、客户管理、项目管理、合同管理、知识管理等基础模块。</p><p>勾股OA开源发布，同时我们也支持功能定制开发，价格优惠，定制开发系统功能更贴近自身需求，欢迎够沟通合作。</p><p>合作联系微信号“hdm588”，业务合作、功能定制加微信时请备注。</p>', 'https://oa.gougucms.com', 1, 2,'',1,'','', 1635696000, 1924876800,1, 1637984962, 1637984975);
INSERT INTO `oa_note` VALUES (3, 1, '勾股DEV——研发管理与团队协作的工具', '<p>勾股DEV是一款专为IT行业研发团队打造的智能化项目管理与团队协作的工具软件，可以在线管理团队的工作、项目和任务，覆盖从需求提出到研发完成上线整个过程的项目协作。</p><p>项目体验地址：https://www.gougucms.com/home/pages/detail/s/gougudev.html</p><p>项目开源地址：https://gitee.com/gouguopen/dev</p><p>勾股DEV开源发布，同时我们也支持功能定制开发，价格优惠，定制开发系统功能更贴近自身需求，欢迎够沟通合作。</p><p>合作联系微信号“hdm588”，业务合作、功能定制加微信时请备注。</p>', 'https://dev.gougucms.com', 1, 2,'',1,'','', 1635696000, 1924876800,1, 1637984962, 1637984975);

-- ----------------------------
-- Table structure for oa_position
-- ----------------------------
DROP TABLE IF EXISTS `oa_position`;
CREATE TABLE `oa_position`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '岗位名称',
  `work_price` int(10) NOT NULL DEFAULT 0 COMMENT '工时单价',
  `remark` varchar(1000) NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '岗位职称';

-- ----------------------------
-- Records of oa_position
-- ----------------------------
INSERT INTO `oa_position` VALUES (1, '超级岗位', 1000, '超级岗位，不能修改', 1, 0, 0);
INSERT INTO `oa_position` VALUES (2, '人事总监', 1000, '人事部的最大领导', 1, 0, 0);
INSERT INTO `oa_position` VALUES (3, '普通员工', 500, '普通员工', 1, 0, 0);

-- ----------------------------
-- Table structure for oa_position_group
-- ----------------------------
DROP TABLE IF EXISTS `oa_position_group`;
CREATE TABLE `oa_position_group`  (
  `pid` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '岗位id',
  `group_id` int(11) NULL DEFAULT NULL COMMENT '权限id',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  UNIQUE INDEX `pid_group_id`(`pid`, `group_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COMMENT = '权限分组和岗位的关联表';

-- ----------------------------
-- Records of oa_position_group
-- ----------------------------
INSERT INTO `oa_position_group` VALUES (1, 1, 1635755739, 0);
INSERT INTO `oa_position_group` VALUES (2, 2, 1638007427, 0);
INSERT INTO `oa_position_group` VALUES (3, 3, 1638007427, 0);

-- ----------------------------
-- Table structure for oa_plan
-- ----------------------------
DROP TABLE IF EXISTS `oa_plan`;
CREATE TABLE `oa_plan`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工作安排主题',
  `type` varchar(100) NOT NULL DEFAULT '' COMMENT '日程优先级',
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联工作内容类型ID',
  `cmid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联客户ID',
  `ptid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联项目ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联创建员工ID',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `remind_type` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提醒类型',
  `remind_time` int(11) NOT NULL DEFAULT 0 COMMENT '提醒时间',
  `remark` text NOT NULL COMMENT '描述',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '日程安排';

-- ----------------------------
-- Table structure for oa_schedule
-- ----------------------------
DROP TABLE IF EXISTS `oa_schedule`;
CREATE TABLE `oa_schedule`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工作记录主题',
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT '预设字段:关联工作内容类型ID',
  `cmid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联客户ID',
  `ptid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联项目ID',
  `tid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预设字段:关联任务ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联创建员工ID',
  `did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `labor_time` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '工时',
  `labor_type` int(1) NOT NULL DEFAULT 0 COMMENT '工作类型:1案头2外勤',
  `remark` text NOT NULL COMMENT '描述',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作记录';

-- ----------------------------
-- Table structure for oa_schedule_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_schedule_interfix`;
CREATE TABLE `oa_schedule_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '工作记录ID',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联创建员工ID',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作记录关联的附件表';


-- ----------------------------
-- Table structure for oa_approve
-- ----------------------------
DROP TABLE IF EXISTS `oa_approve`;
CREATE TABLE `oa_approve`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '审批类型',
  `flow_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批流程ID',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '内容',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号码',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `remark1` varchar(500) NOT NULL DEFAULT '' COMMENT '备注1',
  `detail_time` int(11) NOT NULL DEFAULT 0 COMMENT '时间日期',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `start_time_span` int(1) NOT NULL DEFAULT 0 COMMENT '开始时间时段:1上午,2下午',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `end_time_span` int(1) NOT NULL DEFAULT 0 COMMENT '结束时间时段:1上午,2下午',
  `duration` decimal(10, 1) NOT NULL DEFAULT 0.0 COMMENT '时长',
  `admin_id` int(10) NOT NULL COMMENT '创建人ID',
  `department_id` int(10) NOT NULL COMMENT '创建人部门ID',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `flow_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ID，如:1,2,3',
  `check_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态 0待审核,1审核中,2审核通过,3审核不通过,4撤销审核',
  `last_admin_id` varchar(200) NOT NULL DEFAULT '0' COMMENT '上一审批人',
  `detail_type` int(11) NOT NULL DEFAULT 0 COMMENT '假期类型:1事假,2年假,3调休假,4病假,5婚假,6丧假,7产假,8陪产假,9其他',
  `other_type` int(11) NOT NULL DEFAULT 0 COMMENT '其他类型:1公告类,2规则制度类,3合同类,4资质更新类,5员工证明,6其他',
  `department_type` int(11) NOT NULL DEFAULT 0 COMMENT '部门类型',
  `position_type` int(11) NOT NULL DEFAULT 0 COMMENT '职位类型',
  `bank` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡账号',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `num` bigint(12) NOT NULL DEFAULT 0 COMMENT '数量',
  `num1` bigint(12) NOT NULL DEFAULT 0 COMMENT '数量1',
  `amount` decimal(18, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '日常审批表';

-- ----------------------------
-- Table structure for oa_flow_step
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_step`;
CREATE TABLE `oa_flow_step`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL COMMENT '审批内容ID',
  `flow_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0自由指定,1当前部门负责人，2上一级部门负责人，3指定用户（任意一人），4指定用户（多人会签）',
  `flow_name` varchar(255) NOT NULL DEFAULT '' COMMENT '流程名称',
  `flow_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '审批人ID (使用逗号隔开) 1,2,3',
  `sort` tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序ID',
  `type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '审批类型:1日常审批,2报销审批,3发票审批,4合同审批',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批步骤表';

-- ----------------------------
-- Table structure for oa_flow_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_flow_record`;
CREATE TABLE `oa_flow_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批内容ID',
  `step_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批步骤ID',
  `check_user_id` int(11) NOT NULL DEFAULT 0 COMMENT '审批人ID',
  `check_time` int(11) NOT NULL COMMENT '审批时间',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0发起审批1审核通过2审核拒绝3撤销',
  `type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '审批类型:1日常审批,2报销审批,3发票审批,4合同审批',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '审核意见',
  `is_invalid` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审批失效（1标记为无效）',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '审批记录表';

-- ----------------------------
-- Table structure for oa_work_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_work_cate`;
CREATE TABLE `oa_work_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '工作类型名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作类型';

-- ----------------------------
-- Records of oa_work_cate
-- ----------------------------
INSERT INTO `oa_work_cate` VALUES (1, '其他', 1, 1637987189, 0);
INSERT INTO `oa_work_cate` VALUES (2, '方案策划', 1, 1637987199, 0);
INSERT INTO `oa_work_cate` VALUES (3, '撰写文档', 1, 1637987199, 0);
INSERT INTO `oa_work_cate` VALUES (4, '需求调研', 1, 1637987199, 0);
INSERT INTO `oa_work_cate` VALUES (5, '需求沟通', 1, 1637987199, 0);
INSERT INTO `oa_work_cate` VALUES (6, '参加会议', 1, 1637987199, 0);
INSERT INTO `oa_work_cate` VALUES (7, '拜访客户', 1, 1637987199, 0);
INSERT INTO `oa_work_cate` VALUES (8, '接待客户', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_work
-- ----------------------------
DROP TABLE IF EXISTS `oa_work`;
CREATE TABLE `oa_work`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '类型：1 日报 2周报 3月报',
  `type_user` mediumtext NULL COMMENT '接受人员ID',
  `works` mediumtext NULL COMMENT '汇报工作内容',
  `plans` mediumtext NULL COMMENT '计划工作内容',
  `remark` mediumtext NULL COMMENT '其他事项',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人id',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作表';

-- ----------------------------
-- Table structure for oa_work_file_interfix
-- ----------------------------
DROP TABLE IF EXISTS `oa_work_file_interfix`;
CREATE TABLE `oa_work_file_interfix`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wid` int(11) UNSIGNED NOT NULL COMMENT '汇报工作id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作关联的附件表';

-- ----------------------------
-- Table structure for oa_work_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_work_record`;
CREATE TABLE `oa_work_record`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wid` int(11) UNSIGNED NOT NULL COMMENT '汇报工作id',
  `from_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送人id',
  `to_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接收人id',
  `send_time` int(11) NOT NULL DEFAULT 0 COMMENT '发送日期',
  `read_time` int(11) NOT NULL DEFAULT 0 COMMENT '阅读时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '汇报工作发送记录表';

-- ----------------------------
-- Table structure for oa_work_comment
-- ----------------------------
DROP TABLE IF EXISTS `oa_work_comment`;
CREATE TABLE `oa_work_comment`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `work_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '工作汇报id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `content` mediumtext NULL COMMENT '点评内容',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '工作汇报点评表';

-- ----------------------------
-- Table structure for oa_customer_grade
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer_grade`;
CREATE TABLE `oa_customer_grade`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '客户等级名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户等级';

-- ----------------------------
-- Records of oa_customer_grade
-- ----------------------------
INSERT INTO `oa_customer_grade` VALUES (1, '普通客户', 1, 1637987189, 0);
INSERT INTO `oa_customer_grade` VALUES (2, 'VIP客户', 1, 1637987199, 0);
INSERT INTO `oa_customer_grade` VALUES (3, '白银客户', 1, 1637987199, 0);
INSERT INTO `oa_customer_grade` VALUES (4, '黄金客户', 1, 1637987199, 0);
INSERT INTO `oa_customer_grade` VALUES (5, '钻石客户', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_customer_source
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer_source`;
CREATE TABLE `oa_customer_source`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '客户渠道名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户来源';

-- ----------------------------
-- Records of oa_customer_source
-- ----------------------------
INSERT INTO `oa_customer_source` VALUES (1, '独立开发', 1, 1637987189, 0);
INSERT INTO `oa_customer_source` VALUES (2, '微信公众号', 1, 1637987199, 0);
INSERT INTO `oa_customer_source` VALUES (3, '今日头条', 1, 1637987199, 0);
INSERT INTO `oa_customer_source` VALUES (4, '百度搜索', 1, 1637987199, 0);
INSERT INTO `oa_customer_source` VALUES (5, '销售活动', 1, 1637987199, 0);
INSERT INTO `oa_customer_source` VALUES (6, '电话来访', 1, 1637987199, 0);
INSERT INTO `oa_customer_source` VALUES (7, '客户介绍', 1, 1637987199, 0);
INSERT INTO `oa_customer_source` VALUES (8, '其他来源', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_customer
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer`;
CREATE TABLE `oa_customer`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '客户名称',
  `source_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户来源id',
  `grade_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户等级id',
  `industry_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属行业id',
  `services_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户意向id',
  `provinceid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '省份id',
  `cityid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '城市id',
  `distid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区县id',
  `townid` bigint(20) NOT NULL DEFAULT 0 COMMENT '城镇id',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '客户联系地址',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '客户状态：0未设置,1新进客户,2跟进客户,3正式客户,4流失客户',
  `intent_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '意向状态：0未设置,1意向不明,2意向模糊,3意向一般,4意向强烈',
  `contact_first` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '第一联系人id',
  `admin_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '录入人',
  `belong_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属人',
  `belong_did` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属部门',
  `belong_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '获取时间',
  `distribute_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分配时间',
  `share_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '共享人员，如:1,2,3',
  `content` mediumtext NULL COMMENT '客户描述',
  `market` mediumtext NULL COMMENT '主要经营业务',
  `remark` mediumtext NULL COMMENT '备注信息',
  `bank` varchar(60) NOT NULL DEFAULT '' COMMENT '开户银行',
  `bank_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '银行帐号',
  `tax_num` varchar(100) NOT NULL DEFAULT '' COMMENT '纳税人识别号',
  `cperson_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '开票电话',
  `cperson_address` varchar(200) NOT NULL DEFAULT '' COMMENT '开票地址',
  `discard_time` int(11) NOT NULL DEFAULT 0 COMMENT '废弃时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '客户表';

-- ----------------------------
-- Table structure for oa_customer_trace
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer_trace`;
CREATE TABLE `oa_customer_trace`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户ID',
  `contact_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '联系人id',
  `chance_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销售机会id',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '跟进方式:0其他,1电话,2微信,3QQ,4上门',
  `stage` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前阶段:0未设置,1立项评估,2初期沟通,3需求分析,4方案制定,5商务谈判,6合同签订,7失单',
  `content` mediumtext NULL COMMENT '跟进内容',
  `follow_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '跟进时间',
  `next_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '下次跟进时间',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户跟进记录表';

-- ----------------------------
-- Table structure for oa_customer_contact
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer_contact`;
CREATE TABLE `oa_customer_contact`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户ID',
  `is_default` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是第一联系人',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户性别:0未知,1男,2女',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `qq` varchar(20) NOT NULL DEFAULT '' COMMENT 'QQ号',
  `wechat` varchar(100) NOT NULL DEFAULT '' COMMENT '微信号',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮件地址',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '称谓',
  `department` varchar(50) NOT NULL DEFAULT '' COMMENT '部门',
  `position` varchar(50) NOT NULL DEFAULT '' COMMENT '职务',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户联系人表';

-- ----------------------------
-- Table structure for oa_customer_chance
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer_chance`;
CREATE TABLE `oa_customer_chance`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '销售机会主题',
  `cid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户ID',
  `contact_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '联系人id',
  `services_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '需求服务id',
  `stage` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前阶段:0未设置,1立项评估,2初期沟通,3需求分析,4方案制定,5商务谈判,6合同签订,7失单',
  `content` mediumtext NULL COMMENT '需求描述',
  `discovery_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发现时间',
  `expected_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预计签单时间',
  `expected_amount` decimal(15, 2) NULL DEFAULT 0.00 COMMENT '预计签单金额',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `belong_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属人',
  `assist_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '协助人员，如:1,2,3',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户销售机会表';

-- ----------------------------
-- Table structure for oa_customer_file
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer_file`;
CREATE TABLE `oa_customer_file`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) UNSIGNED NOT NULL COMMENT '关联客户id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户附件关联表';

-- ----------------------------
-- Table structure for oa_customer_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_customer_log`;
CREATE TABLE `oa_customer_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作类型:0客户,1跟进记录,2客户联系人,3销售机会',
  `action` varchar(100) NOT NULL DEFAULT 'edit' COMMENT '动作:add,edit,del,check,upload',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段',
  `customer_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户id',
  `trace_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '跟进记录id',
  `contact_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '客户联系人id',
  `chance_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销售机会id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人',
  `old_content` mediumtext NULL COMMENT '修改前的内容',
  `new_content` mediumtext NULL COMMENT '修改后的内容',
  `remark` mediumtext NULL COMMENT '补充备注',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户操作记录表';

-- ----------------------------
-- Table structure for oa_contract_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_contract_cate`;
CREATE TABLE `oa_contract_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '合同类别名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '合同类别';

-- ----------------------------
-- Records of oa_contract_cate
-- ----------------------------
INSERT INTO `oa_contract_cate` VALUES (1, '销售合同', 1, 1637987189, 0);
INSERT INTO `oa_contract_cate` VALUES (2, '采购合同', 1, 1637987199, 0);
INSERT INTO `oa_contract_cate` VALUES (3, '租赁合同', 1, 1637987199, 0);
INSERT INTO `oa_contract_cate` VALUES (4, '委托协议', 1, 1637987199, 0);
INSERT INTO `oa_contract_cate` VALUES (5, '代理协议', 1, 1637987199, 0);
INSERT INTO `oa_contract_cate` VALUES (6, '其他合同', 1, 1637987199, 0);

-- ----------------------------
-- Table structure for oa_contract
-- ----------------------------
DROP TABLE IF EXISTS `oa_contract`;
CREATE TABLE `oa_contract`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父协议id',
  `code` varchar(255) NOT NULL DEFAULT '' COMMENT '合同编号',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '合同名称',
  `cate_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '合同性质：0未设置,1普通合同、2框架合同、3补充协议、4其他合同',
  `subject_id` varchar(255) NOT NULL DEFAULT '' COMMENT '签约主体',
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID,预设数据',
  `customer` varchar(255) NOT NULL DEFAULT '' COMMENT '客户名称',
  `customer_name` varchar(255) NOT NULL DEFAULT '' COMMENT '客户代表',
  `customer_mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '客户电话',
  `customer_address` varchar(255) NOT NULL DEFAULT '' COMMENT '客户地址',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同结束时间',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `prepared_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同制定人',
  `sign_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同签订人',
  `keeper_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同保管人', 
  `share_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '共享人员，如:1,2,3',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '相关附件，如:1,2,3',
  `sign_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同签订时间',
  `sign_did` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '合同签订部门',
  `cost` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '合同金额',
  `is_tax` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否含税：0未含税,1含税',
  `tax` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT '税点',
  `check_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '合同状态：0待审核,1审核中,2审核通过,3审核不通过,4撤销审核,5已中止,6已作废',
  `check_step_sort` int(11) NOT NULL DEFAULT 0 COMMENT '当前审批步骤',
  `check_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '当前审批人ID，如:1,2,3',
  `flow_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '历史审批人ID，如:1,2,3',
  `last_admin_id` varchar(200) NOT NULL DEFAULT '0' COMMENT '上一审批人', 
  `copy_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '抄送人ID，如:1,2,3',
  `check_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核人',
  `check_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间',
  `check_remark` mediumtext NULL COMMENT '审核备注信息',
  `stop_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '中止人',
  `stop_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '中止时间',
  `stop_remark` mediumtext NULL COMMENT '中止备注信息',
  `void_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '作废人',
  `void_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '作废时间',
  `void_remark` mediumtext NULL COMMENT '作废备注信息',
  `archive_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '归档状态：0未归档,1已归档',
  `archive_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '归档人',
  `archive_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '归档时间',
  `remark` mediumtext NULL COMMENT '备注信息',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '合同表';

-- ----------------------------
-- Table structure for oa_contract_file
-- ----------------------------
DROP TABLE IF EXISTS `oa_contract_file`;
CREATE TABLE `oa_contract_file`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) UNSIGNED NOT NULL COMMENT '关联合同id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '合同附件关联表';

-- ----------------------------
-- Table structure for oa_contract_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_contract_log`;
CREATE TABLE `oa_contract_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `action` varchar(100) NOT NULL DEFAULT 'edit' COMMENT '动作:add,edit,del,check,upload',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段',
  `contract_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联合同id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人',
  `old_content` mediumtext NULL COMMENT '修改前的内容',
  `new_content` mediumtext NULL COMMENT '修改后的内容',
  `remark` mediumtext NULL COMMENT '补充备注',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '合同操作记录表';

-- ----------------------------
-- Table structure for oa_step
-- ----------------------------
DROP TABLE IF EXISTS `oa_step`;
CREATE TABLE `oa_step`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL COMMENT '关联ID',
  `flow_name` varchar(255) NOT NULL DEFAULT '' COMMENT '阶段名称',
  `flow_uid` int(11) NOT NULL DEFAULT 0 COMMENT '阶段负责人ID',
  `flow_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '阶段成员ID (使用逗号隔开) 1,2,3',
  `sort` tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序ID',
  `type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '阶段类型:1合同,2项目',
  `start_time` int(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '阶段步骤表';

-- ----------------------------
-- Table structure for oa_step_record
-- ----------------------------
DROP TABLE IF EXISTS `oa_step_record`;
CREATE TABLE `oa_step_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联ID',
  `step_id` int(11) NOT NULL DEFAULT 0 COMMENT '阶段步骤ID',
  `check_uid` int(11) NOT NULL DEFAULT 0 COMMENT '审批人ID',
  `check_time` int(11) NOT NULL COMMENT '审批时间',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1审核通过2审核拒绝3撤销',
  `type` tinyint(2) NOT NULL DEFAULT 1 COMMENT '阶段类型:1合同,2项目',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '审核意见',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '阶段步骤记录表';

-- ----------------------------
-- Table structure for oa_project
-- ----------------------------
DROP TABLE IF EXISTS `oa_project`;
CREATE TABLE `oa_project`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '项目名称',
  `customer_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联客户ID,预设数据',
  `contract_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预定字段:关联合同协议ID',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `director_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目负责人',
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目开始时间',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目结束时间',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：状态：0未设置,1未开始,2进行中,3已完成,4已关闭',
  `step_sort` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前审核步骤',
  `content` mediumtext NULL COMMENT '项目描述',
  `md_content` mediumtext NULL COMMENT 'markdown项目描述',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '项目表';

-- ----------------------------
-- Table structure for oa_project_user
-- ----------------------------
DROP TABLE IF EXISTS `oa_project_user`;
CREATE TABLE `oa_project_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目成员id',
  `project_id` int(11) UNSIGNED NOT NULL COMMENT '关联项目id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '移除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '项目成员表';


-- ----------------------------
-- Table structure for oa_project_task
-- ----------------------------
DROP TABLE IF EXISTS `oa_project_task`;
CREATE TABLE `oa_project_task`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '主题',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父任务id',
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `plan_hours` decimal(10, 1) NOT NULL DEFAULT 0.00 COMMENT '预估工时',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预计结束时间',
  `over_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际结束时间',
  `director_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '指派给(负责人)',
  `assist_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '协助人员，如:1,2,3',
  `cate` tinyint(1) NOT NULL DEFAULT 1 COMMENT '所属工作类型',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务类型(预留字段)',
  `priority` tinyint(1) NOT NULL DEFAULT 1 COMMENT '优先级:1低,2中,3高,4紧急',
  `before_task` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '前置任务id',
  `flow_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '流转状态：1待办的,2进行中,3已完成,4已拒绝,5已关闭',
  `done_ratio` int(2) NOT NULL DEFAULT 0 COMMENT '完成进度：0,20,40,50,60,80,100',
  `content` mediumtext NULL COMMENT '任务描述',
  `md_content` mediumtext NULL COMMENT 'markdown任务描述',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '项目任务表';

-- ----------------------------
-- Table structure for oa_project_document
-- ----------------------------
DROP TABLE IF EXISTS `oa_project_document`;
CREATE TABLE `oa_project_document`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '附件ids',
  `content` mediumtext NULL COMMENT '文档内容',
  `md_content` mediumtext NULL COMMENT 'markdown文档内容',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '项目文档表';

-- ----------------------------
-- Table structure for oa_project_link
-- ----------------------------
DROP TABLE IF EXISTS `oa_project_link`;
CREATE TABLE `oa_project_link`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT '模块',
  `topic_id` int(11) UNSIGNED NOT NULL COMMENT '关联主题id',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '链接关联表';

-- ----------------------------
-- Table structure for oa_project_file
-- ----------------------------
DROP TABLE IF EXISTS `oa_project_file`;
CREATE TABLE `oa_project_file`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT '模块',
  `topic_id` int(11) UNSIGNED NOT NULL COMMENT '关联主题id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '相关联附件id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '项目任务附件关联表';

-- ----------------------------
-- Table structure for oa_project_comment
-- ----------------------------
DROP TABLE IF EXISTS `oa_project_comment`;
CREATE TABLE `oa_project_comment`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT '模块',
  `topic_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联主题id',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复内容id',
  `padmin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复内容用户id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `content` mediumtext NULL COMMENT '评论内容',
  `md_content` mediumtext NULL COMMENT 'markdown评论内容',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 CHARACTER SET = utf8mb4 COMMENT = '项目任务评论表';

-- ----------------------------
-- Table structure for oa_project_log
-- ----------------------------
DROP TABLE IF EXISTS `oa_project_log`;
CREATE TABLE `oa_project_log`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '' COMMENT '模块:project,task,document',
  `action` varchar(100) NOT NULL DEFAULT 'edit' COMMENT '动作:add,edit,del,upload',
  `field` varchar(100) NOT NULL DEFAULT '' COMMENT '字段',
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目id',
  `task_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联任务id',
  `document_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联文档id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人',
  `old_content` mediumtext NULL COMMENT '修改前的内容',
  `new_content` mediumtext NULL COMMENT '修改后的内容',
  `remark` mediumtext NULL COMMENT '补充备注',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '项目任务操作记录表';

-- ----------------------------
-- Table structure for oa_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `oa_article_cate`;
CREATE TABLE `oa_article_cate`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT 0 COMMENT '父类ID',
  `sort` int(5) NOT NULL DEFAULT 0 COMMENT '排序',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类标题',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '描述',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识文章分类表';

-- ----------------------------
-- Records of oa_article_cate
-- ----------------------------
INSERT INTO `oa_article_cate` VALUES (1, 0, 0, '办公技巧', '', 1637984651, 0);
INSERT INTO `oa_article_cate` VALUES (2, 0, 0, '行业技能', '', 1637984739, 0);

-- ----------------------------
-- Table structure for oa_article
-- ----------------------------
DROP TABLE IF EXISTS `oa_article`;
CREATE TABLE `oa_article`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '知识文章标题',
  `cate_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联分类id',
  `keywords` varchar(255) NULL DEFAULT '' COMMENT '关键字',
  `desc` varchar(1000) NULL DEFAULT '' COMMENT '摘要',
  `thumb` int(11) NOT NULL DEFAULT 0 COMMENT '缩略图id',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '作者',
  `did` int(11) NOT NULL DEFAULT 0 COMMENT '部门',
  `origin_url` varchar(255) NOT NULL DEFAULT '' COMMENT '来源地址',
  `file_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '相关附件',
  `is_share` tinyint(1) NOT NULL DEFAULT 1 COMMENT '分享，0私有,1所有人,2部门,3人员',
  `share_dids` varchar(500) NOT NULL DEFAULT '' COMMENT '分享部门',
  `share_uids` varchar(500) NOT NULL DEFAULT '' COMMENT '分享用户',
  `content` text NOT NULL COMMENT '文章内容',
  `read` int(11) NOT NULL DEFAULT 0 COMMENT '阅读量',
  `type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '属性：1精华 2热门 3推荐',
  `status` int(1) NOT NULL DEFAULT 1 COMMENT '状态:1正常-1下架',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `delete_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识文章表';


-- ----------------------------
-- Records of oa_article
-- ----------------------------
INSERT INTO `oa_article` VALUES (1, '勾股OA——简单实用的开源免费的企业办公系统框架', 2, '', '勾股办公是一款简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功...', 0, 1, 1, '','',1,'','', '<p>勾股办公是一款简单实用的开源免费的企业办公系统框架。系统集成了系统设置、人事管理模块、消息管理模块、日常办公、财务管理等基础模块。系统简约，易于功能扩展，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本，通过二次开发之后可以用来做CRM，ERP，业务管理等系统。</p><p>项目体验地址：https://www.gougucms.com/home/pages/detail/s/gouguoa.html</p><p>项目开源地址：https://gitee.com/gouguopen/office</p>', 1, 2, 1, 1, 1637985280, 1650817107, 0);
INSERT INTO `oa_article` VALUES (2, '勾股Admin——优秀的前端Web UI解决方案', 2, '', '勾股Admin是一款开基于Layui的最新版扩展的Web UI解决方案。封装了Layui的自身调用方法和一些常用的工具函数，整合部分第三方开源的组件。', 0, 1, 1, '','',1,'','', '<p>勾股Admin是一款开基于Layui的最新版扩展的Web UI解决方案。封装了Layui的自身调用方法和一些常用的工具函数，整合部分第三方开源的组件。更多是为服务端程序员量身定做，为使用者提供相对完善的前端UI开发方案，相信她是一个很好的前端轮子。</p>
<p>项目体验地址：http://admin.gougucms.com</p><p>项目开源地址：https://gitee.com/gouguopen/guoguadmin</p>', 0, 0, 1, 1, 1650817189, 0, 0);
INSERT INTO `oa_article` VALUES (3, '勾股CMS——轻量级、高性能极速后台开发框架', 2, '', '勾股CMS是一套轻量级、高性能极速后台开发框架。通用型的后台权限管理框架，极低门槛、操作简单、开箱即用。系统易于功能扩展，代码维护，方便二次开发，让...', 0, 1, 1, '', '',1,'','','<p>勾股CMS是一套轻量级、高性能极速后台开发框架。通用型的后台权限管理框架，极低门槛、操作简单、开箱即用。系统易于功能扩展，代码维护，方便二次开发，让开发者更专注于业务深度需求的开发，帮助开发者简单高效降低二次开发成本。</p><p>项目体验地址：http://www.gougucms.com</p><p>项目开源地址：https://gitee.com/gouguopen/gougucms</p>', 0, 0, 1, 1, 1650817085, 0, 0);
INSERT INTO `oa_article` VALUES (4, '勾股BLOG——简约，易用开源的个人博客系统', 2, '', '勾股BLOG是一款实用的开源免费的个人博客系统。集成了系统管理、基础数据、博客文章、博客动态、语雀知识库、用户管理、访问统计等功能。具有简约，易用，内存占用低等特点，可以用来做个人博客，工作室官网，自...', 0, 1, 1, '', '',1,'','','<p>勾股BLOG是一款实用的开源免费的个人博客系统。集成了系统管理、基础数据、博客文章、博客动态、语雀知识库、用户管理、访问统计等功能。具有简约，易用，内存占用低等特点，可以用来做个人博客，工作室官网，自媒体官网等网站，二次开发之后也可以作为资讯、展品展示等网站。</p><p>项目体验地址：http://blog.gougucms.com</p><p>项目开源地址：https://gitee.com/gouguopen/blog</p>', 0, 0, 1, 1, 1650817152, 0, 0);
INSERT INTO `oa_article` VALUES (5, '勾股DEV——研发管理与团队协作的工具', 2, '', '勾股DEV是一款专为IT行业研发团队打造的智能化项目管理与团队协作的工具，可以在线管理团队的工作、项目和任务，覆盖从需求提出到研发完成上线整个过程的项目协作。', 0, 1, 1, '', '',1,'','','<p>勾股DEV是一款专为IT行业研发团队打造的智能化项目管理与团队协作的工具软件，可以在线管理团队的工作、项目和任务，覆盖从需求提出到研发完成上线整个过程的项目协作。</p><p>项目体验地址：https://www.gougucms.com/home/pages/detail/s/gougudev.html</p><p>项目开源地址：https://gitee.com/gouguopen/dev</p>', 0, 0, 1, 1, 1650817189, 0, 0);

-- ----------------------------
-- Table structure for oa_article_keywords
-- ----------------------------
DROP TABLE IF EXISTS `oa_article_keywords`;
CREATE TABLE `oa_article_keywords`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '知识文章ID',
  `keywords_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联关键字id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：-1删除 0禁用 1启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `aid`(`aid`) USING BTREE,
  INDEX `inid`(`keywords_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '知识文章关联表';

-- ----------------------------
-- Records of oa_article_keywords
-- ----------------------------
INSERT INTO `oa_article_keywords` VALUES (1, 1, 1, 1, 1638093082);
INSERT INTO `oa_article_keywords` VALUES (2, 2, 2, 1, 1638093082);
INSERT INTO `oa_article_keywords` VALUES (3, 3, 3, 3, 1638093082);
INSERT INTO `oa_article_keywords` VALUES (4, 4, 4, 4, 1638093082);

-- ----------------------------
-- Table structure for oa_article_comment
-- ----------------------------
DROP TABLE IF EXISTS `oa_article_comment`;
CREATE TABLE `oa_article_comment`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联知识文章id',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复内容id',
  `padmin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回复内容用户id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `content` mediumtext NULL COMMENT '评论内容',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 COMMENT = '知识评论表' ROW_FORMAT = Compact;
