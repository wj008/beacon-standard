<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/19
 * Time: 13:33
 */

namespace app\tool\module;


use app\tool\libs\MakeForm;
use beacon\Form;
use app\tool\libs\ModuleInterface;

class QiniuModule extends Form implements ModuleInterface
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
                'default' => 'http://up-z1.qiniu.com'
            ],

            'dataExtensions' => [
                'label' => '允许上传的类型',
                'box-style' => 'width:300px',
                'default' => 'txt,doc,docx,zip,rar,jpg,jpeg,bmp,gif,xls,xlsx,pdf'
            ],
            'dataFieldName' => [
                'label' => '上传域名称',
                'default' => 'file'
            ],

        ];
    }

    public static function exportField(MakeForm $maker, array &$field, array $extend)
    {
        foreach ($extend as $key => $item) {
            $field[$key] = $item;
        }
    }

}