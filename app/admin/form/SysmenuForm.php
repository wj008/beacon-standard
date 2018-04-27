<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/27
 * Time: 1:28
 */

namespace app\admin\form;


use beacon\DB;
use beacon\Form;
use beacon\Request;
use beacon\Route;

class SysmenuForm extends Form
{
    public $title = '菜单管理';
    public $caption = '系统-菜单管理';
    public $tbname = '@pf_sysmenu';
    public $useAjax = true;

    protected function load()
    {
        return [
            'name' => [
                'label' => '菜单名称',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入菜单名称'],
                'tips' => '请输入系统菜单名称',
            ],
            'allow' => [
                'label' => '是否启用',
                'type' => 'check',
                'after-text' => '勾选启用菜单',
                'default' => true,
            ],

            'pid' => [
                'label' => '所属上级',
                'type' => 'select',
                'header' => [0, '顶级菜单'],
                'options' => function () {
                    $items = [];
                    $rows = DB::getList('select * from @pf_sysmenu where pid=0 order by sort asc');
                    foreach ($rows as $item) {
                        $items[] = [$item['id'], $item['name']];
                        $temp = DB::getList('select * from @pf_sysmenu where pid=?  order by sort asc', $item['id']);
                        foreach ($temp as $rs) {
                            $items[] = [$rs['id'], '+--- ' . $rs['name']];
                        }
                    }
                    return $items;
                },
                'default' => Request::instance()->get('pid:i', 0),
                'tips' => '选择该菜单所在的上级菜单',
            ],

            'icon' => [
                'label' => 'ICON样式', //标题
                'type' => 'select_dialog', //下拉框
                'data-url' => Route::url('^/service/IconSelect'),
                'data-width' => 860,
                'box-style' => 'width:300px',
                'tips' => '图标样式，仅菜单项需要输入 <a href="http://icofont.com/icons/" target="_blank">打开icon参考链接</a>',
            ],

            'url' => [
                'label' => '连接',
                'tips' => '连接地址,仅最后一层需要输入',
            ],
            'sort' => [
                'label' => '排序',
                'type' => 'integer',
                'tips' => '越小越靠前',
                'default' => function () {
                    return $this->maxSort();
                }
            ],
            'remark' => [
                'label' => '备注信息',
                'type' => 'textarea',
                'tips' => '越小越靠前',
            ],
        ];

    }
}