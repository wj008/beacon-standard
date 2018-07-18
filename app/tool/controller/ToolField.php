<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/12
 * Time: 20:46
 */

namespace app\tool\controller;

use app\tool\form\ToolFieldForm;
use app\tool\libs\MakeForm;
use beacon\DB;
use beacon\Form;
use beacon\Route;
use beacon\SqlSelector;
use beacon\Utils;

class ToolField extends ToolController
{
    private $formId = 0;

    private function loadFormId()
    {
        $this->formId = $this->param('formId:i', 0);
        if ($this->formId == 0) {
            $this->error('缺少参数', null, Route::url('~/tool_form'));
        }
        $this->assign('formId', $this->formId);
    }

    public function indexAction()
    {
        $this->loadFormId();
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_tool_field');
            $selector->where('formId=?', $this->formId);
            $name = $this->get('name', '');
            if ($name) {
                $selector->where("(`name` LIKE CONCAT('%',?,'%') or `label` LIKE CONCAT('%',?,'%'))", [$name, $name]);
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
            $pdata = ['recordsCount' => $selector->getCount()];
            $list = $selector->getList();
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('field.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('field.tpl');
    }

    public function checkNameAction()
    {
        $form = new ToolFieldForm();
        $username = $this->param('name', '');
        if (($form->getField('name')->remoteFunc)($username)) {
            $this->success('字段名称可以使用');
        }
        $this->error('字段名称已经存在');
    }

    public function addAction(int $copyid = 0)
    {
        $this->loadFormId();
        $form = new ToolFieldForm('add');
        $frow = DB::getRow('select tbName,tbCreate,viewUseTab,viewTabs from @pf_tool_form where id=?', $this->formId);
        if ($frow == null) {
            $this->error('添加失败,表单不存在');
        }
        if ($frow['viewUseTab']) {
            if (!empty($frow['viewTabs']) && Utils::isJsonString($frow['viewTabs'])) {
                $temp = json_decode($frow['viewTabs'], 1);
                $options = [];
                foreach ($temp as $item) {
                    $options[] = [$item['key'], $item['value'] . '(' . $item['key'] . ')'];
                }
                $form->addField('viewTabIndex', ['label' => '选择所属Tab [view-tab-index]', 'type' => 'select', 'options' => $options, 'tips' => '选择所属TAB标签', 'view-tab-index' => 'base'], 'sort');
            }
        }

        if ($this->isGet()) {
            if ($copyid > 0) {
                $row = DB::getRow('select * from @pf_tool_field where id=?', $copyid);
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
            $vals['formId'] = $this->formId;
            $type = $vals['type'];
            $vals['names'] = [];
            $typeClass = Route::getNamespace() . '\\module\\' . Utils::toCamel($type) . 'Module';
            $xform = Form::instance($typeClass);
            if ($xform != null) {
                $tvals = $xform->autoComplete();
                $vals['extendAttrs'] = $tvals;
                if (isset($tvals['names'])) {
                    $vals['names'] = json_decode($tvals['names'], true);
                }
            }
            $tbname = '@pf_' . $frow['tbName'];
            try {
                DB::beginTransaction();
                if ($frow['tbCreate'] == 1) {
                    if ($vals['dbtype'] != 'null' && $vals['dbfield'] == 1) {
                        if (DB::existsField($tbname, $vals['name'])) {
                            DB::rollBack();
                            $this->error(['name' => '添加失败,字段名已经存在']);
                        }
                        DB::addField($tbname, $vals['name'], [
                            'type' => $vals['dbtype'],
                            'len' => $vals['dblen'],
                            'scale' => $vals['dbpoint'],
                            'comment' => empty($vals['dbcomment']) ? $vals['label'] : $vals['dbcomment'],
                        ]);
                    }
                    if (!empty($vals['names'])) {
                        foreach ($vals['names'] as $item) {
                            $option = [
                                'type' => $vals['dbtype'],
                                'len' => 11,
                                'scale' => 0,
                                'comment' => empty($vals['dbcomment']) ? $vals['label'] : $vals['dbcomment'],
                            ];
                            if (!isset($item['type'])) {
                                $item['type'] = 'bool';
                            }
                            if ($item['type'] == 'int') {
                                $option['type'] = 'int';
                                $option['len'] = 11;
                            } elseif ($item['type'] == 'bool') {
                                $option['type'] = 'tinyint';
                                $option['len'] = 1;
                            } else {
                                $option['type'] = 'varchar';
                                $option['len'] = 250;
                            }
                            DB::addField($tbname, $item['field'], $option);
                        }
                    }

                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->error('创建字段失败');
            }
            DB::insert('@pf_tool_field', $vals);
            MakeForm::make($this->formId);
            $this->success('添加' . $form->title . '成功', $vals);
        }
    }

    public function editAction(int $id = 0)
    {
        $form = new ToolFieldForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $row = DB::getRow('select * from @pf_tool_field where id=?', $id);
        if ($row == null) {
            $this->error('不存在的数据');
        }
        $this->formId = $row['formId'];
        $frow = DB::getRow('select tbName,tbCreate,viewUseTab,viewTabs from @pf_tool_form where id=?', $this->formId);
        if ($frow == null) {
            $this->error('添加失败,表单不存在');
        }
        if ($frow['viewUseTab']) {
            if (!empty($frow['viewTabs']) && Utils::isJsonString($frow['viewTabs'])) {
                $temp = json_decode($frow['viewTabs'], 1);
                $options = [];
                foreach ($temp as $item) {
                    $options[] = [$item['key'], $item['value'] . '(' . $item['key'] . ')'];
                }
                $form->addField('viewTabIndex', ['label' => '选择所属Tab [view-tab-index]', 'type' => 'select', 'options' => $options, 'tips' => '选择所属TAB标签', 'view-tab-index' => 'base'], 'sort');
            }
        }
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
                $tvals = $xform->autoComplete();
                $vals['extendAttrs'] = $tvals;
                if (isset($tvals['names'])) {
                    $vals['names'] = json_decode($tvals['names'], true);
                }
            }
            $tbname = '@pf_' . $frow['tbName'];
            try {
                DB::beginTransaction();
                if ($frow['tbCreate'] == 1) {
                    if ($vals['dbtype'] != 'null' && $vals['dbfield'] == 1) {
                        if ($row['name'] != $vals['name']) {
                            if (DB::existsField($tbname, $vals['name'])) {
                                DB::rollBack();
                                $this->error(['name' => '添加失败,字段名已经存在']);
                            }
                        }
                        DB::updateField($tbname, $row['name'], $vals['name'], [
                            'type' => $vals['dbtype'],
                            'len' => $vals['dblen'],
                            'scale' => $vals['dbpoint'],
                            'comment' => empty($vals['dbcomment']) ? $vals['label'] : $vals['dbcomment'],
                        ]);
                    }
                    if (!empty($vals['names'])) {
                        foreach ($vals['names'] as $item) {
                            $option = [
                                'type' => $vals['dbtype'],
                                'len' => 11,
                                'scale' => 0,
                                'comment' => empty($vals['dbcomment']) ? $vals['label'] : $vals['dbcomment'],
                            ];
                            if (!isset($item['type'])) {
                                $item['type'] = 'bool';
                            }
                            if ($item['type'] == 'int') {
                                $option['type'] = 'int';
                                $option['len'] = 11;
                            } elseif ($item['type'] == 'bool') {
                                $option['type'] = 'tinyint';
                                $option['len'] = 1;
                            } else {
                                $option['type'] = 'varchar';
                                $option['len'] = 250;
                            }
                            DB::modifyField($tbname, $item['field'], $option);
                        }
                    }
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->error('修改字段失败');
            }
            DB::update('@pf_tool_field', $vals, $id);
            MakeForm::make($this->formId);
            $this->success('编辑' . $form->title . '成功');
        }
    }

    public function copy($id)
    {
        $frow = DB::getRow('select tbName,tbCreate,viewUseTab,viewTabs from @pf_tool_form where id=?', $this->formId);
        if ($frow == null) {
            $this->error('添加失败,表单不存在');
        }
        $vals = DB::getRow('select * from @pf_tool_field where id=?', $id);
        if ($vals == null) {
            $this->error('不存在的数据');
        }
        unset($vals['id']);
        $vals['sort'] = intval(DB::getMax('@pf_tool_field', 'sort', 'formId=?', $this->formId)) + 10;
        $vals['formId'] = $this->formId;
        if ($vals['names']) {
            $vals['names'] = json_decode($vals['names'], true);
            if ($vals['extendAttrs']) {
                $vals['extendAttrs'] = json_decode($vals['extendAttrs'], true);
            } else {
                $vals['extendAttrs'] = [];
            }
        }
        $tbname = '@pf_' . $frow['tbName'];
        try {
            DB::beginTransaction();
            if ($frow['tbCreate'] == 1) {
                if ($vals['dbtype'] != 'null' && $vals['dbfield'] == 1) {
                    $idx = 1;
                    $name = $vals['name'];
                    while (DB::existsField($tbname, $vals['name'])) {
                        $vals['name'] = $name . $idx;
                        $idx++;
                    }

                    DB::addField($tbname, $vals['name'], [
                        'type' => $vals['dbtype'],
                        'len' => $vals['dblen'],
                        'scale' => $vals['dbpoint'],
                        'comment' => empty($vals['dbcomment']) ? $vals['label'] : $vals['dbcomment'],
                    ]);
                }
                if (!empty($vals['names'])) {
                    foreach ($vals['names'] as &$item) {
                        $idx = 1;
                        $name = $item['field'];
                        while (DB::existsField($tbname, $item['field'])) {
                            $item['field'] = $name . $idx;
                            $idx++;
                        }
                        $option = [
                            'type' => $vals['dbtype'],
                            'len' => 11,
                            'scale' => 0,
                            'comment' => empty($vals['dbcomment']) ? $vals['label'] : $vals['dbcomment'],
                        ];
                        if (!isset($item['type'])) {
                            $item['type'] = 'bool';
                        }
                        if ($item['type'] == 'int') {
                            $option['type'] = 'int';
                            $option['len'] = 11;
                        } elseif ($item['type'] == 'bool') {
                            $option['type'] = 'tinyint';
                            $option['len'] = 1;
                        } else {
                            $option['type'] = 'varchar';
                            $option['len'] = 250;
                        }
                        DB::addField($tbname, $item['field'], $option);
                    }
                    $vals['extendAttrs']['names'] = json_encode($vals['names'], JSON_UNESCAPED_UNICODE);
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->error('字段拷贝失败');
        }
        DB::insert('@pf_tool_field', $vals);
    }

    public function copyAction($cptype = '', array $fids = [])
    {
        $this->loadFormId();
        if ($cptype !== 'field') {
            $this->error('字段拷贝失败');
        }
        if (empty($fids)) {
            $this->error('字段拷贝失败');
        }
        foreach ($fids as $id) {
            $this->copy($id);
        }
        MakeForm::make($this->formId);
        $this->success('字段拷贝成功');

    }

    public function editSortAction(int $id = 0, $sort = 0)
    {
        $row = DB::getRow('select * from @pf_tool_field where id=?', $id);
        if ($row == null) {
            $this->error('不存在的数据');
        }
        $this->formId = $row['formId'];
        DB::update('@pf_tool_field', ['sort' => $sort], $id);
        MakeForm::make($this->formId);
        $this->success('更新排序成功');
    }

    private function delete($id = 0)
    {
        if ($id == 0) {
            return;
        }
        $row = DB::getRow('select * from @pf_tool_field where id=?', $id);
        $this->formId = $row['formId'];
        $frow = DB::getRow('select tbName,tbCreate from @pf_tool_form where id=?', $row['formId']);
        //删除字段
        if ($frow != null && $frow['tbCreate'] == 1) {
            $tbname = '@pf_' . $frow['tbName'];
            if ($row['names']) {
                $row['names'] = json_decode($row['names'], true);
                foreach ($row['names'] as $item) {
                    $field = isset($item['field']) ? $item['field'] : '';
                    if (!empty($field) && DB::existsField($tbname, $field)) {
                        DB::dropField($tbname, $field);
                    }
                }
            }
            if (DB::existsField($tbname, $row['name'])) {
                DB::dropField($tbname, $row['name']);
            }
        }
        DB::delete('@pf_tool_field', $id);
    }

    public function delAction(int $id = 0)
    {
        if ($id == 0) {
            $this->error('参数有误');
        }
        $this->delete($id);
        if ($this->formId) {
            MakeForm::make($this->formId);
        }
        $this->success('删除字段成功');
    }

    public function delSelectAction(string $sel_id = '')
    {
        $fids = explode(',', $sel_id);
        foreach ($fids as $id) {
            $this->delete($id);
        }
        if ($this->formId) {
            MakeForm::make($this->formId);
        }
        $this->success('删除选中字段成功');
    }

    public function fanyiAction($label = '')
    {

        if (empty($label)) {
            $this->error('翻译失败');
        }
        $url = "http://api.fanyi.baidu.com/api/trans/vip/translate";
        $salt = time();
        $appid = '20180212000122391';
        $keysArr = array(
            'from' => 'zh',
            'to' => 'en',
            'q' => $label,
            'appid' => '20180212000122391',
            'salt' => $salt,
            'sign' => md5($appid . $label . $salt . 'ppa8TQ6cyQkxoknvnO_8'),
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        curl_close($ch);
        $a = json_decode($ret, 1);
        if (empty($a) || empty($a['trans_result']) || empty($a['trans_result']) || empty($a['trans_result'][0]) || empty($a['trans_result'][0]['dst'])) {
            $this->error('翻译失败');
        }
        $word = Utils::toCamel(preg_replace('@[^A-Za-z0-9]+@', '_', $a['trans_result'][0]['dst']));
        $this->success('翻译成功', ['name' => lcfirst($word)]);
    }

    public function loadPluginAction($id = 0, $type = 'text')
    {
        $typeClass = Route::getNamespace() . '\\module\\' . Utils::toCamel($type) . 'Module';
        $form = Form::instance($typeClass);
        if ($form == null) {
            $this->success('', null);
        }
        $vals = $form->autoComplete();
        $this->assign('form', $form);
        $code = $this->fetch('common/ajaxform.tpl');
        $this->success('', $code);
    }

    public function defaultSetAction($default = '')
    {
        $this->assign('code', $default);
        $this->display('default_set.tpl');
    }

    public function textSetAction($default = '')
    {
        $this->assign('code', $default);
        $this->display('text_set.tpl');
    }
}