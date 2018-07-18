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

class ListTopBtnPlugin extends Form
{

    protected function load()
    {
        return [
            'code' => [
                'label' => '模板代码',
                'type' => 'textarea',
                'box-style' => 'width:700px; height:50px;',
            ],

            'temp' => [
                'label' => '快捷设置',
                'type' => 'select',
                'dbfield' => false,
                'box-class' => 'form-inp select shortcut',
                'view-merge' => -1,
                'header' => '快捷设置',
                'options' => [['<a id="add-btn" href="{url act=\'add\'}" class="yee-btn add"><i class="icofont icofont-ui-add"></i>新增</a>', '新增 add']],
            ],

        ];
    }
}