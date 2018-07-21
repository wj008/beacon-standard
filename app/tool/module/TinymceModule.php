<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/7/21
 * Time: 4:40
 */

namespace app\tool\module;


use app\tool\libs\MakeInterface;
use app\tool\libs\ModuleInterface;
use beacon\Form;

class TinymceModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],

            'dataImagesUploadUrl' => [
                'label' => '图片上传路径',
                'default' => '/service/tiny_upfile',
            ],
        ];
    }

    public static function exportField(MakeInterface $maker, array &$field, array $extend)
    {
        foreach ($extend as $key => $item) {
            $field[$key] = $item;
        }
    }
}