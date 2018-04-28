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

class CustomPlugin extends Form
{
    protected function load()
    {
        return [

            'name' => [
                'label' => '属性名',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '属性名不能为空'],
                'box-style' => 'width:120px;'
            ],
            'type' => [
                'label' => '值类型',
                'type' => 'select',
                'options' => [
                    ['string', 'string'],
                    ['integer', 'integer'],
                    ['float', 'float'],
                    ['boolean', 'boolean'],
                    ['array', 'array'],
                ],
                'data-val' => ['r' => true],
                'view-merge' => -1,
                'data-val-msg' => ['r' => '属性名不能为空'],
                'box-style' => 'width:120px;'
            ],
            'value' => [
                'label' => '属性值',
                'type' => 'textarea',
                //'box-style' => 'width:100%;height:400px',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '属性值不能为空'],
            ],
        ];
    }
}