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
            //   exit;
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