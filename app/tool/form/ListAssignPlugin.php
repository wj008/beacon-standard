<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/13
 * Time: 16:35
 */

namespace app\tool\form;


use beacon\Form;
use beacon\Route;

class ListAssignPlugin extends Form
{

    protected function load()
    {
        return [
            'key' => [
                'label' => '变量注册名',
                'box-style' => 'width:100px;',
            ],
            'param' => [
                'label' => '参数名',
                'box-style' => 'width:100px;',
                'view-merge' => -1,
            ],
            'useSets' => [
                'label' => '使用集合',
                'type' => 'check',
                'view-merge' => -1,
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'setsSql,setsTips',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'setsSql,setsTips',
                    ],
                ],
            ],
            'global' => [
                'label' => '是否全局',
                'type' => 'check',
                'view-merge' => -1
            ],
            'tips' => [
                'label' => '参数为空提示',
                'type' => 'textarea',
                'box-style' => 'width:500px; height:20px;margin-top: 2px;',
                'default' => '参数错误'
            ],

            'setsSql' => [
                'label' => '集合查询SQL',
                'type' => 'textarea',
                'box-style' => 'width:500px; height:20px;margin-top: 2px;',
            ],
            'setsTips' => [
                'label' => '集合为空提示',
                'type' => 'textarea',
                'box-style' => 'width:500px; height:20px;margin-top: 2px;',
            ],
        ];
    }
}