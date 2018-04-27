<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/27
 * Time: 1:51
 */

namespace app\service\controller;


use beacon\Controller;

class IconSelect extends Controller
{
    public function indexAction()
    {
        $this->display('IconSelect.tpl');
    }
}