<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/11
 * Time: 15:14
 */

namespace app\tool\form;


use beacon\DB;
use beacon\Form;
use beacon\Request;
use beacon\Route;

class ToolApplicationForm extends Form
{
    public $title = '项目管理';
    public $caption = '工具-项目管理';
    public $useAjax = true;

    protected function load()
    {
        $load = [
            'name' => [
                'label' => '项目名称',
                'data-val' => ['r' => true, 'remote' => [Route::url('~/ToolApplication/checkName'), 'post']],
                'data-val-msg' => ['r' => '请输入账号名称'],
                'data-val-events' => 'blur',
                'tips' => '请输入项目名称',
                'box-class' => 'form-inp text',
                'remote-func' => function ($value) {
                    $id = Request::instance()->param('id:i', 0);
                    $row = DB::getRow('select id from @pf_tool_application where `name`=? and id<>?', [$value, $id]);
                    if ($row) {
                        return false;
                    }
                    return true;
                },
            ],
            'namespace' => [
                'label' => '命名空间',
                'data-val' => ['r' => true, 'regex' => '^[a-z0-9]+(\\\\[a-z0-9]+)*$',],
                'data-val-msg' => ['r' => '请输入命名空间', 'regex' => '命名空间格式不正确'],
                'tips' => '请输入项目命名空间',
            ],
            'module' => [
                'label' => '路由模块名',
                'data-val' => ['r' => true, 'regex' => '^[a-zA-Z][A-Za-z0-9_]*$'],
                'data-val-msg' => ['r' => '没有填写路由模块名！', 'regex' => '路由模块名只能使用字母开头的数字及字母组合。'],
                'tips' => '如果模块名称不正确，则无法测试列表',
                'box-style' => 'width:120px;',
                'view-tab-index' => 'base',
            ],
        ];
        if ($this->isEdit()) {
            $load['name']['data-val']['remote'] = [Route::url('~/ToolApplication/checkName'), 'post', 'id'];
            $this->addHideBox('id', Request::instance()->get('id:i', 0));
        }
        return $load;
    }
}