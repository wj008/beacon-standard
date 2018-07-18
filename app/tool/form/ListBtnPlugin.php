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

class ListBtnPlugin extends Form
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
                    ['<a href="{url act=\'edit\' id=$rs.id}" class="yee-btn small edit"><i class="icofont icofont-edit"></i>编辑</a>', '编辑 edit'],
                    ['<a href="{url act=\'show\' id=$rs.id}" class="yee-btn small show"><i class="icofont icofont-paper"></i>查看</a>', '查看 show'],
                    ['<a href="{url act=\'delete\' id=$rs.id}" yee-module="confirm ajaxlink" data-confirm="确定要删除该信息了吗？" class="yee-btn small del" onsuccess=" $(\'#list\').trigger(\'reload\');"><i class="icofont icofont-bin"></i>删除</a>', '删除 delete'],
                    ['<a href="{url act=\'allow\' id=$rs.id}" yee-module="ajaxlink"   onsuccess=" $(\'#list\').trigger(\'reload\');" class="yee-btn small edit"><i class="icofont icofont-check-circled"></i>审核</a>', '审核 allow'],
                    ['<a href="{url act=\'unallow\' id=$rs.id}" yee-module="ajaxlink"   onsuccess=" $(\'#list\').trigger(\'reload\');" class="yee-btn small edit"><i class="icofont icofont-not-allowed"></i>撤审</a>', '撤审 unallow'],
                    ['<a href="{url act=\'changeAllow\' id=$rs.id}" yee-module="ajaxlink" onsuccess=" $(\'#list\').trigger(\'reload\');" class="yee-btn small edit">{if $rs.allow}<i class="icofont icofont-not-allowed"></i>撤审{else}<i class="icofont icofont-check-circled"></i>审核{/if}</a>', '审核/撤审 changeAllow'],
                    ['<a href="javascript:;" yee-module="select_dialog" data-value="{$rs.id}" data-text="{$rs.name}"  class="yee-btn edit">选择</a>', '选择']
                ],
            ],

        ];
    }
}