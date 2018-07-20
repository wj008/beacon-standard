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

class XheditorModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],

            'dataUpLinkUrl' => [
                'label' => '文件上传路径',
                'default' => '/service/xh_upfile?immediate=1',
                'tips'=>'如果不支持请留空'
            ],
            'dataUpLinkExt' => [
                'label' => '文件上传后缀',
                'box-style' => 'width:300px',
                'default' => 'txt,doc,docx,zip,rar,xls,xlsx,pdf'
            ],
            'dataUpImgUrl' => [
                'label' => '文件上传路径',
                'default' => '/service/xh_upfile?immediate=1',
                'tips'=>'如果不支持请留空'
            ],
            'dataUpImgExt' => [
                'label' => '图片上传后缀',
                'box-style' => 'width:300px',
                'default' => 'jpg,jpeg,bmp,gif,png'
            ],

            'dataSkin' => [
                'label' => '选择皮肤',
                'type' => 'select',
                'options' => [
                    ['value' => 'default', 'text' => 'default'],
                    ['value' => 'vista', 'text' => 'vista'],
                    ['value' => 'o2007blue', 'text' => 'o2007blue'],
                    ['value' => 'o2007silver', 'text' => 'o2007silver'],
                ],
            ],

            'dataTools' => [
                'label' => '按钮风格',
                'type' => 'select',
                'options' => [
                    ['value' => 'full', 'text' => 'full'],
                    ['value' => 'mfull', 'text' => 'mfull'],
                    ['value' => 'simple', 'text' => 'simple'],
                    ['value' => 'mini', 'text' => 'mini'],
                ],
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