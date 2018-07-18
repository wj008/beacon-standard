<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/12
 * Time: 21:10
 */

namespace app\tool\form;


use beacon\Config;
use beacon\DB;
use beacon\Form;
use beacon\Request;
use beacon\Route;


class ToolSearchForm extends Form
{
    public $title = '搜索字段';
    public $caption = '工具-搜索字段管理';
    public $viewUseTab = true;
    public $useAjax = true;
    public $tbname = '@pf_tool_search';

    public $viewTabs = [
        'base' => '基本配置',
        'senior' => '高级配置',
    ];

    public $viewScript = 'Yee.loader(\'/tool/js/tool_search.js\');';

    public function __construct(string $type = '')
    {
        parent::__construct($type);
        if ($this->loadEdit()) {
            $this->addHideBox('id', Request::instance()->get('id:i', 0));
        }
    }

    protected function load()
    {
        return [

            'label' => [
                'label' => '字段标题 [label]',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入表单名称'],
                'view-tab-index' => 'base',
                'tips' => '提示：如果标题需要隐藏 可在标题前加 # 号 ',
            ],

            'btn1' => [
                'label' => '翻译',
                'type' => 'button',
                'box-href' => Route::url('~/tool_field/fanyi'),
                'box-yee-module' => 'ajaxlink',
                'box-onsuccess' => 'if(ret){$(\'#name\').val(ret.data.name);}',
                'data-carry' => '#label',
                'view-merge' => -1, //合并到上一行
                'view-tab-index' => 'base',
            ],

            'name' => [
                'label' => '字段名称 [name]',
                'data-val' => ['r' => true, 'regex' => '^[a-z][A-Za-z0-9_]+$'],
                'data-val-msg' => ['r' => '没有填写模型关键字！', 'regex' => '模型标识只能使用大写字母开头的数字及字母组合。'],
                'tips' => '字段名称将作为表字段名称',
                'box-style' => 'width:120px;',
                'view-tab-index' => 'base',
            ],

            'type' => [
                'label' => '字段类型 [type]',
                'view-tab-index' => 'base',
                'box-style' => 'width:170px;display:inline-block;',
                'type' => 'radiogroup', // 单选组
                'options' => Config::get('tool.search_type'), // 单选组的选项值
                'default' => 'text',
                'dynamic' => [
                    [
                        'eq' => 'hidden',
                        'show' => 'hideBox',
                    ],
                    [
                        'neq' => 'hidden',
                        'hide' => 'hideBox',
                    ],
                ],
            ],

            'hideBox' => [
                'label' => '底部隐藏输入框 [hideBox]', // 字段标题
                'type' => 'check', // 单选组
                'default' => 0,
                'after-text' => '勾选后直接在底部添加隐藏输入框',
                'view-tab-index' => 'base',
            ],

            'varType' => [
                'label' => '值类型 [var-type]',
                'type' => 'select',
                'options' => [
                    ['value' => 'string', 'text' => 'string'],
                    ['value' => 'integer', 'text' => 'integer'],
                    ['value' => 'boolean', 'text' => 'boolean'],
                    ['value' => 'float', 'text' => 'float'],
                    ['value' => 'array', 'text' => 'array'],
                ],
                'tips' => '选择值的变量类型',
                'view-tab-index' => 'base'
            ],

            'beforeText' => [
                'label' => '前置文本(小标题) [before-text]', // 字段标题
                'view-tab-index' => 'base',
            ],

            'afterText' => [
                'label' => '尾随文本(单位等) [after-text]', // 字段标题
                'view-tab-index' => 'base',
            ],
            'viewTabIndex' => [
                'label' => '选择所属Tab [view-tab-index]',
                'type' => 'select',
                'options' => [
                    ['value' => 'base', 'text' => '基本栏目'],
                    ['value' => 'senior', 'text' => '高级搜索'],
                ],
                'tips' => '选择所属TAB标签',
                'view-tab-index' => 'base'
            ],

            'tbWhere' => [
                'label' => 'SQL查询代码',
                'type' => 'textarea',
                'view-tab-index' => 'base',
                'tips' => '如 (模糊查找) `name` like concat(\'%\',?,\'%\') 或者 type=? 或者 datetime > ?'
            ],

            'tbWhereType' => [
                'label' => '加入条件',
                'type' => 'select',
                'var-type' => 'integer',
                'options' => [
                    [0, '为[空,0,null]不加入'],
                    [1, '为[null]不加入'],
                    [2, '为[空,null]不加入'],
                    [3, '为[0,null]不加入'],
                    [-1, '直接加入'],
                ],
                'view-tab-index' => 'base'
            ],

            'sort' => [
                'label' => '排序', // 字段标题
                'type' => 'integer',
                'view-tab-index' => 'base',
                'default' => function () {
                    $listId = Request::instance()->get('listId:i', 0);
                    return DB::getMax($this->tbname, 'sort', 'listId=?', $listId) + 10;
                },
            ],

            'viewMerge' => [
                'label' => '向上合并 [view-merge]', // 字段标题
                'type' => 'radiogroup', // 单选组
                'options' => [
                    ['value' => 0, 'text' => '不合并'],
                    ['value' => 1, 'text' => '向下合并'],
                    ['value' => -1, 'text' => '向上合并']
                ], // 单选组的选项值
                'default' => 0,
                'view-tab-index' => 'base',
            ],

            'default' => [
                'label' => '默认值 [default]',
                'type' => 'textarea',
                'box-style' => 'height:100px;width:480px',
                'tips' => '如果默认值是数组，请书写JSON格式',
                'view-tab-index' => 'base',
            ],
            'default_btn' => [
                'label' => '高级设置',
                'type' => 'button',
                'data-maxmin' => 'false',
                'data-width' => '700',
                'data-height' => '360',
                'box-style' => 'vertical-align: top;',
                'box-href' => Route::url('~/toolField/defaultSet'),
                'box-yee-module' => 'dialog',
                'box-onsuccess' => 'if(ret){$(\'#default\').val(ret.code);}',
                'box-onbefore' => '$(this).data(\'assign\',$(\'#default\').val());',
                'view-merge' => -1,
                'view-tab-index' => 'base',
            ],

            'forceDefault' => [
                'label' => '强制默认值 [force-default]', // 字段标题
                'type' => 'check', // 单选组
                'tips' => '强制使用默认值，如果数据为空 或 0 则强制使用默认值',
                'after-text' => '勾选空时强制使用默认值',
                'default' => 0,
                'view-tab-index' => 'base',
            ],

            'extendAttrs' => [
                'label' => '高级设置',
                'type' => 'ajax_plugin',
                'data-url' => Route::url('~/tool_search/load_plugin'),
                'data-bind-param' => 'type',
                'data-auto-load' => true,
                'view-tab-index' => 'senior',
            ],

            'custom-line' => [
                'label' => '自定义扩展属性',
                'type' => 'line',
                'view-tab-index' => 'senior',
            ],
            'customAttrs' => [
                'label' => '自定义属性 ',
                'type' => 'plugin',
                'plug-name' => 'CustomPlugin',
                'plug-type' => 3,
                'plug-mode' => 'composite',
                'view-tab-index' => 'senior',
                'tips' => '控件的内联样式',
            ],

            'box-line' => [
                'label' => 'Input 样式及属性',
                'type' => 'line',
                'view-tab-index' => 'base',
            ],
            'boxPlaceholder' => [
                'label' => '输入框内提示文本 [box-placeholder]',
                'type' => 'text',
                'tips' => '直接在输入框内的提示文本(placeholder)',
                'view-tab-index' => 'base',
            ],
            'boxClass' => [
                'label' => '控件CSS样式名称 [box-class]',
                'type' => 'text',
                'tips' => '默认系统会指定为 "form-inp 控件类型"',
                'view-tab-index' => 'base',
            ],
            'boxStyle' => [
                'label' => '内联style样式 [box-style]',
                'type' => 'textarea',
                'tips' => '控件的内联样式',
                'view-tab-index' => 'base',
            ],
            'boxAttrs' => [
                'label' => '其他属性 [box-*]',
                'type' => 'plugin',
                'plug-name' => 'InpAttributePlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'view-tab-index' => 'base',
                'tips' => '控件的内联样式',
            ],
            'listId' => [
                'label' => '表单ID',
                'type' => 'hidden',
                'hideBox' => true,
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '列表ID丢失'],
                'default' => Request::instance()->param('listId:i', 0),
                'view-tab-index' => 'base',
            ],
        ];
    }

}