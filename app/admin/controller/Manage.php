<?php

namespace app\admin\controller;


/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/1/5
 * Time: 1:26
 */


use app\admin\form\ManageForm;
use beacon\Console;
use beacon\DB;
use beacon\Request;
use beacon\SqlSelector;

class Manage extends AdminController
{
    public function indexAction()
    {
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_manage');
            $name = $this->get('name', '');
            if ($name) {
                $selector->where("(`name` LIKE CONCAT('%',?,'%') OR realname LIKE CONCAT('%',?,'%'))", [$name, $name]);
            }
            $sort = $this->get('sort', 'id_asc');
            switch ($sort) {
                case 'id_asc':
                    $selector->order('id asc');
                    break;
                case 'id_desc':
                    $selector->order('id desc');
                    break;
                case 'name_asc':
                    $selector->order('name asc');
                    break;
                case 'name_desc':
                    $selector->order('name desc');
                    break;
                case 'realname_asc':
                    $selector->order('realname asc');
                    break;
                case 'realname_desc':
                    $selector->order('realname desc');
                    break;
                case 'type_asc':
                    $selector->order('type asc');
                    break;
                case 'type_desc':
                    $selector->order('type desc');
                    break;
                default:
                    $selector->order('id asc');
                    break;
            }
            $plist = $selector->getPageList(10);
            $pdata = $plist->getInfo();
            $list = $this->hackData('Manage.hack.tpl', $plist->getList());
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $this->success('获取数据成功', $data);
        }
        $this->display('Manage.tpl');
    }

    public function checkNameAction()
    {

        $username = $this->param('username', '');
        $id = $this->param('id', 0);
        $row = DB::getRow('select id from @pf_manage where `name`=? and id<>?', [$username, $id]);
        if ($row) {
            $this->error('用户名已经存在');
        }
        $this->success('用户名可以使用');
    }

    public function addAction()
    {
        $form = new ManageForm('add');
        if ($this->isGet()) {
            $this->displayForm($form);
            return;
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            DB::insert('@pf_manage', $vals);
            $this->success('添加' . $form->title . '成功');
        }
    }

    public function editAction(int $id = 0)
    {
        $form = new ManageForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $row = DB::getRow('select * from @pf_manage where id=?', $id);
        $form->initValues($row);

        if ($this->isGet()) {
            $this->displayForm($form);
            return;
        }

        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            DB::update('@pf_manage', $vals, $id);
            $this->success('编辑' . $form->title . '成功');
        }
    }

    public function delAction(int $id = 0)
    {
        if ($id == 0) {
            $this->error('参数有误');
        }
        if ($id == 1) {
            $this->error('最高管理员不可删除');
        }
        DB::delete('@pf_manage', $id);
        $this->success('删除账号成功');
    }

    //修改账号密码
    public function modifyPassAction()
    {
        if ($this->isGet()) {
            $this->assign('row', $this->getSession());
            $this->display('ManageModifyPass.tpl');
            return;
        }
        if ($this->isPost()) {
            $oldpass = $this->post('oldpass:s', '');
            $newpass = $this->post('newpass:s', '');
            if ($oldpass == '') {
                $this->error(['oldpass' => '旧密码不可为空']);
            }
            if ($newpass == '') {
                $this->error(['newpass' => '新密码不可为空']);
            }
            $row = DB::getRow('select id,pwd from @pf_manage where id=?', $this->adminId);
            if ($row == null) {
                $this->error('用户不存在');
            }
            if (md5($oldpass) != $row['pwd']) {
                $this->error(['oldpass' => '旧密码不正确，请重新输入']);
            }
            $newpass = md5($newpass);
            DB::update('@pf_manage', ['pwd' => $newpass], $this->adminId);
            $this->success('修改密码成功');
        }
    }
}