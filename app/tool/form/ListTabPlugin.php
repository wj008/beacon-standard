<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/13
 * Time: 16:35
 */

namespace app\tool\form;


use beacon\Form;

class ListTabPlugin extends Form
{
    protected function load()
    {
        return [
            'name' => [
                'label' => '名称',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '标识不能为空'],
                'box-style' => 'width:120px;'
            ],
            'url' => [
                'label' => '链接',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '文本不能为空'],
                'view-merge' => -1,
            ],
            'useCode' => [
                'label' => '是否代码', //标题
                'type' => 'check', // 这里是一个 checkbox
                'default' => 0, //默认 选中
                'after-text' => '勾选使用代码', //在输入框尾部添加一个提示内容
                'view-tab-index' => 'other',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'code',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'code',
                    ],
                ],
                'view-merge' => -1,
            ],
            'code' => [
                'label' => '代码', //标题
                'type' => 'textarea',
                'box-style' => 'width:500px; height:20px;margin-top: 2px;',
            ],

        ];
    }
}