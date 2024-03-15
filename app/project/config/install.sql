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
  `content` text NULL COMMENT '项目描述',
  `md_content` text NULL COMMENT 'markdown项目描述',
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
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联项目id',
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人',
  `plan_hours` decimal(10, 1) NOT NULL DEFAULT 0.00 COMMENT '预估工时',
  `end_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预计结束时间',
  `over_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际结束时间',
  `director_uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '指派给(负责人)',
  `assist_admin_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '协助人员，如:1,2,3',
  `cate` tinyint(1) NOT NULL DEFAULT 1 COMMENT '所属工作类型',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '任务类型:1需求,2设计,3研发,4缺陷',
  `priority` tinyint(1) NOT NULL DEFAULT 1 COMMENT '优先级:1低,2中,3高,4紧急',
  `flow_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '流转状态：1待办的,2进行中,3已完成,4已拒绝,5已关闭',
  `done_ratio` int(2) NOT NULL DEFAULT 0 COMMENT '完成进度：0,20,40,50,60,80,100',
  `content` text NULL COMMENT '任务描述',
  `md_content` text NULL COMMENT 'markdown任务描述',
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
  `content` text NULL COMMENT '文档内容',
  `md_content` text NULL COMMENT 'markdown文档内容',
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
  `content` text NULL COMMENT '评论内容',
  `md_content` text NULL COMMENT 'markdown评论内容',
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
  `old_content` text NULL COMMENT '修改前的内容',
  `new_content` text NULL COMMENT '修改后的内容',
  `remark` text NULL COMMENT '补充备注',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COMMENT = '项目任务操作记录表';


INSERT INTO `oa_data_auth` VALUES ((SELECT MAX(id) +1  FROM `oa_data_auth` a), '项目管理员','project_admin','拥有该权限的员工可以查看所有项目。', 'project', '',0,0,0,'立项阶段|实施阶段|验收阶段|交付阶段','','',1656143065, 0);