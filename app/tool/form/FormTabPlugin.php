<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/13
 * Time: 16:35
 */

namespace app\tool\form;


use beacon\Form;

class FormTabPlugin extends Form
{
    protected function load()
    {
        return [
            'key' => [
                'label' => '标识',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '标识不能为空'],
                'box-style' => 'width:120px;'
            ],
            'value' => [
                'label' => '文本',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '文本不能为空'],
            ],
        ];
    }
}