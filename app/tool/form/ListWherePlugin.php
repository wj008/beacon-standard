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

class ListWherePlugin extends Form
{


    protected function load()
    {
        return [

            'sql' => [
                'label' => '查询代码',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '附加表不能为空'],
                'box-style' => 'width:300px; height:20px;margin-top: 2px;',
                'type' => 'textarea',
            ],
            'param' => [
                'label' => '参数名',
                'data-val' => ['regex' => '^(\w+(:[abfis])?)(,\w+(:[abfis])?)*$'],
                'data-val-msg' => ['regex' => '参数格式不正确'],
                'tips' => '如有多个用,隔开'
            ],
            'type' => [
                'label' => '加入条件',
                'type' => 'select',
                'var-type' => 'integer',
                'view-merge' => -1,
                'options' => [
                    [0, '为[空,0,null]不加入'],
                    [1, '为[null]不加入'],
                    [2, '为[空,null]不加入'],
                    [3, '为[0,null]不加入'],
                    [-1, '直接加入'],
                ]
            ],

        ];
    }
}