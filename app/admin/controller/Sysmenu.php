<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/26
 * Time: 13:33
 */

namespace app\admin\controller;


use app\admin\form\SysmenuForm;
use beacon\DB;

class Sysmenu extends AdminController
{
    public function indexAction()
    {
        if ($this->isAjax()) {

            $items = [];
            $name = $this->get('name', '');
            if (!empty($name)) {
                $rows = DB::getList("SELECT * FROM @pf_sysmenu A WHERE pid = 0 AND ( `name` LIKE CONCAT('%', ?, '%') OR EXISTS (SELECT id FROM @pf_sysmenu B WHERE pid = A.id AND (`name` LIKE CONCAT('%', ?, '%') OR EXISTS (SELECT 1 FROM @pf_sysmenu WHERE pid = B.id AND `name` LIKE CONCAT('%', ?, '%'))))) ORDER BY sort ASC", [$name, $name, $name]);
            } else {
                $rows = DB::getList('select * from @pf_sysmenu where pid=0 order by sort asc');
            }
            foreach ($rows as $item) {
                $item['title'] = '<b>' . $item['name'] . '</b>';
                $item['create'] = true;
                $items[] = $item;
                if (!empty($name)) {
                    $temp = DB::getList("SELECT * FROM @pf_sysmenu A WHERE pid = ? AND ( `name` LIKE CONCAT('%', ?, '%') OR EXISTS (SELECT id FROM @pf_sysmenu B WHERE pid = A.id AND `name` LIKE CONCAT('%', ?, '%'))) ORDER BY sort ASC", [$item['id'], $name, $name]);
                } else {
                    $temp = DB::getList('select * from @pf_sysmenu where pid=?  order by sort asc', $item['id']);
                }
                foreach ($temp as $rs) {
                    $rs['title'] = '+--- <span>' . $rs['name'] . '</span>';
                    $rs['create'] = true;
                    $items[] = $rs;
                    if (!empty($name)) {
                        $xtemp = DB::getList("SELECT * FROM @pf_sysmenu A WHERE pid = ? AND  `name` LIKE CONCAT('%', ?, '%') ORDER BY sort ASC", [$rs['id'], $name]);
                    } else {
                        $xtemp = DB::getList('select * from @pf_sysmenu where pid=? order by sort asc', $rs['id']);
                    }
                    foreach ($xtemp as $xrs) {
                        $xrs['title'] = '+---+--- <span class="blue">' . $xrs['name'] . '</span>';
                        $xrs['create'] = false;
                        $items[] = $xrs;
                    }
                }
            }
            $this->assign('list', $items);
            $this->assign('pdata', ['recordsCount' => count($items)]);
            $data = $this->getAssign();
            $data['list'] = $this->hackData('Sysmenu.hack.tpl', $data['list']);
            $this->success('获取数据成功', $data);
        }
        $this->display('Sysmenu.tpl');
    }

    public function editSortAction(int $id = 0, int $sort = 0)
    {
        DB::update('@pf_sysmenu', ['sort' => $sort], $id);
        $this->success('更新排序成功');
    }

    public function addAction()
    {
        $form = new SysmenuForm('add');
        if ($this->isGet()) {
            $this->displayForm($form);
            return;
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            $form->insert();
            $this->success('添加' . $form->title . '成功');
        }
    }

    public function editAction(int $id = 0)
    {
        $form = new SysmenuForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $form->initValues($form->getRow($id));
        if ($this->isGet()) {
            $this->displayForm($form);
            return;
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            $form->update($id);
            $this->success('编辑' . $form->title . '成功');
        }
    }

    public function deleteAction(int $id = 0)
    {
        $form = new SysmenuForm('edit');
        if ($id == 0) {
            $this->error('参数有误');
        }
        $form->delete($id);
        $this->success('删除' . $form->title . '成功');
    }
}