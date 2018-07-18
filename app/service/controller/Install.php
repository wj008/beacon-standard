<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/4/28
 * Time: 6:06
 */

namespace app\service\controller;


use app\service\form\InstallForm;
use beacon\Controller;
use beacon\Mysql;
use beacon\Utils;

class Install extends Controller
{
    public function initialize()
    {
        parent::initialize();
        $file = Utils::path(ROOT_DIR, 'config/db.config.php');
        if (file_exists($file)) {
            exit;
        }
    }

    private function chkdir($dir, &$info = array())
    {
        $dir = Utils::path(ROOT_DIR, $dir);
        $rt = $this->checkIsWritable($dir);
        if ($rt) {
            $info[] = "<span class=yes>YES</span>--- {$dir}目录存在可写。";
        } else {
            $info[] = "<span class=no>NO</span>--- {$dir}目录权限不足，请修改目录读写权限。";
        }
        return $rt;
    }

    private function chkfile($file, &$info = array())
    {
        $file = Utils::path(ROOT_DIR, $file);
        if (file_exists($file)) {
            if (is_writable($file)) {
                $info[] = "<span class=yes>YES</span>--- {$file}文件存在可写。";
                return true;
            } else {
                $info[] = "<span class=no>NO</span>--- {$file}文件权限不足，请修改文件读写权限。";
                return false;
            }
        }
        return true;
    }

    private function checkIsWritable($dirpath)
    {
        $dir_path = Utils::path($dirpath);
        Utils::makeDir($dir_path);
        if (!is_dir($dir_path)) {
            return false;
        } else {
            $file_hd = @fopen($dir_path . 'beacon__test.txt', 'w');
            if (!$file_hd) {
                return false;
            } else {
                @fclose($file_hd);
                @unlink($dir_path . 'beacon__test.txt');
            }
        }
        return true;
    }

    public function indexAction()
    {
        $this->display('Install.tpl');
    }

    public function checkAction()
    {
        $info = [];
        $ok = true;
        //判断配置文件夹 是否存在可写
        $ok = $this->chkdir('/config', $info) && $ok;
        $ok = $this->chkdir('/app', $info) && $ok;
        $ok = $this->chkdir('/runtime', $info) && $ok;
        $ok = $this->chkfile('/config/db.config.php', $info) && $ok;
        $html = join('<br>', $info);
        $this->assign('info', $html);
        $this->assign('ok', $ok);
        $this->display('InstallCheck.tpl');
    }

    public function databaseAction()
    {
        $form = new InstallForm('add');
        if ($this->isGet()) {
            $this->assign('form', $form);
            $this->display('InstallDatabase.tpl');
            return;
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            try {
                if ($vals['is_create']) {
                    $db = new Mysql($vals['db_host'], $vals['db_port'], '', $vals['db_user'], $vals['db_pwd'], $vals['db_prefix']);
                    $db->exec('CREATE DATABASE if not exists `' . $vals['db_name'] . '` DEFAULT CHARSET utf8 COLLATE utf8_general_ci');
                    $db = new Mysql($vals['db_host'], $vals['db_port'], $vals['db_name'], $vals['db_user'], $vals['db_pwd'], $vals['db_prefix']);
                } else {
                    $db = new Mysql($vals['db_host'], $vals['db_port'], $vals['db_name'], $vals['db_user'], $vals['db_pwd'], $vals['db_prefix']);
                }
                //导入系统数据
                $db->exec('CREATE TABLE `@pf_manage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT \'用户名\',
  `pwd` varchar(255) DEFAULT NULL COMMENT \'用户密码\',
  `realname` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT \'0\' COMMENT \'管理员类型\',
  `errtice` int(11) DEFAULT \'0\' COMMENT \'错误次数\',
  `errtime` date DEFAULT NULL COMMENT \'错误时间\',
  `thistime` datetime DEFAULT NULL COMMENT \'本次登录时间\',
  `lasttime` datetime DEFAULT NULL COMMENT \'最后登录时间\',
  `thisip` varchar(255) DEFAULT NULL COMMENT \'本次登录IP\',
  `lastip` varchar(255) DEFAULT NULL COMMENT \'最后一次登录IP\',
  `islock` int(1) DEFAULT \'0\' COMMENT \'是否锁定账号\',
  `email` varchar(255) DEFAULT NULL COMMENT \'管理员邮箱\',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
CREATE TABLE `@pf_sysmenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT \'菜单标题\',
  `allow` int(1) DEFAULT \'0\' COMMENT \'是否启用\',
  `pid` varchar(255) DEFAULT NULL COMMENT \'所属上级菜单\',
  `show` int(1) DEFAULT \'0\' COMMENT \'是否展开\',
  `url` varchar(255) DEFAULT NULL COMMENT \'栏目路径\',
  `sort` int(11) DEFAULT \'0\' COMMENT \'排序\',
  `remark` text COMMENT \'备注\',
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `@pf_manage` VALUES (\'1\', \'admin\', \'e10adc3949ba59abbe56e057f20f883e\', \'wj008\', \'1\', \'0\', \'1999-01-01\', \'1999-01-01 00:00:00\', \'1999-01-01 00:00:00\', \'127.0.0.1\', \'127.0.0.1\', \'0\', \'\');
INSERT INTO `@pf_sysmenu` VALUES (\'1\', \'首页\', \'1\', \'0\', \'1\', \'\', \'10\', \'\', \'\');
INSERT INTO `@pf_sysmenu` VALUES (\'2\', \'系统账号管理\', \'1\', \'1\', \'1\', \'\', \'0\', \'\', \'icofont icofont-user-male\');
INSERT INTO `@pf_sysmenu` VALUES (\'3\', \'管理员管理\', \'1\', \'2\', \'1\', \'~/Manage\', \'12\', null, null);
INSERT INTO `@pf_sysmenu` VALUES (\'4\', \'修改管理密码\', \'1\', \'2\', \'1\', \'~/Manage/modifyPass\', \'0\', \'\', \'\');
INSERT INTO `@pf_sysmenu` VALUES (\'5\', \'网站信息管理\', \'1\', \'1\', \'1\', \'\', \'20\', \'\', \'icofont icofont-king-crown\');
INSERT INTO `@pf_sysmenu` VALUES (\'6\', \'系统菜单\', \'1\', \'0\', \'1\', null, \'400\', null, null);
INSERT INTO `@pf_sysmenu` VALUES (\'7\', \'工具箱\', \'1\', \'6\', \'1\', \'\', \'0\', \'\', \'icofont icofont-tools-alt-2\');
INSERT INTO `@pf_sysmenu` VALUES (\'8\', \'系统菜单管理\', \'1\', \'7\', \'1\', \'~/Sysmenu\', \'50\', null, null);');

                //安装工具库==============
                $db->exec('
-- ----------------------------
-- Table structure for @pf_tool_application
-- ----------------------------
DROP TABLE IF EXISTS `@pf_tool_application`;
CREATE TABLE `@pf_tool_application` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `namespace` varchar(255) DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for @pf_tool_field
-- ----------------------------
DROP TABLE IF EXISTS `@pf_tool_field`;
CREATE TABLE `@pf_tool_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formId` int(11) NOT NULL DEFAULT \'0\' COMMENT \'表单ID\',
  `name` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `boxName` text,
  `type` varchar(255) DEFAULT NULL,
  `hideBox` tinyint(1) DEFAULT NULL,
  `dbfield` tinyint(1) DEFAULT NULL,
  `dbtype` varchar(255) DEFAULT NULL,
  `dblen` int(11) DEFAULT NULL,
  `dbpoint` varchar(500) DEFAULT NULL,
  `dbcomment` varchar(255) DEFAULT NULL,
  `beforeText` varchar(255) DEFAULT NULL,
  `afterText` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `viewMerge` int(11) DEFAULT NULL,
  `close` tinyint(1) DEFAULT NULL,
  `viewClose` tinyint(1) DEFAULT NULL,
  `offEdit` tinyint(1) DEFAULT NULL,
  `forceDefault` tinyint(1) DEFAULT NULL,
  `default` text,
  `extendAttrs` text,
  `customAttrs` text,
  `dynamic` text,
  `boxPlaceholder` text,
  `boxClass` text,
  `boxStyle` text,
  `boxAttrs` text,
  `tips` text,
  `isEditTips` tinyint(1) DEFAULT NULL,
  `editTips` text,
  `viewTplName` varchar(255) DEFAULT NULL,
  `viewTplCode` text,
  `viewAsterisk` tinyint(4) DEFAULT NULL,
  `dataVal` text,
  `dataValMsg` text,
  `dataValGroup` text,
  `isEditDataVal` tinyint(1) DEFAULT NULL,
  `editDataVal` text,
  `editDataValMsg` text,
  `dataValInfo` text,
  `dataValValid` text,
  `dataValFor` varchar(255) DEFAULT NULL,
  `dataValOff` tinyint(1) DEFAULT NULL,
  `dataValEvents` varchar(255) DEFAULT NULL,
  `names` text,
  `viewTabIndex` varchar(255) DEFAULT NULL,
  `valueFunc` varchar(255) DEFAULT NULL,
  `validFunc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for @pf_tool_form
-- ----------------------------
DROP TABLE IF EXISTS `@pf_tool_form`;
CREATE TABLE `@pf_tool_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proId` int(11) DEFAULT NULL,
  `namespace` varchar(255) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `tbName` varchar(255) DEFAULT NULL,
  `tbEngine` varchar(255) DEFAULT NULL,
  `tbCreate` tinyint(1) DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `version` int(255) DEFAULT NULL,
  `extType` int(11) DEFAULT NULL,
  `extTbname` varchar(255) DEFAULT NULL,
  `extMode` int(11) DEFAULT NULL,
  `extFields` text,
  `useAjax` tinyint(4) DEFAULT NULL,
  `description` text,
  `isEditDescription` tinyint(4) DEFAULT NULL,
  `editDescription` text,
  `information` text,
  `attention` text,
  `script` text,
  `validateMode` int(11) DEFAULT NULL,
  `viewUseTab` tinyint(1) DEFAULT NULL,
  `viewTabs` text,
  `valueFuncArgs` varchar(255) DEFAULT NULL,
  `valueFuncSql` text,
  `valueFuncField` varchar(255) DEFAULT NULL,
  `viewNotBack` tinyint(1) DEFAULT NULL,
  `viewTemplate` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for @pf_tool_list
-- ----------------------------
DROP TABLE IF EXISTS `@pf_tool_list`;
CREATE TABLE `@pf_tool_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` varchar(255) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `formId` int(11) DEFAULT NULL,
  `proId` int(11) DEFAULT NULL,
  `fields` text,
  `orgFields` text,
  `listResize` tinyint(1) DEFAULT NULL,
  `usePageList` tinyint(1) DEFAULT NULL,
  `pageSize` int(11) DEFAULT NULL,
  `baseController` varchar(255) DEFAULT NULL,
  `useCustomTemplate` tinyint(1) DEFAULT NULL,
  `template` varchar(255) DEFAULT NULL,
  `templateHack` varchar(255) DEFAULT NULL,
  `baseLayout` varchar(255) DEFAULT NULL,
  `tbName` varchar(255) DEFAULT NULL,
  `tbNameAlias` varchar(255) DEFAULT NULL,
  `tbJoin` text,
  `tbField` text,
  `tbWhere` text,
  `tbOrder` text,
  `useSqlTemplate` tinyint(1) DEFAULT NULL,
  `sqlTemplate` text,
  `sqlCountTemplate` text,
  `actions` text,
  `topBtns` text,
  `thTitle` varchar(255) DEFAULT NULL,
  `thFixed` varchar(255) DEFAULT NULL,
  `thAlign` varchar(255) DEFAULT NULL,
  `thWidth` varchar(255) DEFAULT NULL,
  `thAttrs` text,
  `tdAlign` varchar(255) DEFAULT NULL,
  `tdAttrs` text,
  `listBtns` text,
  `useSelect` tinyint(4) DEFAULT NULL,
  `selectType` varchar(255) DEFAULT NULL,
  `selectBtns` text,
  `headTemplate` text,
  `buttomTemplate` text,
  `information` text,
  `attention` text,
  `assign` text,
  `viewTabs` text,
  `viewTabRight` text,
  `viewUseTab` tinyint(1) DEFAULT NULL,
  `leftFixed` int(11) DEFAULT NULL,
  `rightFixed` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for @pf_tool_search
-- ----------------------------
DROP TABLE IF EXISTS `@pf_tool_search`;
CREATE TABLE `@pf_tool_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `listId` int(11) NOT NULL DEFAULT \'0\' COMMENT \'表单ID\',
  `name` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `hideBox` tinyint(1) DEFAULT NULL,
  `beforeText` varchar(255) DEFAULT NULL,
  `afterText` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `viewMerge` int(11) DEFAULT NULL,
  `default` text,
  `forceDefault` tinyint(1) DEFAULT NULL,
  `extendAttrs` text,
  `customAttrs` text,
  `boxPlaceholder` text,
  `boxClass` text,
  `boxStyle` text,
  `boxAttrs` text,
  `names` text,
  `viewTabIndex` varchar(255) DEFAULT NULL,
  `tbWhere` text,
  `tbWhereType` int(11) DEFAULT NULL,
  `varType` varchar(255) DEFAULT \'\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;');

                $db->exec('
INSERT INTO `@pf_sysmenu` VALUES (\'10\', \'项目管理\', \'1\', \'7\', \'0\', \'^/tool/ToolApplication\', \'1\', \'\', \'\');
INSERT INTO `@pf_sysmenu` VALUES (\'11\', \'表单模型\', \'1\', \'7\', \'0\', \'^/tool/ToolForm\', \'2\', \'\', \'\');
INSERT INTO `@pf_sysmenu` VALUES (\'12\', \'列表模型\', \'1\', \'7\', \'0\', \'^/tool/ToolList\', \'3\', \'\', \'\');
');

                $db->exec('INSERT INTO `@pf_tool_application` VALUES (\'1\', \'系统后台\', \'app\\\\admin\', \'admin\');');

                unset($vals['is_create']);
                $code = '<?php return ' . var_export($vals, TRUE) . ';';
                $file = Utils::path(ROOT_DIR, '/config/db.config.php');
                file_put_contents($file, $code);
            } catch (\Exception $exception) {
                $this->error('保存失败：' . $exception->getMessage());
            }
            $this->success('保存成功', null, '/admin');
        }
    }

}