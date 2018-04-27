<?php

namespace app\service\form;

use beacon\Form;

class InstallForm extends Form
{
    public $title = '安装系统';
    public $caption = '安装系统';
    public $useAjax = true;

    protected function load()
    {
        return [
            'db_host' => [
                'label' => '数据库HOST：',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入数据库HOST'],
                'default' => '127.0.0.1',
            ],
            'db_port' => [
                'label' => '数据库端口',
                'type' => 'integer',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入数据库端口'],
                'default' => 3306,
            ],
            'db_name' => [
                'label' => '数据库名称',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入数据库名称'],
            ],
            'db_user' => [
                'label' => '数据库账号',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入数据库账号'],
                'default' => 'root',
            ],
            'db_pwd' => [
                'label' => '数据库密码',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入数据库密码'],
            ],
            'db_prefix' => [
                'label' => '数据库表前缀',
                'data-val' => ['r' => true, 'regex' => '^\w+$'],
                'data-val-msg' => ['r' => '请输入数据库表前缀', 'regex' => '格式不正确'],
                'default' => 'sl_',
            ],
            'is_create' => [
                'label' => '否创建数据库',
                'type' => 'check',
                'after-text' => '勾选创建数据库',
            ],
        ];
    }
}