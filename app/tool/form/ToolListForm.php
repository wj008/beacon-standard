<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/11
 * Time: 16:27
 */

namespace app\tool\form;


use beacon\Config;
use beacon\DB;
use beacon\Form;
use beacon\Request;
use beacon\Route;

class ToolListForm extends Form
{
    public $title = '项目管理';
    public $caption = '工具-列表管理';
    public $viewUseTab = true;
    public $viewTabs = [
        'base' => '基本配置',
        'data' => '数据查询',
        'operate' => '操作项',
        'other' => '其他项',
    ];

    public $useAjax = true;
    public $tbname = '@pf_tool_list';
    public $viewScript = 'Yee.loader(\'/tool/js/tool_list.js\');';
    public $viewDescription = '注意：为了防止模板与脚本等冲突，如果在脚本中有{}的，请使用 {literal}{/literal} 包括起来';

    protected function load()
    {
        return [
            'formId' => [
                'label' => '选择表单模型',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请选择表单模型'],
                'type' => 'select',
                'header' => '请选择表单模型',
                'options' => function () {
                    $options = [];
                    $rows = DB::getList('select * from @pf_tool_form where extMode<>1 order by id desc');
                    foreach ($rows as $rs) {
                        $item = [];
                        $item[] = isset($rs['id']) ? $rs['id'] : '';
                        $item[] = isset($rs['title']) ? $rs['title'] : '';
                        $item[] = isset($rs['key']) ? $rs['key'] : '';
                        $options[] = $item;
                    }
                    return $options;
                },
                'view-tab-index' => 'base',
            ],
            'key' => [
                'label' => '列表标识符',
                'data-val' => ['r' => true, 'regex' => '^[A-Z][A-Za-z0-9]+$', 'remote' => [Route::url('~/toolList/checkKey'), 'Post', 'eid']],
                'data-val-msg' => ['r' => '没有填写模型关键字！', 'regex' => '模型标识只能使用大写字母开头的数字及字母组合。', 'remote' => '标识已经使用，请更换其他标识'],
                'tips' => '创建后不可更改，并具有唯一性，与文档的模板相关连，建议由英文、数字组成，因为部份Unix系统无法识别中文文件',
                'offedit' => true,
                'remote-func' => function ($value) {
                    $id = Request::instance()->param('id:i', 0);
                    $row = DB::getRow('select id from @pf_tool_list where `key`=? and id<>?', [$value, $id]);
                    if ($row) {
                        return false;
                    }
                    return true;
                },
                'view-tab-index' => 'base',
            ],

            'title' => [
                'label' => '列表名称',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入表单名称'],
                'view-tab-index' => 'base',
            ],

            'caption' => [
                'label' => '列表标题',
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

            'fields' => [
                'label' => '字段信息 ',
                'type' => 'plugin',
                'plug-name' => 'ListFieldPlugin',
                'plug-type' => 2,
                'plug-mode' => 'composite',
                'view-tab-index' => 'base',
            ],
            'useTwoLine' => [
                'label' => '使用两行',
                'type' => 'check',
                'after-text' => '勾选使用两行,会将最后一列拆到下一行',
                'view-tab-index' => 'base',
            ],

            'orgFields' => [
                'label' => '其他未修饰字段',
                'type' => 'text',
                'box-placeholder' => 'id,name,title',
                'box-style' => 'width:400px',
                'view-tab-index' => 'base',
                'tips' => '未修饰的字段只用于模板数据处理使用，并不作为列显示，多个用逗号隔开',
            ],

            'listResize' => [
                'label' => '可调整列',
                'type' => 'check',
                'after-text' => '勾选拖动调整列宽',
                'view-tab-index' => 'base',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'leftFixed,rightFixed',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'leftFixed,rightFixed',
                    ],
                ],
            ],

            'leftFixed' => [
                'label' => '左固定列数',
                'type' => 'integer',
                'view-tab-index' => 'base',
            ],

            'rightFixed' => [
                'label' => '右固定列数',
                'type' => 'integer',
                'view-tab-index' => 'base',
                'view-merge' => -1, //合并到上一行
            ],

            'usePageList' => [
                'label' => '是否使用分页',
                'type' => 'check',
                'after-text' => '勾选使用分页',
                'view-tab-index' => 'base',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'pageSize',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'pageSize',
                    ],
                ],
            ],
            'pageSize' => [
                'label' => '每页记录数 ',
                'type' => 'integer',
                'default' => 20,
                'view-tab-index' => 'base',
            ],
            'baseController' => [
                'label' => '控制器继承于',
                'view-tab-index' => 'base',
                'tips' => '将会生托管生成一个同名控制器继承于此控制器',
            ],

            'useCustomTemplate' => [
                'label' => '使用自定义模板',
                'type' => 'check',
                'after-text' => '勾选使用自定义模板',
                'view-tab-index' => 'base',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'templateHack,template',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'templateHack,template',
                    ],
                ],
            ],

            'templateHack' => [
                'label' => '数据调整模板',
                'view-tab-index' => 'base',
                'tips' => '用于调整数据格式的模板',
            ],

            'template' => [
                'label' => '列表模板',
                'view-tab-index' => 'base',
                'tips' => '列表使用的模板,使用模板后其他列表设置无效',
            ],

            'baseLayout' => [
                'label' => '皮肤文件Layout',
                'view-tab-index' => 'base',
                'default' => 'layoutDataTable.tpl',
                'tips' => '父页面皮肤文件，如果为空 默认使用系统皮肤文件 layoutDataTable.tpl',
            ],
            'tbName' => [
                'label' => '主表',
                'view-tab-index' => 'data',
                'box-disabled' => 'disabled',
                'tips' => '主表和所选表单模型保持一致',
            ],
            'tbNameAlias' => [
                'label' => '主表别名',
                'view-tab-index' => 'data',
                'box-placeholder' => '别名',
                'view-merge' => -1,
                'data-val' => ['regex' => '^[A-Z]+$'],
                'data-val-msg' => ['regex' => '表别名只能是大写字母'],
                'box-style' => 'width:100px;',
            ],
            'tbWhere' => [
                'label' => '查询条件',
                'type' => 'plugin',
                'plug-name' => 'ListWherePlugin',
                'box-placeholder' => '如 and `name`=?',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'view-tab-index' => 'data',
                'viewShowRemoveBtn' => true,
            ],

            'useSqlTemplate' => [
                'label' => '使用SQL模板',
                'after-text' => '勾选使用SQL模板',
                'type' => 'check',
                'view-tab-index' => 'data',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'sqlTemplate',
                        'hide' => 'tbJoin,tbField,tbOrder',
                    ],
                    [
                        'neq' => 1,
                        'show' => 'tbJoin,tbField,tbOrder',
                        'hide' => 'sqlTemplate',
                    ],
                ],
            ],

            'tbJoin' => [
                'label' => '附加表',
                'type' => 'plugin',
                'plug-name' => 'ListTbJoinPlugin',
                'plug-type' => 5,
                'box-placeholder' => '附加表',
                'plug-mode' => 'composite',
                'view-tab-index' => 'data',
                'viewShowRemoveBtn' => true,
            ],
            'tbField' => [
                'label' => '查询字段',
                'type' => 'textarea',
                'box-placeholder' => '查询字段,为空则为 *',
                'view-tab-index' => 'data',
            ],

            'tbOrder' => [
                'label' => '数据排序',
                'type' => 'textarea',
                'view-tab-index' => 'data',
                'box-placeholder' => '如 sort desc,id asc',
                'default' => 'id desc',
            ],

            'sqlTemplate' => [
                'label' => 'SQL模板',
                'type' => 'textarea',
                'tips' => '支持模板语法 参数数组：{$param}，条件：{$where|raw}，raw 过滤器可排除SQL编码',
                'box-style' => 'width:700px; height:120px;',
                'view-tab-index' => 'data',
            ],

            'sqlCountTemplate' => [
                'label' => '查询数量SQL模板',
                'type' => 'textarea',
                'tips' => '用于加速查询条数的SQL,非必填,支持模板语法，参数数组：{$param}，条件：{$where|raw}，raw 过滤器可排除SQL编码',
                'box-style' => 'width:700px; height:120px;',
                'view-tab-index' => 'data',
                'box-placeholder' => '如 select count(*) from `@pf_test` where 1=1 {$where|raw}',
            ],
            'actionLine' => [
                'label' => '公开的方法',
                'type' => 'line',
                'view-tab-index' => 'operate',
            ],
            'actions' => [
                'label' => '公开方法',
                'type' => 'checkgroup',
                'options' => Config::get('tool.support_action'),
                'view-tab-index' => 'operate',
            ],
            'topLine' => [
                'label' => '顶部操作区',
                'type' => 'line',
                'view-tab-index' => 'operate',
            ],
            'topBtns' => [
                'label' => '顶部右侧操作区域',
                'type' => 'plugin',
                'plug-name' => 'ListTopBtnPlugin',
                'plug-type' => 3,
                'plug-mode' => 'composite',
                'view-tab-index' => 'operate',
                'viewShowRemoveBtn' => true,
                'viewShowInsertBtn' => true,
                'viewShowSortBtn' => true,
            ],

            'listLine' => [
                'label' => '列表操作区',
                'type' => 'line',
                'view-tab-index' => 'operate',
            ],
            'thTitle' => [
                'label' => '操作区TH列标题',
                'tips' => '如果不需要列表操作区，此处留空',
                'box-style' => 'width:160px;',
                'view-tab-index' => 'operate',
                'default' => '操作',
            ],
            'thFixed' => [
                'label' => '固定列',
                'type' => 'select',
                'options' => [['', '不固定'], ['left', '左固定'], ['right', '右固定']],
                'view-tab-index' => 'operate',
                'view-merge' => -1,
            ],
            'thAlign' => [
                'label' => 'Th对齐',
                'type' => 'select',
                'options' => [['', '默认对齐'], ['left', 'left'], ['center', 'center'], ['right', 'right']],
                'view-tab-index' => 'operate',
                'default' => 'center',
            ],
            'thWidth' => [
                'label' => '宽',
                'view-merge' => -1,
                'box-style' => 'width:50px;',
                'default' => 180,
                'view-tab-index' => 'operate',
            ],
            'thAttrs' => [
                'label' => '其他属性',
                'view-merge' => -1,
                'view-tab-index' => 'operate',
            ],

            'tdAlign' => [
                'label' => 'TD对齐',
                'type' => 'select',
                'options' => [['', '默认对齐'], ['left', 'left'], ['center', 'center'], ['right', 'right']],
                'view-tab-index' => 'operate',
                'default' => 'center',
            ],
            'tdAttrs' => [
                'label' => '其他属性',
                'view-merge' => -1,
                'view-tab-index' => 'operate',
            ],

            'listBtns' => [
                'label' => '列表操作区域',
                'type' => 'plugin',
                'plug-name' => 'ListBtnPlugin',
                'plug-type' => 3,
                'plug-mode' => 'composite',
                'view-tab-index' => 'operate',
                'viewShowRemoveBtn' => true,
                'viewShowInsertBtn' => true,
                'viewShowSortBtn' => true,
            ],

            'selectLine' => [
                'label' => '全选操作区',
                'type' => 'line',
                'view-tab-index' => 'operate',
            ],

            'useSelect' => [
                'label' => '是否支持全选',
                'type' => 'check',
                'after-text' => '勾选支持全选',
                'view-tab-index' => 'operate',
            ],

            'selectType' => [
                'label' => '全选按钮位置',
                'type' => 'select',
                'options' => [['search', '搜索区右侧'], ['buttom', '列表底部'], ['top', '列表顶部']],
                'view-tab-index' => 'operate',
            ],

            'selectBtns' => [
                'label' => '全选区域操作',
                'type' => 'plugin',
                'plug-name' => 'ListButBtnPlugin',
                'plug-type' => 3,
                'plug-mode' => 'composite',
                'view-tab-index' => 'operate',
                'viewShowRemoveBtn' => true,
                'viewShowInsertBtn' => true,
                'viewShowSortBtn' => true,
            ],

            'assignLine' => [
                'label' => '注册变量',
                'type' => 'line',
                'view-tab-index' => 'other',
            ],

            'assign' => [
                'label' => '全选区域操作',
                'type' => 'plugin',
                'plug-name' => 'ListAssignPlugin',
                'plug-type' => 3,
                'plug-mode' => 'composite',
                'view-tab-index' => 'other',
                'viewShowRemoveBtn' => true,
                'viewShowInsertBtn' => true,
                'viewShowSortBtn' => true,
            ],
            'templateLine' => [
                'label' => '模板其他',
                'type' => 'line',
                'view-tab-index' => 'other',
            ],

            'viewUseTab' => [
                'label' => '是否分栏', //标题
                'type' => 'check', // 这里是一个 checkbox
                'default' => 0, //默认 选中
                'after-text' => '勾选开启分栏', //在输入框尾部添加一个提示内容
                'view-tab-index' => 'other',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'viewTabs,viewTabRight',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'viewTabs,viewTabRight',
                    ],
                ],
            ],
            'viewTabs' => [
                'label' => '分栏栏目', //标题
                'type' => 'plugin',
                'plug-name' => 'ListTabPlugin',
                'plug-type' => 3,
                'plug-mode' => 'composite',
                'viewShowRemoveBtn' => true,
                'view-tab-index' => 'other',
            ],
            'viewTabRight' => [
                'label' => '分栏右侧', //标题
                'type' => 'textarea',
                'view-tab-index' => 'other',
            ],

            'headTemplate' => [
                'label' => '页面head区域模板',
                'type' => 'textarea',
                'tips' => '可放置脚本样式等引用',
                'view-tab-index' => 'other',
            ],

            'buttomTemplate' => [
                'label' => '页面底部区域模板',
                'type' => 'textarea',
                'tips' => '可放置底部脚本，或其他版权等信息',
                'view-tab-index' => 'other',
            ],

            'information' => [
                'label' => '提示信息',
                'type' => 'textarea',
                'tips' => '在底部的提示说明帮助',
                'view-tab-index' => 'other',
            ],

            'attention' => [
                'label' => '警告提示',
                'type' => 'textarea',
                'tips' => '在底部的警告提示说明帮助',
                'view-tab-index' => 'other',
            ],

        ];
    }

    protected function loadEdit()
    {
        $this->addHideBox('id', Request::instance()->get('id:i', 0));
        return [
            'key' => [
                'data-val' => ['remote' => [Route::url('~/toolList/checkKey'), 'post', 'id']]
            ]
        ];
    }
}