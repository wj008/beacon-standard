<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/12
 * Time: 20:46
 */

namespace app\tool\controller;


use app\tool\form\ToolSearchForm;
use app\tool\libs\MakeController;
use beacon\DB;
use beacon\Form;
use beacon\Route;
use beacon\SqlSelector;
use beacon\Utils;

class ToolSearch extends ToolController
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
            $selector = new SqlSelector('@pf_tool_search');
            $selector->where('listId=?', $this->listId);
            $name = $this->get('name', '');
            if ($name) {
                $selector->where("(`name` LIKE CONCAT('%',?,'%') or `label` LIKE CONCAT('%',?,'%'))", [$name, $name]);
            }
            $viewTabIndex = $this->get('viewTabIndex', '');
            $selector->search('viewTabIndex=?', $viewTabIndex);

            $sort = $this->get('sort', 'sort_asc');
            switch ($sort) {
                case 'id_asc':
                    $selector->order('id asc');
                    break;
                case 'id_desc':
                    $selector->order('id desc');
                    break;
                case 'sort_asc':
                    $selector->order('sort asc');
                    break;
                case 'sort_desc':
                    $selector->order('sort desc');
                    break;
                default:
                    $selector->order('sort asc');
                    break;
            }
            $plist = $selector->getPageList();
            $pdata = $plist->getInfo();
            $list = $plist->getList();
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('search.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('search.tpl');
    }

    public function addAction(int $copyid = 0)
    {
        $this->loadListId();
        $form = new ToolSearchForm('add');

        if ($this->isGet()) {
            if ($copyid > 0) {
                $row = DB::getRow('select * from @pf_tool_search where id=?', $copyid);
                if ($row == null) {
                    $this->error('不存在的数据');
                }
                $form->initValues($row);
            }
            $this->displayForm($form);
            return;
        }

        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            $vals['listId'] = $this->listId;
            $type = $vals['type'];
            $vals['names'] = [];
            $typeClass = Route::getNamespace() . '\\module\\' . Utils::toCamel($type) . 'Module';
            $xform = Form::instance($typeClass);
            if ($xform != null) {
                if ($type == 'linkage') {
                    $xform->getField('dataValGroup')->close = true;
                    $xform->getField('isEditDataValGroup')->close = true;
                    $xform->getField('editDataValGroup')->close = true;
                }
                $tvals = $xform->autoComplete();
                $vals['extendAttrs'] = $tvals;
                if (isset($tvals['names'])) {
                    $vals['names'] = json_decode($tvals['names'], true);
                }
            }
            $id = DB::insert('@pf_tool_search', $vals);
            MakeController::make($this->listId);
            $this->success('添加' . $form->title . '成功', $vals);
        }
    }

    public function editAction(int $id = 0)
    {
        $form = new ToolSearchForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $row = DB::getRow('select * from @pf_tool_search where id=?', $id);
        if ($row == null) {
            $this->error('不存在的数据');
        }
        $this->listId = $row['listId'];
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
            $type = $vals['type'];
            $vals['names'] = [];
            if ($type != 'hidden') {
                $vals['hideBox'] = false;
            }
            $typeClass = Route::getNamespace() . '\\module\\' . Utils::toCamel($type) . 'Module';
            $xform = Form::instance($typeClass);
            if ($xform != null) {
                if ($type == 'linkage') {
                    $xform->getField('dataValGroup')->close = true;
                    $xform->getField('isEditDataValGroup')->close = true;
                    $xform->getField('editDataValGroup')->close = true;
                }
                $tvals = $xform->autoComplete();
                $vals['extendAttrs'] = $tvals;
                if (isset($tvals['names'])) {
                    $vals['names'] = json_decode($tvals['names'], true);
                }
            }
            DB::update('@pf_tool_search', $vals, $id);
            MakeController::make($this->listId);
            $this->success('编辑' . $form->title . '成功');
        }
    }

    public function copy($id)
    {
        $vals = DB::getRow('select * from @pf_tool_search where id=?', $id);
        if ($vals == null) {
            $this->error('不存在的数据');
        }
        unset($vals['id']);
        $vals['sort'] = intval(DB::getMax('@pf_tool_search', 'sort', 'listId=?', $this->listId)) + 10;
        $vals['listId'] = $this->listId;
        DB::insert('@pf_tool_search', $vals);
    }

    public function copyAction($cptype = '', array $fids = [])
    {
        $this->loadListId();
        if ($cptype !== 'search_field') {
            $this->error('字段拷贝失败');
        }
        if (empty($fids)) {
            $this->error('字段拷贝失败');
        }
        foreach ($fids as $id) {
            $this->copy($id);
        }
        MakeController::make($this->listId);
        $this->success('字段拷贝成功');

    }

    public function editSortAction(int $id = 0, $sort = 0)
    {
        $row = DB::getRow('select * from @pf_tool_search where id=?', $id);
        if ($row == null) {
            $this->error('不存在的数据');
        }
        $this->listId = $row['listId'];
        DB::update('@pf_tool_search', ['sort' => $sort], $id);
        MakeController::make($this->listId);
        $this->success('更新排序成功');
    }

    private function delete($id = 0)
    {
        if ($id == 0) {
            return;
        }
        $row = DB::getRow('select * from @pf_tool_search where id=?', $id);
        $this->listId = $row['listId'];
        DB::delete('@pf_tool_search', $id);
    }

    public function delAction(int $id = 0)
    {
        if ($id == 0) {
            $this->error('参数有误');
        }
        $this->delete($id);
        MakeController::make($this->listId);
        $this->success('删除字段成功');
    }

    public function delSelectAction(string $sel_id = '')
    {
        $fids = explode(',', $sel_id);
        foreach ($fids as $id) {
            $this->delete($id);
        }
        MakeController::make($this->listId);
        $this->success('删除选中字段成功');
    }

    public function loadPluginAction($id = 0, $type = 'text')
    {
        $typeClass = Route::getNamespace() . '\\module\\' . Utils::toCamel($type) . 'Module';
        $form = Form::instance($typeClass);
        if ($form == null) {
            $this->success('', null);
        }
        if ($type == 'linkage') {
            $form->getField('dataValGroup')->close = true;
            $form->getField('isEditDataValGroup')->close = true;
            $form->getField('editDataValGroup')->close = true;
        }
        $vals = $form->autoComplete();
        $this->assign('form', $form);
        $code = $this->fetch('common/ajaxform.tpl');
        $this->success('', $code);
    }

    public function selectFieldAction()
    {
        $this->loadListId();
        $lrow = DB::getRow('select * from @pf_tool_list where id=?', $this->listId);
        if ($lrow == null) {
            $this->error('不存在的数据');
        }
        $formId = $lrow['formId'];
        $this->assign('formId', $formId);
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_tool_field');
            $selector->where('formId=?', $formId);
            $name = $this->get('name', '');
            if ($name) {
                $selector->where("`name` LIKE CONCAT('%',?,'%')", $name);
            }
            $sort = $this->get('sort', 'sort_asc');
            switch ($sort) {
                case 'id_asc':
                    $selector->order('id asc');
                    break;
                case 'id_desc':
                    $selector->order('id desc');
                    break;
                case 'sort_asc':
                    $selector->order('sort asc');
                    break;
                case 'sort_desc':
                    $selector->order('sort desc');
                    break;
                default:
                    $selector->order('sort asc');
                    break;
            }
            $plist = $selector->getPageList();
            $pdata = $plist->getInfo();
            $list = $plist->getList();
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('field.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('select_field.tpl');
    }

    public function copySelectAction($sel_id = '')
    {
        $this->loadListId();
        $ids = explode(',', $sel_id);
        foreach ($ids as $id) {
            $field = DB::getRow('select * from @pf_tool_field where id=?', $id);
            if ($field == null) {
                $this->error('不存在的数据');
            }
            $vals = [];
            foreach (['name', 'label', 'type', 'hideBox',
                         'beforeText', 'afterText', 'viewMerge', 'default', 'forceDefault',
                         'extendAttrs', 'customAttrs', 'boxPlaceholder'
                         , 'boxClass'
                         , 'boxStyle'
                         , 'boxAttrs'
                         , 'names'
                     ] as $key) {
                $vals[$key] = $field[$key];
            }
            $vals['viewTabIndex'] = 'base';
            $vals['varType'] = 'string';
            if ($field['dbtype'] == 'int') {
                $vals['varType'] = 'int';
            } elseif ($field['dbtype'] == 'decimal' || $field['dbtype'] == 'double' || $field['dbtype'] == 'float') {
                $vals['varType'] = 'float';
            } elseif ($field['dbtype'] == 'tinyint') {
                $vals['varType'] = 'bool';
            }
            if ($vals['varType'] == 'string') {
                $vals['tbWhere'] = "`{$vals['name']}` like concat('%',?,'%')";
                $vals['tbWhereType'] = 2;
            } else {
                $vals['tbWhere'] = "`{$vals['name']}` = ?";
                $vals['tbWhereType'] = 2;
            }
            $vals['varType'] = 'string';
            $vals['sort'] = intval(DB::getMax('@pf_tool_search', 'sort', 'listId=?', $this->listId)) + 10;
            $vals['listId'] = $this->listId;
            DB::insert('@pf_tool_search', $vals);
        }
        MakeController::make($this->listId);
        $this->success('拷贝成功');
    }

    public function changetabAction(int $id = 0)
    {
        $row = DB::getRow('select * from @pf_tool_search where id=?', $id);
        if ($row == null) {
            $this->error('不存在的数据');
        }
        if ($row['viewTabIndex'] == 'base') {
            DB::update('@pf_tool_search', ['viewTabIndex' => 'senior'], $id);
        } else {
            DB::update('@pf_tool_search', ['viewTabIndex' => 'base'], $id);
        }
        $this->listId = $row['listId'];
        MakeController::make($this->listId);
        $this->success('设置成功');

    }

}