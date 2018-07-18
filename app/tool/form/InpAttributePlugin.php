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

class InpAttributePlugin extends Form
{
    protected function load()
    {
        return [
            'name' => [
                'label' => '属性名',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '属性名不能为空'],
                'box-style'=>'width:120px;'
            ],
            'value' => [
                'label' => '属性值',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '属性值不能为空'],
            ],
        ];
    }
}