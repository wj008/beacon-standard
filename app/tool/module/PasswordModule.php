<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/19
 * Time: 13:33
 */

namespace app\tool\module;


use app\tool\libs\MakeInterface;
use app\tool\libs\ModuleInterface;
use beacon\Form;

class PasswordModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],
            'encodeFunc' => [
                'label' => '加密函数',
                'tips' => '如果需要加密保存，请填写加密函数，含命名空间'
            ],
        ];
    }

    public static function exportField(MakeInterface $maker, array &$field, array $extend)
    {
        // TODO: Implement exportField() method.
    }

}