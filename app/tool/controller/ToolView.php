<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/12
 * Time: 20:46
 */

namespace app\tool\controller;


use app\tool\form\ToolSearchForm;
use app\tool\form\ToolViewForm;
use app\tool\libs\MakeController;
use beacon\DB;
use beacon\Form;
use beacon\Route;
use beacon\SqlSelector;
use beacon\Utils;

class ToolView extends ToolController
{
    private $listId = 0;

    private function loadListId()
    {
        $this->listId = $this->param('listId:i', 0);
        if ($this->listId == 0) {
            $this->error('缺少参数', null, Route::url('~/tool_form'));
        }
        $this->assign('listId', $this->listId);
    }

    public function indexAction()
    {
        $this->loadListId();
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_tool_view');
            $selector->where('listId=?', $this->listId);
            $name = $this->get('title', '');
            if ($name) {
                $selector->where("`title` LIKE CONCAT('%',?,'%')", $name);
            }
            $selector->order('id asc');
            $list = $selector->getList();
            $this->assign('list', $list);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('view.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('view.tpl');
    }


    public function addAction()
    {
        $form = new ToolViewForm('add');
        if ($this->isGet()) {
            $this->displayForm($form);
        }
        if ($this->isPost()) {
            $form->autoComplete();
            $error = [];
            if (!$form->validation($error)) {
                $this->error($error);
            }
            $form->insert();
            $this->success('添加' . $form->title . '成功');
        }
    }

    public function editAction()
    {
        $id = $this->param('id:i');
        $form = new ToolViewForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $row = $form->getRow($id);
        $form->initValues($row);
        if ($this->isGet()) {
            $this->displayForm($form);
        }
        if ($this->isPost()) {
            $form->autoComplete();
            $error = [];
            if (!$form->validation($error)) {
                $this->error($error);
            }
            $form->update($id);
            $this->success('编辑' . $form->title . '成功');
        }
    }


}