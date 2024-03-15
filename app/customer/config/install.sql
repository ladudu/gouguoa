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
  `content` text NULL COMMENT '客户描述',
  `market` text NULL COMMENT '主要经营业务',
  `remark` text NULL COMMENT '备注信息',
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
  `content` text NULL COMMENT '跟进内容',
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
  `content` text NULL COMMENT '需求描述',
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
  `old_content` text NULL COMMENT '修改前的内容',
  `new_content` text NULL COMMENT '修改后的内容',
  `remark` text NULL COMMENT '补充备注',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '客户操作记录表';

INSERT INTO `oa_data_auth` VALUES ((SELECT MAX(id) +1  FROM `oa_data_auth` a), '客户管理员','customer_admin','拥有该权限的员工可以查看、转移所有客户。', 'customer', '',10,0,0,'','','',1656143065, 0);