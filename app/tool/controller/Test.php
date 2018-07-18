<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/20
 * Time: 17:51
 */

namespace app\tool\controller;


use app\tool\libs\MakeForm;
use beacon\Form;
use beacon\Route;

class Test extends ToolController
{
    private $formId = 0;

    private function loadFormId()
    {
        $this->formId = $this->get('formId:i', 0);
        if ($this->formId == 0) {
            $this->error('缺少参数', null, Route::url('~/tool_form'));
        }
        $this->assign('formId', $this->formId);
    }

    function indexAction($type = 'add')
    {
        $this->loadFormId();
        $maker = new MakeForm($this->formId);
        $code = $maker->getCode();
        eval('?' . '>' . $code);

        $form = Form::instance($maker->getClassName(), $type);
        $form->useAjax = false;
        foreach ($form->getTabFields() as $field) {
            if ($field->type == 'plugin') {
                $field->viewtplName = null;
            }
        }
        if ($this->isGet()) {
            $this->displayForm($form);
            return;
        }
        if ($this->isPost()) {
            $vals = $form->autoComplete();
            if (!$form->validation($error)) {
                $this->error($error);
            }
            var_export($vals);
        }
    }
}