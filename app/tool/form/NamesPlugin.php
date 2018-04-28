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

class NamesPlugin extends Form
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
            'type' => [
                'label' => '字段类型',
                'type' => 'select',
                'options' => [
                    ['int', '整数(int)'],
                    ['string', '字符串(string)'],
                    ['bool', 'Bool值(bool)'],
                ],
            ],

        ];
    }
}