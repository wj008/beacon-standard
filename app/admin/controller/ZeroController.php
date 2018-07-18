<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/23
 * Time: 18:23
 */

namespace app\admin\controller;

use beacon\DB;
use beacon\Form;

abstract class ZeroController extends AdminController
{
    protected $zero = [];

    public function initialize()
    {
        $this->zero = $this->zeroLoad();
        if (isset($this->zero['actionForm'])) {
            $actionForm = preg_replace_callback('@\\\\zero\\\\form\\\\Zero(.*)$@', function ($m) {
                return '\\form\\' . $m[1];
            }, $this->zero['actionForm']);
            if (class_exists($actionForm)) {
                $this->zero['actionForm'] = $actionForm;
            }
        }
        parent::initialize();
    }

    protected function indexAction()
    {
        if ($this->isAjax()) {
            $selector = $this->zeroSelector();
            if (isset($this->zero['usePageList']) && $this->zero['usePageList']) {
                $pageSize = $this->get('pageSize:i', 0);
                if ($pageSize < 1) {
                    $pageSize = isset($this->zero['pageSize']) ? intval($this->zero['pageSize']) : 20;
                }
                $plist = $selector->getPageList($pageSize);
                if (method_exists($this, 'zeroCountBySqlTemplate')) {
                    $plist->setCount($this->zeroCountBySqlTemplate());
                }
                $this->assign('pdata', $plist->getInfo());
                $this->assign('list', $plist->getList());
            } else {
                $this->assign('list', $selector->getList());
                $this->assign('pdata', ['recordsCount' => $selector->getCount()]);
            }
            $data = $this->getAssign();
            $listOrgFields = isset($this->zero['listOrgFields']) ? $this->zero['listOrgFields'] : [];
            $data['list'] = $this->hackData($this->zero['templateHack'], $data['list'], $listOrgFields);
            $this->success('获取数据成功', $data);
        }
        if (isset($this->zero['searchForm'])) {
            $this->assign('search', Form::instance($this->zero['searchForm']));
        }
        $this->display($this->zero['template']);
    }

    protected function addAction()
    {
        $form = Form::instance($this->zero['actionForm'], 'add');
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

    protected function editAction()
    {
        $id = $this->param('id:i');
        $form = Form::instance($this->zero['actionForm'], 'edit');
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

    protected function deleteAction()
    {
        $id = $this->param('id:i');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $form = Form::instance($this->zero['actionForm'], 'delete');
        $form->delete($id);
        $this->success('删除' . $form->title . '成功');
    }

    protected function deleteSelectAction()
    {
        $ids = $this->param('sel_id:a', []);
        $form = Form::instance($this->zero['actionForm'], 'delete');
        foreach ($ids as $id) {
            $form->delete($id);
        }
        $this->success('删除' . $form->title . '成功');
    }

    protected function allowSelectAction()
    {
        $ids = $this->param('sel_id:a', []);
        $form = Form::instance($this->zero['actionForm'], 'delete');
        foreach ($ids as $id) {
            DB::update($this->zero['tbname'], ['allow' => 1], $id);
        }
        $this->success('设置审核' . $form->title . '成功');
    }

    protected function unAllowSelectAction()
    {
        $ids = $this->param('sel_id:a', []);
        $form = Form::instance($this->zero['actionForm'], 'delete');
        foreach ($ids as $id) {
            DB::update($this->zero['tbname'], ['allow' => 0], $id);
        }
        $this->success('撤销审核' . $form->title . '成功');
    }

    protected function sortAction()
    {
        $id = $this->param('id:i');
        $sort = $this->param('sort:i');
        $row = DB::getRow('select id from ' . $this->zero['tbname'] . ' where id=?', $id);
        if ($row == null) {
            $this->error('不存在的数据');
        }
        DB::update($this->zero['tbname'], ['sort' => $sort], $id);
        $this->success('更新排序成功');
    }

    protected function changeAllowAction()
    {
        $id = $this->param('id:i');
        $row = DB::getRow('select id,allow from ' . $this->zero['tbname'] . ' where id=?', $id);
        if ($row == null) {
            $this->error('不存在的数据');
        }
        $allow = (intval($row['allow']) == 1 ? 0 : 1);
        DB::update($this->zero['tbname'], ['allow' => $allow], $id);
        if ($allow == 1) {
            $this->success('设置审核成功');
        }
        $this->success('撤销审核成功');
    }

}