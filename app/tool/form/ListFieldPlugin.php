<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/13
 * Time: 16:35
 */

namespace app\tool\form;


use beacon\DB;
use beacon\Form;

class ListFieldPlugin extends Form
{
    public $viewtplName = 'widget/list_field.tpl';

    //public $tbname = '@pf_tool_listfield';

    protected function load()
    {
        return [

            'title' => [
                'label' => '标题',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '标识不能为空'],
                'box-class' => 'form-inp stext title',
                'box-style' => 'width:145px;',
            ],
            'orderName' => [
                'label' => '排序',
                'view-merge' => -1,
                'type' => 'dynamic_select',
                'header' => ['', '选择字段'],
                'box-style' => 'max-width:200px;',
            ],

            'thAlign' => [
                'label' => 'Th属性',
                'type' => 'select',
                'options' => [['', '默认对齐'], ['left', 'left'], ['center', 'center'], ['right', 'right']],
                'default' => 'center',
            ],
            'thWidth' => [
                'label' => '宽',
                'view-merge' => -1,
                'box-style' => 'width:50px;',
                'default' => 80
            ],
            'thAttrs' => [
                'label' => '其他属性',
                'view-merge' => -1,
            ],
            'tdAlign' => [
                'label' => 'TD对齐',
                'type' => 'select',
                'options' => [['', '默认对齐'], ['left', 'left'], ['center', 'center'], ['right', 'right']],
                'default' => 'center',
            ],
            'tdAttrs' => [
                'label' => '其他属性',
                'view-merge' => -1,
            ],
            'keyname' => [
                'label' => '指定键名',
                'box-style' => 'width:150px;',
                'box-placeholder' => '不指定系统自动分配',
            ],
            'field' => [
                'label' => '数据库字段',
                'type' => 'dynamic_select',
                'header' => ['', '选择字段'],
            ],
            'code' => [
                'label' => '值',
                'type' => 'textarea',
                'box-style' => "width: 450px; height:60px;",
                'box-class' => 'form-inp textarea code',

            ],

        ];
    }
}