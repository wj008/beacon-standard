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

class ListTbJoinPlugin extends Form
{


    protected function load()
    {
        return [

            'tbname' => [
                'label' => '附加表',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '附加表不能为空'],
            ],
            'alias' => [
                'label' => '别名',
                'view-merge' => -1,
                'data-val' => ['regex' => '^[A-Z]+$'],
                'data-val-msg' => ['regex' => '表别名只能是大写字母'],
                'box-style' => 'width:100px;',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '别名不能为空'],
            ],

            'join' => [
                'label' => 'JOIN',
                'type' => 'select',
                'view-merge' => -1,
                'options' => [['inner join', 'inner join | 交集'], ['left join', 'left join | 左集合'], ['right join', 'right join | 右集合']],
            ],

            'on' => [
                'label' => 'ON',
                'type' => 'textarea',
                'box-style' => 'width:200px; height:20px;margin-top: 2px;',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => 'JSON条件不能为空'],
            ],

        ];
    }
}