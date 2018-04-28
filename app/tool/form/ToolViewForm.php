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

class ToolViewForm extends Form
{
    public $title = '内页详情';
    public $caption = '工具-内页详情';
    public $viewUseTab = false;
    public $useAjax = true;
    public $tbname = '@pf_tool_view';
    public $viewDescription = '注意：为了防止模板与脚本等冲突，除了SQL模板以外，其他输入框中如使用到模板渲染的，模板引擎分界符 为 `{@` 和 `@}`';

    protected function load()
    {
        return [
            'title' => [
                'label' => '内页名称',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '请输入内页名称'],
            ],
            'caption' => [
                'label' => '内页标题',
            ],
            'action' => [
                'label' => '动作方法',
                'data-val' => ['r' => true, 'regex' => '^[a-z][A-Za-z0-9]+$', 'remote' => [Route::url('~/ToolView/check'), 'Post', 'id,listId']],
                'data-val-msg' => ['r' => '没有填写模型关键字！', 'regex' => '动作方法只能使用大写字母开头的数字及字母组合。', 'remote' => '标识已经使用，请更换其他标识'],
                'tips' => '创建后不可更改，并具有唯一性，与文档的模板相关连，建议由英文、数字组成，因为部份Unix系统无法识别中文文件',
                'offedit' => true,
                'remote-func' => function ($value) {
                    $id = Request::instance()->param('id:i', 0);
                    $listId = Request::instance()->param('listId:i', 0);
                    $row = DB::getRow('select id from @pf_tool_view where `action`=? and id<>? and listId=?', [$value, $id, $listId]);
                    if ($row) {
                        return false;
                    }
                    return true;
                },
                'view-tab-index' => 'base',
            ],
            'baseLayout' => [
                'label' => '皮肤文件Layout',
                'view-tab-index' => 'base',
                'default' => 'layoutDetail.tpl',
                'tips' => '父页面皮肤文件，如果为空 默认使用系统皮肤文件 layoutDetail.tpl',
            ],
            'listId' => [
                'label' => '列表ID',
                'type' => 'hide',
                'default' => function () {
                    $id = Request::instance()->get('id:i', 0);
                    if ($id != 0) {
                        $row = DB::getRow('select listId from @pf_tool_view where id=?', $id);
                        if ($row) {
                            return $row['listId'];
                        }
                    }
                    return Request::instance()->get('listId:i', 0);
                }
            ],
        ];
    }

    protected function loadEdit()
    {
        $this->addHideBox('id', Request::instance()->get('id:i', 0));
        return [
            'action' => [
                'data-val' => ['remote' => [Route::url('~/ToolView/check'), 'post', 'id,listId']]
            ]
        ];
    }
}