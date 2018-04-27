<?php

namespace app\admin\form;

/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/1/5
 * Time: 1:15
 */


use beacon\DB;
use beacon\Form;
use beacon\Request;
use beacon\Route;

class ManageForm extends Form
{
    public $title = '账号管理';
    public $caption = '账号管理';
    public $useAjax = true;

    protected function load()
    {
        $load = [
            'name' => [
                'label' => '账号名称',
                'data-val' => ['r' => true, 'minlen' => 5, 'maxlen' => 20, 'remote' => [Route::url('~/manage/check_name'), 'post']],
                'data-val-msg' => ['r' => '请输入账号名称', 'minlen' => '账号名称至少是5个字符以上', 'maxlen' => '账号名称过长，不可超过20个字符'],
                'data-val-events' => 'blur',
                'tips' => '请输入管理员账号名称，5-20位字母数字组合',
                'box-class' => 'form-inp text',
                'box-name' => 'username',
                'remote-func' => function ($value) {
                    $id = Request::instance()->param('id:i', 0);
                    $row = DB::getRow('select id from @pf_manage where `name`=? and id<>?', [$value, $id]);
                    if ($row) {
                        return false;
                    }
                    return true;
                },
            ],
            'realname' => [
                'label' => '真实姓名',
                'tips' => '请输入真实姓名',
                'box-class' => 'form-inp text',
            ],
            'pwd' => [
                'label' => '账号密码',
                'type' => 'password',
                'data-val' => ['r' => true, 'minlen' => 6, 'maxlen' => 20],
                'data-val-msg' => ['r' => '请输入新密码', 'minlen' => '密码至少是6个字符以上', 'maxlen' => '密码过长，不可超过20个字符'],
                'tips' => '设置账号密码，请输入6-20位字符',
                'encode-func' => 'md5',
                'box-class' => 'form-inp text',
                'box-name' => 'password'
            ],
            'cfmpass' => [
                'label' => '确认密码',
                'type' => 'password',
                'close' => true,
                'data-val' => ['eqto' => '#password'],
                'data-val-msg' => ['eqto' => '两次输入的密码不一致'],
                'tips' => '再次输入密码',
                'encode-func' => 'md5',
                'box-class' => 'form-inp text',
                'box-name' => 'cfmpass'
            ],
        ];
        if ($this->isEdit()) {
            $load['name']['data-val']['remote'] = [Route::url('~/manage/check_name'), 'post', 'id'];
            $load['pwd']['data-val'] = ['minlen' => 6, 'maxlen' => 20];
            $this->addHideBox('id', Request::instance()->get('id:i', 0));
        }
        return $load;
    }
}