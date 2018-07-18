<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/11
 * Time: 14:40
 */

namespace app\tool\controller;


use app\tool\form\ToolListForm;
use app\tool\libs\MakeController;
use app\tool\libs\MakeForm;
use beacon\DB;
use beacon\Route;
use beacon\SqlSelector;
use beacon\Utils;

class ToolList extends ToolController
{
    public function indexAction()
    {
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_tool_list');
            $title = $this->get('title', '');
            if ($title) {
                $selector->where("(`title` LIKE CONCAT('%',?,'%') or `key` LIKE CONCAT('%',?,'%'))", [$title, $title]);
            }
            $sort = $this->get('sort', 'id_desc');
            switch ($sort) {
                case 'id_asc':
                    $selector->order('id asc');
                    break;
                case 'id_desc':
                    $selector->order('id desc');
                    break;
                case 'title_asc':
                    $selector->order('title asc');
                    break;
                case 'title_desc':
                    $selector->order('title desc');
                    break;
                default:
                    $selector->order('id asc');
                    break;
            }
            $plist = $selector->getPageList();
            $pdata = $plist->getInfo();
            $list = $plist->getList();
            $prow_cache = [];
            foreach ($list as &$item) {
                $frow = DB::getRow('select `key`,tbName from @pf_tool_form where id=?', $item['formId']);
                if ($frow) {
                    $item['formKey'] = $frow['key'];
                    $item['tbName'] = $frow['tbName'];
                } else {
                    $item['formKey'] = '--';
                    $item['tbName'] = '--';
                }
                $proId = $item['proId'];
                if (!isset($prow_cache[$proId])) {
                    $prow_cache[$proId] = DB::getOne('select `module` from @pf_tool_application where id=?', $proId);
                }
                $item['testUrl'] = Route::url('^/' . $prow_cache[$proId] . '/' . $item['key']);
            }
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('list.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('list.tpl');
    }

    public function checkKeyAction()
    {
        $form = new ToolListForm();
        $username = $this->param('key', '');
        if (($form->getField('key')->remoteFunc)($username)) {
            $this->success('表单标识可以使用');
        }
        $this->error('表单标识已经存在');
    }

    public function addAction($copyid = 0)
    {
        $form = new ToolListForm('add');
        if ($this->isGet()) {
            if ($copyid) {
                $row = $form->getRow($copyid);
                $form->initValues($row);
            }
            $this->displayForm($form);
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            $pro = DB::getRow('select * from @pf_tool_application where id=?', $vals['proId']);
            if ($pro) {
                $vals['namespace'] = $pro['namespace'];
            } else {
                $this->error(['proId' => '不存在的项目']);
            }
            $id = $form->insert($vals);
            //拷贝添加
            if (!empty($copyid)) {
                $slist = DB::getList('select * from @pf_tool_search where listId=?', $copyid);
                foreach ($slist as $field) {
                    unset($field['id']);
                    $field['sort'] = intval(DB::getMax('@pf_tool_search', 'sort', 'listId=?', $id)) + 10;
                    $field['listId'] = $id;
                    if (empty($field['viewTabIndex'])) {
                        $field['viewTabIndex'] = 'base';
                    }
                    DB::insert('@pf_tool_search', $field);
                }
            }
            MakeController::make($id);
            $this->success('添加' . $form->title . '成功');
        }
    }

    public function editAction(int $id = 0)
    {
        $form = new ToolListForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $row = $form->getRow($id);
        $form->initValues($row);
        if ($this->isGet()) {
            $this->displayForm($form);
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            $pro = DB::getRow('select * from @pf_tool_application where id=?', $vals['proId']);
            if ($pro) {
                $vals['namespace'] = $pro['namespace'];
            } else {
                $this->error(['proId' => '不存在的项目']);
            }
            $form->update($id, $vals);
            MakeController::make($id);
            $this->success('编辑' . $form->title . '成功');
        }
    }

    public function delAction(int $id = 0)
    {
        if ($id == 0) {
            $this->error('参数有误');
        }
        DB::delete('@pf_tool_list', $id);
        DB::delete('@pf_tool_search', 'listId=?', $id);
        $this->success('删除账号成功');
    }

    public function fanyiAction($title = '')
    {
        if (empty($title)) {
            $this->error('翻译失败');
        }
        $url = "http://api.fanyi.baidu.com/api/trans/vip/translate";
        $salt = time();
        $appid = '20180212000122391';
        $keysArr = array(
            'from' => 'zh',
            'to' => 'en',
            'q' => $title,
            'appid' => '20180212000122391',
            'salt' => $salt,
            'sign' => md5($appid . $title . $salt . 'ppa8TQ6cyQkxoknvnO_8'),
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
        $word = Utils::toCamel(preg_replace('@[^A-Za-z]+@', '_', $a['trans_result'][0]['dst']));
        $this->success('翻译成功', ['key' => $word, 'tbname' => Utils::toUnder($word)]);
    }

    public function codeAction(int $id = 0)
    {
        $maker = new MakeForm($id);
        $this->assign('code', $maker->getCode());
        return $this->fetch('main/code.tpl');
    }

    public function getFieldAction(int $formId = 0)
    {
        $frow = DB::getRow('select tbName from @pf_tool_form where id=?', $formId);
        if ($frow == null) {
            $this->success('ok', []);
        }
        $fields = DB::getList('select `name`,`label` from @pf_tool_field where formId=?', $formId);
        $temp = [];
        foreach ($fields as $field) {
            $temp[$field['name']] = $field['label'];
        }
        $options = [];
        $list = DB::getFields('@pf_' . $frow['tbName']);
        foreach ($list as $item) {
            $field = $item['Field'];
            if (isset($temp[$field])) {
                $comment = $temp[$field];
            } else {
                $comment = !empty($item['Comment']) ? $item['Comment'] : $field;
            }
            $options[] = [$field, $comment . ' | ' . $field];
        }
        $this->success('ok', ['tbname' => '@pf_' . $frow['tbName'], 'options' => $options]);
    }
}