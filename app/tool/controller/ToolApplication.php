<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/11
 * Time: 14:59
 */

namespace app\tool\controller;


use app\tool\form\ToolApplicationForm;
use app\tool\libs\MakeController;
use app\tool\libs\MakeForm;
use beacon\Console;
use beacon\DB;
use beacon\SqlSelector;

class ToolApplication extends ToolController
{
    public function indexAction()
    {
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_tool_application');
            $name = $this->get('name', '');
            if ($name) {
                $selector->where("`name` LIKE CONCAT('%',?,'%')", [$name]);
            }
            $sort = $this->get('sort', 'id_asc');
            switch ($sort) {
                case 'id_asc':
                    $selector->order('id asc');
                    break;
                case 'id_desc':
                    $selector->order('id desc');
                    break;
                case 'name_asc':
                    $selector->order('name asc');
                    break;
                case 'name_desc':
                    $selector->order('name desc');
                    break;
                default:
                    $selector->order('id asc');
                    break;
            }
            $plist = $selector->getPageList();
            $pdata = $plist->getInfo();
            $list = $plist->getList();
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('application.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('application.tpl');
    }

    public function dialogAction()
    {
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_tool_application');
            $name = $this->get('name', '');
            if ($name) {
                $selector->where("`name` LIKE CONCAT('%',?,'%')", [$name]);
            }
            $sort = $this->get('sort', 'id_asc');
            switch ($sort) {
                case 'id_asc':
                    $selector->order('id asc');
                    break;
                case 'id_desc':
                    $selector->order('id desc');
                    break;
                case 'name_asc':
                    $selector->order('name asc');
                    break;
                case 'name_desc':
                    $selector->order('name desc');
                    break;
                default:
                    $selector->order('id asc');
                    break;
            }
            $plist = $selector->getPageList();
            $pdata = $plist->getInfo();
            $list = $plist->getList();
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('application.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('application_dlg.tpl');
    }

    public function checkNameAction()
    {
        $form = new ToolApplicationForm();
        $username = $this->param('name', '');
        $field = $form->getField('name');
        if ($field && $field->remoteFunc) {
            if (($field->remoteFunc)($username)) {
                $this->success('项目名可以使用');
            }
        }
        $this->error('项目名已经存在');
    }

    public function addAction()
    {
        $form = new ToolApplicationForm('add');
        if ($this->isGet()) {
            $this->displayForm($form);
            return;
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            DB::insert('@pf_tool_application', $vals);
            $this->success('添加' . $form->title . '成功');
        }
    }

    public function editAction(int $id = 0)
    {
        $form = new ToolApplicationForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $row = DB::getRow('select * from @pf_tool_application where id=?', $id);
        $form->initValues($row);
        if ($this->isGet()) {
            $this->displayForm($form);
            return;
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            DB::update('@pf_tool_application', $vals, $id);
            $this->success('编辑' . $form->title . '成功');
        }
    }

    public function delAction(int $id = 0)
    {
        if ($id == 0) {
            $this->error('参数有误');
        }
        DB::delete('@pf_tool_application', $id);
        $this->success('删除账号成功');
    }

    public function makeAction(int $id = 0)
    {
        //生成模型
        $flist = DB::getList('select id from @pf_tool_form where proId=?', $id);
        foreach ($flist as $item) {
            MakeForm::make($item['id']);
        }
        $llist = DB::getList('select id from @pf_tool_list where proId=?', $id);
        foreach ($llist as $item) {
            MakeController::make($item['id']);
        }
        $this->success('生成成功');
    }

}