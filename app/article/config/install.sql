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
  `content` text NULL COMMENT '评论内容',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1000 COMMENT = '知识评论表' ROW_FORMAT = Compact;