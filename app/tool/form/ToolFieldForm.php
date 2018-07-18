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


class ToolFieldForm extends Form
{
    public $title = '字段管理';
    public $caption = '工具-表单字段管理';
    public $viewUseTab = true;
    public $useAjax = true;
    public $tbname = '@pf_tool_field';

    public $viewTabs = [
        'base' => '基本配置',
        'extend' => '高级配置',
        'view' => '视图及属性',
        'valid' => '验证相关',

    ];
    public $viewScript = 'Yee.loader(\'/tool/js/tool_field.js\');';

    public function __construct(string $type = '')
    {
        parent::__construct($type);
        if ($this->isEdit()) {
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
                'tips' => '提示：如果标题需要隐藏 可在标题前加 ! 号 ',
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
                'data-val' => ['r' => true, 'regex' => '^[a-z][A-Za-z0-9_]+$', 'remote' => [Route::url('~/tool_field/check_name'), 'POST', 'id,formId']],
                'data-val-msg' => ['r' => '没有填写模型关键字！', 'regex' => '字段标识只能使用大写字母开头的数字及字母组合。', 'remote' => '标识已经使用，请更换其他标识'],
                'remote-func' => function ($value) {
                    $id = Request::instance()->param('id:i', 0);
                    $formId = Request::instance()->param('formId:i', 0);
                    $row = DB::getRow('select id from @pf_tool_field where `name`=? and id<>? and formId=?', [$value, $id, $formId]);
                    if ($row) {
                        return false;
                    }
                    return true;
                },
                'tips' => '字段名称将作为表字段名称',
                'box-style' => 'width:120px;',
                'view-tab-index' => 'base',
            ],

            'boxName' => [
                'label' => '输入框名称 [box-name]', // 字段标题
                'tips' => '输入框的 name 属性值,如果不填则与字段名称一致',
                'view-tab-index' => 'base',
                'box-style' => 'width:120px;',
                // 'view-merge' => -1,
            ],

            'type' => [
                'label' => '字段类型 [type]',
                'view-tab-index' => 'base',
                'box-style' => 'width:170px;display:inline-block;',
                'type' => 'radiogroup', // 单选组
                'options' => Config::get('tool.support_type'), // 单选组的选项值
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

            'dbfield' => [
                'label' => '是否数据库字段 [dbfield]',
                'type' => 'check',
                'default' => 1,
                'after-text' => '是否同步创建数据库字段',
                //同步动态
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => ['dbtype', 'dbcomment'],
                    ],
                    [
                        'neq' => 1,
                        'hide' => ['dbtype', 'dbcomment'],
                    ],
                ],
                'view-tab-index' => 'base',
            ],
            'dbtype' => [
                'label' => '数据库字段类型 [dbtype]',
                'type' => 'dynamic_select',
                'header' => ['', '数据库字段类型'],
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请选择字段类型'],
                'tips' => '在数据库中的字段类型',
                'view-tab-index' => 'base',
            ],

            'dblen' => [
                'label' => '长度 [dblen]',
                'type' => 'integer',
                'view-merge' => -1,
                'box-style' => "width: 80px",
                'view-tab-index' => 'base',
            ],
            'dbpoint' => [
                'label' => '小数点 [dbpoint]',
                'type' => 'integer',
                'view-merge' => -1,
                'box-style' => "width: 80px",
                'view-tab-index' => 'base',
            ],

            'dbpoint' => [
                'label' => '小数点 [dbpoint]',
                'type' => 'integer',
                'view-merge' => -1,
                'box-style' => "width: 80px",
                'view-tab-index' => 'base',
            ],

            'dbcomment' => [
                'label' => '字段备注 [dbcomment]',
                'type' => 'text',
                'tips' => '如果为空，使用标题作为备注',
                'box-style' => "width: 380px",
                'view-tab-index' => 'base',
            ],

            'beforeText' => [
                'label' => '前置文本(小标题) [before-text]', // 字段标题
                'view-tab-index' => 'base',
            ],

            'afterText' => [
                'label' => '尾随文本(单位等) [after-text]', // 字段标题
                'view-tab-index' => 'base',
            ],

            'sort' => [
                'label' => '排序', // 字段标题
                'type' => 'integer',
                'view-tab-index' => 'base',
                'default' => function () {
                    $formId = Request::instance()->get('formId:i', 0);
                    return DB::getMax($this->tbname, 'sort', 'formId=?', $formId) + 10;
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

            'close' => [
                'label' => '关闭控件 [close]', // 字段标题
                'type' => 'check', // 单选组
                'tips' => '被关闭的控件不会输出任何HTML代码，也不会保存入库',
                'after-text' => '勾选关闭该控件',
                'default' => 0,
                'view-tab-index' => 'base',
            ],

            'viewClose' => [
                'label' => '关闭视图 [view-close]', // 字段标题
                'type' => 'check', // 单选组
                'tips' => '仅关闭视图，保存入库时使用默认值',
                'after-text' => '勾选关闭该控件视图',
                'default' => 0,
                'view-tab-index' => 'base',
            ],

            'offEdit' => [
                'label' => '编辑状态只读 [off-edit]', // 字段标题
                'type' => 'check', // 单选组
                'tips' => '在Form为编辑状态时，不可编辑',
                'after-text' => '勾选编辑只读',
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
                'box-href' => Route::url('~/tool_field/default_set'),
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
                'data-url' => Route::url('~/tool_field/load_plugin'),
                'data-bind-param' => 'type',
                'data-auto-load' => true,
                'view-tab-index' => 'extend',
            ],

            'custom-line' => [
                'label' => '自定义扩展属性',
                'type' => 'line',
                'view-tab-index' => 'extend',
            ],
            'customAttrs' => [
                'label' => '自定义属性 ',
                'type' => 'plugin',
                'plug-name' => 'CustomPlugin',
                'plug-type' => 3,
                'plug-mode' => 'composite',
                'view-tab-index' => 'extend',
                'tips' => '控件的内联样式',
            ],

            'value-line' => [
                'label' => '值处理函数',
                'type' => 'line',
                'view-tab-index' => 'extend',
            ],

            'valueFunc' => [
                'label' => '处理值的函数 ',
                'type' => 'text',
                'data-val' => ['regex' => '^\w+(\\\\\w+)*::\w+$'],
                'data-val-msg' => ['regex' => '格式不正确。'],
                'view-tab-index' => 'extend',
                'tips' => '如果值需要后期加工，可以设置加工的PHP函数，完整的静态函数名，如 libs\MyClass::myFunc',
            ],

            'dynamic-line' => [
                'label' => '动态呈现',
                'type' => 'line',
                'view-tab-index' => 'view',
                'tips' => '在数据库中的字段类型',
            ],

            'dynamic' => [
                'label' => '动态呈现控制 [dynamic]',
                'type' => 'textarea',
                'box-yee-module' => 'tooldynamic',
                'box-yee-depend' => '/tool/js/tooldynamic.js',
                'view-tab-index' => 'view',
            ],
            'box-line' => [
                'label' => 'Input 样式及属性',
                'type' => 'line',
                'view-tab-index' => 'view',
            ],
            'boxPlaceholder' => [
                'label' => '输入框内提示文本 [box-placeholder]',
                'type' => 'text',
                'tips' => '直接在输入框内的提示文本(placeholder)',
                'view-tab-index' => 'view',
            ],
            'boxClass' => [
                'label' => '控件CSS样式名称 [box-class]',
                'type' => 'text',
                'tips' => '默认系统会指定为 "form-inp 控件类型"',
                'view-tab-index' => 'view',
            ],
            'boxStyle' => [
                'label' => '内联style样式 [box-style]',
                'type' => 'textarea',
                'tips' => '控件的内联样式',
                'view-tab-index' => 'view',
            ],
            'boxAttrs' => [
                'label' => '其他属性 [box-*]',
                'type' => 'plugin',
                'plug-name' => 'InpAttributePlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'view-tab-index' => 'view',
                'tips' => '控件的内联样式',
            ],

            'tips' => [
                'label' => '提示信息 [tips]',
                'type' => 'textarea',
                'view-tab-index' => 'view',
            ],
            'isEditTips' => [
                'label' => '编辑修正',
                'type' => 'check',
                'view-tab-index' => 'view',
                'view-merge' => -1,
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'editTips',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'editTips',
                    ],
                ],
            ],
            'editTips' => [
                'label' => '提示信息(编辑)',
                'type' => 'textarea',
                'view-tab-index' => 'view',
            ],
            'tpl-line' => [
                'label' => '自定义模板',
                'type' => 'line',
                'view-tab-index' => 'view',
            ],
            'viewTplName' => [
                'label' => '排版样式 [view-tpl-name]',
                'type' => 'select',
                'header' => '请选择排版样式皮肤',
                'tips' => '指定控件皮肤',
                'options' => [['default', '默认(横向排版)', 'default'], ['editor', '编辑器（纵向排版）', 'editor']],
                'view-tab-index' => 'view',
            ],
            'viewTplCode' => [
                'label' => '自定义控件代码 [view-tpl-code]',
                'type' => 'textarea',
                'tips' => '自定义控件的片段，Sdopx形式,参考控件皮肤文件。仅工具支持直接书写模板 ',
                'view-tab-index' => 'view',
                'box-style' => "width: 500px;height:150px;",
            ],

            'viewAsterisk' => [
                'label' => '标注星号(*) [view-asterisk]',
                'type' => 'check',
                'tips' => '在标题后面打上一个红色星号',
                'view-tab-index' => 'valid',
            ],
            'dataVal' => [
                'label' => '验证配置 [data-val]',
                'type' => 'textarea',
                'tips' => '验证规则配置',
                'view-tab-index' => 'valid',
                'box-yee-module' => 'validtor',
                'data-bind' => '#dataValMsg',
                'box-yee-depend' => '/tool/js/validtor.js',
            ],
            'dataValMsg' => [
                'label' => '错误提示  [data-val-msg]',
                'type' => 'textarea',
                'tips' => '错误提示信息',
                'view-tab-index' => 'valid',
                'box-yee-module' => 'validmsg',
                'box-yee-depend' => '/tool/js/validtor.js',
            ],

            'isEditDataVal' => [
                'label' => '编辑修正',
                'type' => 'check',
                'view-tab-index' => 'valid',
                'view-merge' => -1,
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'editDataVal,editDataValMsg',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'editDataVal,editDataValMsg',
                    ],
                ],
            ],
            'editDataVal' => [
                'label' => '验证配置(编辑)',
                'type' => 'textarea',
                'tips' => '验证规则配置',
                'view-tab-index' => 'valid',
                'box-yee-module' => 'validtor',
                'box-yee-depend' => '/tool/js/validtor.js',
            ],

            'editDataValMsg' => [
                'label' => '错误提示(编辑)',
                'type' => 'textarea',
                'tips' => '错误提示信息',
                'view-tab-index' => 'valid',
                'box-yee-module' => 'validmsg',
                'data-bind' => '#editDataVal',
                'box-yee-depend' => '/tool/js/validtor.js',
            ],

            'dataValInfo' => [
                'label' => '默认提示内容',
                'type' => 'textarea',
                'tips' => '验证的默认提示内容',
                'view-tab-index' => 'valid',
            ],
            'dataValValid' => [
                'label' => '正确提示内容',
                'type' => 'textarea',
                'tips' => '验证的默认提示内容',
                'view-tab-index' => 'valid',
            ],
            'dataValFor' => [
                'label' => '呈现内容的标签ID',
                'type' => 'text',
                'tips' => '用于呈现正确或者错误信息的HTML标签，如：#test-validation 或者 .test-validation',
                'view-tab-index' => 'valid',
            ],
            'dataValOff' => [
                'label' => '关闭验证',
                'type' => 'check',
                'after-text' => '关闭认证后，前后台不再验证数据，如需要开启可在JS和PHP控制器代码中取消',
                'view-tab-index' => 'valid',
            ],
            'dataValEvents' => [
                'label' => '触发验证',
                'type' => 'text',
                'tips' => '如 blur，当离开后立即验证数据，如果有多个触发事件 可用 , 号隔开',
                'view-tab-index' => 'valid',
            ],
            'validFunc' => [
                'label' => '验证数值的函数 ',
                'type' => 'text',
                'data-val' => ['regex' => '^\w+(\\\\\w+)*::\w+$'],
                'data-val-msg' => ['regex' => '格式不正确。'],
                'view-tab-index' => 'extend',
                'tips' => '如果值需要后期验证，可以设置验证的PHP函数，完整的静态函数名，如 libs\MyClass::myFunc',
            ],
            'formId' => [
                'label' => '表单ID',
                'type' => 'hidden',
                'hideBox' => true,
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '表单ID丢失'],
                'default' => Request::instance()->param('formId:i', 0),
                'view-tab-index' => 'base',
            ],
        ];
    }

}