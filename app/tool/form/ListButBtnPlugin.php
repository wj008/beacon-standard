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

class ListButBtnPlugin extends Form
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
                'options' => [
                    ['<a href="{url act=\'deleteSelect\'}" data-batch="sel_id"  yee-module="confirm ajaxlink" data-confirm="确定要删除所选该信息了吗？" class="yee-btn small del" onsuccess=" $(\'#list\').trigger(\'reload\');"><i class="icofont icofont-bin"></i>删除所选</a>', '删除所选 deleteSelect'],
                    ['<a href="{url act=\'allowSelect\'}" data-batch="sel_id"  yee-module="ajaxlink" class="yee-btn small del" onsuccess=" $(\'#list\').trigger(\'reload\');"><i class="icofont icofont-not-allowed"></i>审核所选</a>', '审核所选 allowSelect'],
                    ['<a href="{url act=\'unAllowSelect\'}" data-batch="sel_id"  yee-module="ajaxlink" class="yee-btn small del" onsuccess=" $(\'#list\').trigger(\'reload\');"><i class="icofont icofont-check-circled"></i>撤销审核</a>', '撤销审核 unAllowSelect'],
                ],
            ],

        ];
    }
}