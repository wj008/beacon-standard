<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/13
 * Time: 16:35
 */

namespace app\tool\form;


use beacon\DB;
use beacon\Form;

class CheckgroupNamesPlugin extends Form
{
    protected function load()
    {
        return [
            'field' => [
                'label' => '字段名',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '选项值不能为空'],
                'box-style' => 'width:120px;'
            ],
        ];
    }
}