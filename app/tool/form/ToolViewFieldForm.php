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

class ToolViewFieldForm extends Form
{
    public $title = '内页详情字段';
    public $caption = '工具-内页字段';
    public $viewUseTab = false;
    public $useAjax = true;
    public $tbname = '@pf_tool_view_field';
    public $viewDescription = '注意：为了防止模板与脚本等冲突，除了SQL模板以外，其他输入框中如使用到模板渲染的，模板引擎分界符 为 `{@` 和 `@}`';

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
            'type' => [
                'label' => '字段类型 [type]',
                'view-tab-index' => 'base',
                'box-style' => 'width:170px;display:inline-block;',
                'type' => 'radiogroup', // 单选组
                'options' => [['label', '文本'], ['line', '分割行']], // 单选组的选项值
                'default' => 'label',
            ],

            'sort' => [
                'label' => '排序', // 字段标题
                'type' => 'integer',
                'view-tab-index' => 'base',
                'default' => function () {
                    $viewId = Request::instance()->get('viewId:i', 0);
                    return DB::getMax($this->tbname, 'sort', 'viewId=?', $viewId) + 10;
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
            ],
            'value' => [
                'label' => '值',
                'type' => 'textarea',
                'box-style' => 'height:100px;width:480px',
                'tips' => '使用模板',
            ],
            'viewId' => [
                'label' => '列表ID',
                'type' => 'hide',
                'default' => function () {
                    $id = Request::instance()->get('id:i', 0);
                    if ($id != 0) {
                        $row = DB::getRow('select viewId from @pf_tool_view_field where id=?', $id);
                        if ($row) {
                            return $row['viewId'];
                        }
                    }
                    return Request::instance()->get('viewId:i', 0);
                }
            ],
        ];
    }

    protected function loadEdit()
    {
        $this->addHideBox('id', Request::instance()->get('id:i', 0));
        return [];
    }
}