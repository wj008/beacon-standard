<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/11
 * Time: 14:39
 */

namespace app\tool\controller;


use beacon\Controller;
use beacon\Form;

class ToolController extends Controller
{

    protected function displayForm(Form $form, $tplname = 'common/form.tpl')
    {
        $this->assign('form', $form);
        parent::display($tplname);
        exit;
    }
}