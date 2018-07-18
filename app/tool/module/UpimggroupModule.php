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

class UpimggroupModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],

            'dataUrl' => [
                'label' => '上传路径',
                'default' => '/service/upfile'
            ],

            'dataExtensions' => [
                'label' => '允许上传的类型',
                'box-style' => 'width:300px',
                'default' => 'jpg,jpeg,bmp,gif,png'
            ],

            'dataFieldName' => [
                'label' => '上传域名称',
                'default' => 'filedata'
            ],

            'dataSize' => [
                'label' => '上传最大数量',
                'type' => 'integer',
                'default' => '0',
                'tips' => '0为不限制数量',
            ],

            'dataBtnWidth' => [
                'label' => '显示区宽',
                'type' => 'integer',
                'default' => '400'
            ],
            'dataBtnHeight' => [
                'label' => '高',
                'type' => 'integer',
                'viewMerge' => -1,
                'default' => '300'
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