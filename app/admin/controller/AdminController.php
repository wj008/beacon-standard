<?php

namespace app\admin\controller;

/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/1/4
 * Time: 18:37
 */


use beacon\Controller;
use beacon\DB;
use beacon\Form;


abstract class AdminController extends Controller
{
    protected $adminId = 0;
    protected $adminName = '';

    public function initialize()
    {
        $this->checkLogin();
    }

    protected function checkLogin()
    {
        $this->adminId = $this->getSession('adminId', 0);
        $this->adminName = $this->getSession('adminName', '');
        if (!empty($this->adminId) && !empty($this->adminName)) {
            return;
        }
        if ($this->isGet()) {
            $this->display('Login.tpl');
            exit;
        }
        $username = $this->post('username:s', '');
        $password = $this->post('password:s', '');
        $code = strtoupper($this->post('code:s', ''));
        if ($username == '') {
            $this->error('用户名不能为空！');
        }
        if ($password == '') {
            $this->error('用户密码不能为空！');
        }
        $pcode = $this->getSession('validcode') || '';
        if ($pcode == '' || $pcode != $code) {
            $this->setSession('code', '');
            $this->error('验证码有误！');
        }
        $row = DB::getRow('select * from @pf_manage where `name`=?', $username);
        if ($row == null) {
            $this->error('账号不存在！');
        }
        if ($row['pwd'] != md5($password)) {
            $this->error('用户密码不正确！');
        }
        $this->setSession('adminId', $row['id']);
        $this->setSession('adminName', $row['name']);
        //Console::log($this->getSession());
        $vals = [];
        if (isset($row['thistime']) && isset($row['lasttime'])) {
            $vals['thistime'] = date('Y-m-d H:i:s');
            $vals['lasttime'] = $row['thistime'];
        }
        if (isset($row['thisip']) && isset($row['lastip'])) {
            $vals['thisip'] = $this->getIP();
            $vals['lastip'] = $row['thisip'];
        }
        if (count($vals) > 0) {
            DB::update('@pf_manage', $vals, 'id=?', $row['id']);
        }
        $this->redirect('~/index');
        exit;
    }

    protected function displayForm(Form $form, string $tplname = '')
    {
        $this->assign('form', $form);
        if (empty($tplname)) {
            if (!empty($form->viewTemplate)) {
                $tplname = $form->viewTemplate;
            } else {
                $tplname = 'bodyForm.tpl';
            }
        }
        return parent::display($tplname);
    }

    protected function displayList($tplname = 'layoutList.tpl')
    {
        return parent::display($tplname);
    }
}