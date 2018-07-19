<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/11
 * Time: 14:40
 */

namespace app\tool\controller;


use app\tool\form\ToolFormForm;
use app\tool\libs\MakeForm;
use beacon\DB;
use beacon\SqlSelector;
use beacon\Utils;

class ToolForm extends ToolController
{
    public function indexAction()
    {
        if ($this->isAjax()) {
            $selector = new SqlSelector('@pf_tool_form');
            $title = $this->get('title', '');
            $extMode = $this->get('extMode', '');
            if ($title) {
                $selector->where("(`title` LIKE CONCAT('%',?,'%') or `key` LIKE CONCAT('%',?,'%'))", [$title, $title]);
            }
            if ($extMode !== '') {
                if ($extMode == 0) {
                    $selector->where("(`extMode` = 0 or `extMode` = 3)");
                } else {
                    $selector->where("`extMode` = ?", $extMode);
                }
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
            $this->assign('list', $list);
            $this->assign('pdata', $pdata);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('form.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('form.tpl');
    }

    public function checkKeyAction()
    {
        $form = new ToolFormForm();
        $username = $this->param('key', '');
        $remoteFunc = $form->getField('key')->remoteFunc;
        if ($remoteFunc($username)) {
            $this->success('表单标识可以使用');
        }
        $this->error('表单标识已经存在');
    }

    public function addAction($copyid = 0)
    {
        $form = new ToolFormForm('add');
        if ($this->isGet()) {
            if ($copyid) {
                $row = $form->getRow($copyid);
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
            $pro = DB::getRow('select * from @pf_tool_application where id=?', $vals['proId']);
            if ($pro) {
                $vals['namespace'] = $pro['namespace'];
            } else {
                $this->error(['proId' => '不存在的项目']);
            }
            if ($vals['tbCreate']) {
                try {
                    if (empty($vals['tbName'])) {
                        $this->error(['tbName' => '数据库表名没有填写']);
                    }
                    if (empty($vals['tbEngine'])) {
                        $this->error(['tbEngine' => '数据库表引擎没有选择']);
                    }
                    DB::createTable('@pf_' . $vals['tbName'], ['engine' => $vals['tbEngine']]);
                } catch (\Exception $exception) {
                    $this->error(['tbName' => '创建数据库表失败']);
                }
            }
            DB::insert('@pf_tool_form', $vals);
            $id = DB::lastInsertId();
            if ($copyid != 0) {
                $fieldList = DB::getList('select id from @pf_tool_field where formId=? order by sort asc', $copyid);
                foreach ($fieldList as $field) {
                    $this->copyField($id, $field['id']);
                }
            }
            MakeForm::make($id);
            $this->success('添加' . $form->title . '成功');
        }
    }

    public function copyField($formId, $id)
    {
        $frow = DB::getRow('select tbName,tbCreate,viewUseTab,viewTabs from @pf_tool_form where id=?', $formId);
        if ($frow == null) {
            return;
        }
        $vals = DB::getRow('select * from @pf_tool_field where id=?', $id);
        if ($vals == null) {
            return;
        }
        unset($vals['id']);
        $vals['sort'] = intval(DB::getMax('@pf_tool_field', 'sort', 'formId=?', $formId)) + 10;
        $vals['formId'] = $formId;
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
            return;
        }
        DB::insert('@pf_tool_field', $vals);
    }

    public function editAction(int $id = 0)
    {
        $form = new ToolFormForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }

        $row = DB::getRow('select * from @pf_tool_form where id=?', $id);
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
            $pro = DB::getRow('select * from @pf_tool_application where id=?', $vals['proId']);
            if ($pro) {
                $vals['namespace'] = $pro['namespace'];
            } else {
                $this->error(['proId' => '不存在的项目']);
            }
            try {
                //修改表名
                DB::beginTransaction();
                if ($vals['tbCreate'] == 1) {
                    $tbname = '@pf_' . $vals['tbName'];
                    if (empty($vals['tbName'])) {
                        DB::rollBack();
                        $this->error(['tbName' => '数据库表名没有填写']);
                    }
                    if (empty($vals['tbEngine'])) {
                        DB::rollBack();
                        $this->error(['tbEngine' => '数据库表引擎没有选择']);
                    }
                    if (DB::existsTable("@pf_{$row['tbName']}")) {
                        if ($row['tbName'] != $vals['tbName']) {
                            DB::exec("alter  table `@pf_{$row['tbName']}` rename to `{$tbname}`;");
                        }
                        if ($row['tbEngine'] != $vals['tbEngine']) {
                            DB::exec("alter  table `{$tbname}` engine = {$vals['tbEngine']};");
                        }
                        if ($row['tbCreate'] != 1) {
                            $list = DB::getList('select `name`,dbtype as type,dblen as len,dbpoint as scale,label,dbcomment from @pf_tool_field where formId=? and dbtype<>? and dbfield=1', [$id, 'null']);
                            foreach ($list as $item) {
                                $item['comment'] = empty($item['dbcomment']) ? $item['label'] : $item['dbcomment'];
                                DB::modifyField($tbname, $item['name'], $item);
                            }
                        }
                    } else {
                        DB::createTable($tbname, ['engine' => $vals['tbEngine']]);
                        $list = DB::getList('select `name`,dbtype as type,dblen as len,dbpoint as scale,label,dbcomment from @pf_tool_field where formId=? and dbtype<>? and dbfield=1', [$id, 'null']);
                        foreach ($list as $item) {
                            $item['comment'] = empty($item['dbcomment']) ? $item['label'] : $item['dbcomment'];
                            DB::addField($tbname, $item['name'], $item);
                        }
                    }
                }
                DB::update('@pf_tool_form', $vals, $id);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->error(['tbName' => '创建数据库表失败']);
            }
            MakeForm::make($id);
            $this->success('编辑' . $form->title . '成功');
        }
    }

    public function delAction(int $id = 0)
    {
        if ($id == 0) {
            $this->error('参数有误');
        }
        DB::delete('@pf_tool_form', $id);
        DB::delete('@pf_tool_field', 'formId=?', $id);
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
        return $this->fetch('code.tpl');
    }
}