<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/11
 * Time: 16:27
 */

namespace app\tool\form;


use beacon\DB;
use beacon\Form;
use beacon\Request;
use beacon\Route;

class ToolFormForm extends Form
{
    public $title = '项目管理';
    public $caption = '工具-项目管理';
    public $viewUseTab = true;
    public $viewTabs = [
        'base' => '基本配置',
        'extend' => '扩展配置'
    ];
    public $useAjax = true;
    public $tbname = '@pf_tool_form';

    protected function load()
    {
        return [
            'title' => [
                'label' => '表单名称',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入表单名称'],
                'view-tab-index' => 'base',
            ],
            'btn1' => [
                'label' => '翻译',
                'type' => 'button',
                'box-href' => Route::url('~/toolForm/fanyi'),
                'box-yee-module' => 'ajaxlink',
                'box-onsuccess' => 'if(ret){$(\'#key\').val(ret.data.key);$(\'#tbName\').val(ret.data.tbname);}',
                'data-carry' => '#title',
                'view-merge' => -1, //合并到上一行
                'view-tab-index' => 'base',
            ],
            'caption' => [
                'label' => '表单标题',
                'view-tab-index' => 'base',
            ],
            'key' => [
                'label' => '模型关键字',
                'data-val' => ['r' => true, 'regex' => '^[A-Z][A-Za-z0-9]+$', 'remote' => [Route::url('~/tool_form/check_key'), 'Post', 'eid']],
                'data-val-msg' => ['r' => '没有填写模型关键字！', 'regex' => '模型标识只能使用大写字母开头的数字及字母组合。', 'remote' => '标识已经使用，请更换其他标识'],
                'tips' => '创建后不可更改，并具有唯一性，与文档的模板相关连，建议由英文、数字组成，因为部份Unix系统无法识别中文文件',
                'offedit' => true,
                'remote-func' => function ($value) {
                    $id = Request::instance()->param('id:i', 0);
                    $proId = Request::instance()->param('proId:i', 0);
                    $row = DB::getRow('select id from @pf_tool_form where `key`=? and id<>? and proId=?', [$value, $id, $proId]);
                    if ($row) {
                        return false;
                    }
                    return true;
                },
                'view-tab-index' => 'base',
            ],
            'proId' => [
                'label' => '所属项目', //标题
                'type' => 'select_dialog', //下拉框
                'default' => 0,
                'view-tab-index' => 'base',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '没有选择项目'],
                'data-url' => Route::url('~/toolApplication/dialog'),
                'data-width' => 860,
                'box-style' => 'width:350px',
                'text-func' => function ($value) {
                    $row = DB::getRow('select `name`,`namespace` from @pf_tool_application where  id=?', $value);
                    if ($row) {
                        return $row['name'] . ' (' . $row['namespace'] . ')';
                    }
                    return '';
                }
            ],
            'tbName' => [
                'label' => '数据库表名称', // 字段标题
                'type' => 'text', //输入框类型
                //验证数据
                'data-val' => ['r' => true, 'regex' => '^[a-z][a-z0-9_]+$'], // 验证规则 r 是简写  是验证不能为空
                'data-val-msg' => ['r' => '没有填数据库名称！', 'regex' => '数据库名称为小写字母下划线数字组合。'], //如果错误了 提示的
                'offedit' => true, //编辑状态下 这个不允许修改数据库
                'tips' => '自定义类型数据存放数据的表', //在输入框标题处 给个提示
                'view-tab-index' => 'base',
            ],
            'tbCreate' => [
                'label' => '是否创建数据库', //标题
                'type' => 'check', // 这里是一个 checkbox
                'default' => 1, //默认 选中
                'view-merge' => -1, //合并到上一行
                'after-text' => '勾选后将会创建对应的数据库表', //在输入框尾部添加一个提示内容
                'offedit' => true, //编辑状态下 这个不允许修改数据库
                'default' => true,
                'view-tab-index' => 'base',
            ],
            'tbEngine' => [
                'label' => '数据库存储引擎', //标题
                'type' => 'select', //下拉框
                'options' => [
                    ['value' => 'InnoDB', 'text' => 'InnoDB'],
                    ['value' => 'MyISAM', 'text' => 'MyISAM'],
                ], // 下拉框的两个选项
                'default' => 'InnoDB',
                'view-tab-index' => 'base',
            ],
            'extType' => [
                'label' => '选择类型', //标题
                'offedit' => true, //编辑不修改表
                'type' => 'radiogroup', // 单选组
                'options' => [['value' => 1, 'text' => '基本类型'], ['value' => 2, 'text' => '附加表类型']], // 单选组的选项值
                'default' => 1, //默认选中 值为1的
                'view-tab-index' => 'base',
            ],
            'extTbname' => [
                'label' => '选择主表',
                'offedit' => true,
                'type' => 'select',
                //验证数据
                'tips' => '作为为该主表的附加表添加',
                'header' => ['value' => '', 'text' => '请选择主表'],
                'options' => function () {
                    $list = DB::getList('select distinct tbName as `value`,tbName as text  from @pf_tool_form where tbCreate=1');
                    return $list;
                },
                'view-tab-index' => 'base',
            ],
            'extMode' => [
                'label' => '选择模式',
                'offedit' => true,
                'type' => 'radiogroup',
                'box-style' => 'width:230px; clear:both;',
                'options' => [
                    ['value' => 0, 'text' => '普通模式', 'tips' => '表单形式使用'],
                    ['value' => 1, 'text' => '插件模式', 'tips' => '即该模型仅作为表达插件使用'],
                    ['value' => 2, 'text' => '分类层级', 'tips' => '比如分类结构'],
                    ['value' => 3, 'text' => '常用字段', 'tips' => '一些常用的字段'],
                ],
                'default' => 0,
                'dynamic' => [
                    [
                        'eq' => 3,
                        'show' => 'extFields',
                    ],
                    [
                        'neq' => 3,
                        'hide' => 'extFields',
                    ],
                ],
                'view-tab-index' => 'base',
            ],
            'extFields' => [
                'label' => '常用字段',
                'offedit' => true,
                'type' => 'checkgroup',
                'box-style' => 'width:230px; clear:both;',
                'options' => [
                    ['value' => 'name', 'text' => '名称[name]'],
                    ['value' => 'title', 'text' => '标题[title]'],
                    ['value' => 'allow', 'text' => '审核[allow]'],
                    ['value' => 'sort', 'text' => '排序[sort]',]
                ],
                'default' => ['name', 'allow', 'sort'],
                'view-tab-index' => 'base',
            ],
            'useAjax' => [
                'label' => '使用AJAX提交',
                'offedit' => true,
                'type' => 'check',
                'default' => true,
                'view-tab-index' => 'base',
                'after-text' => '勾选使用AJAX提交表单',
            ],
            'viewNotBack' => [
                'label' => '不返回上页',
                'type' => 'check',
                'after-text' => '勾选添加编辑等不返回上页',
                'view-tab-index' => 'base',
            ],
            'validateMode' => [
                'label' => '验证提示方式',
                'offedit' => true,
                'type' => 'radiogroup',
                'default' => 0,
                'view-tab-index' => 'base',
                'options' => [
                    ['value' => 0, 'text' => '默认方式'],
                    ['value' => 2, 'text' => '对话框(单条)'],
                    ['value' => 1, 'text' => '对话框(多条)'],
                ],
            ],
            'viewTemplate' => [
                'label' => '使用模板',
                'tips' => '如果不输入使用默认模板 bodyForm.tpl 渲染',
                'view-tab-index' => 'base',
            ],
            'viewUseTab' => [
                'label' => '是否分栏', //标题
                'type' => 'check', // 这里是一个 checkbox
                'default' => 0, //默认 选中
                'after-text' => '勾选开启分栏', //在输入框尾部添加一个提示内容
                'view-tab-index' => 'extend',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'viewTabs',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'viewTabs',
                    ],
                ],
            ],
            'viewTabs' => [
                'label' => '分栏栏目', //标题
                'type' => 'plugin',
                'plug-name' => 'FormTabPlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'view-tab-index' => 'extend',
            ],
            'description' => [
                'label' => '头部说明',
                'type' => 'textarea',
                'view-tab-index' => 'extend',
            ],
            'isEditDescription' => [
                'label' => '修正编辑',
                'type' => 'check',
                'view-tab-index' => 'extend',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'editDescription',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'editDescription',
                    ],
                ],
            ],
            'editDescription' => [
                'label' => '编辑头部说明',
                'type' => 'textarea',
                'view-tab-index' => 'extend',
            ],
            'information' => [
                'label' => '提示信息',
                'type' => 'textarea',
                'tips' => '在底部的提示说明帮助',
                'view-tab-index' => 'extend',
            ],
            'attention' => [
                'label' => '警告提示',
                'type' => 'textarea',
                'tips' => '在底部的警告提示说明帮助',
                'view-tab-index' => 'extend',
            ],
            'script' => [
                'label' => '脚本代码',
                'type' => 'textarea',
                'tips' => '需要在页面中执行的JS代码',
                'box-style' => 'width:600px; height:200px;',
                'view-tab-index' => 'extend',
            ],
        ];
    }

    protected function loadEdit()
    {
        $this->addHideBox('id', Request::instance()->get('id:i', 0));
        return [
            'key' => [
                'data-val' => ['remote' => [Route::url('~/toolForm/checkKey'), 'post', 'id']]
            ]
        ];
    }


}