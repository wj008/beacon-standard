<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/19
 * Time: 13:33
 */

namespace app\tool\module;


use app\tool\libs\CodeItem;
use app\tool\libs\MakeInterface;
use app\tool\libs\ModuleInterface;
use beacon\Form;
use beacon\Utils;

class LinkageModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],

            'dataSource' => [
                'label' => '数据源地址',
            ],

            'dataMethod' => [
                'label' => '请求方式',
                'type' => 'select',
                'options' => [
                    ['get', 'GET'],
                    ['post', 'POST'],
                ],
            ],

            'dataLevel' => [
                'label' => '联动下拉级别',
                'type' => 'integer',
                'default' => 0,
                'tips' => '联动下拉的层级深度 0 为按数据层级自动增长',
            ],

            'dataHeader' => [
                'label' => '选项头',
                'type' => 'plugin',
                'plug-name' => 'LinkageHeadePlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'view-show-insert-btn' => true,
                'view-show-sort-btn' => true,
            ],

            'names' => [
                'label' => '拆分字段保存',
                'type' => 'plugin',
                'plug-name' => 'NamesPlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'viewShowInsertBtn' => true,
                'viewShowSortBtn' => true,
            ],

            'dataValGroup' => [
                'label' => '验证配置 [data-val-group]',
                'type' => 'textarea',
                'tips' => '验证规则配置',
                'view-tab-index' => 'valid',
                'box-yee-module' => 'validgroup',
                'box-yee-depend' => '/tool/js/validtor.js',
            ],
            'isEditDataValGroup' => [
                'label' => '编辑修正',
                'type' => 'check',
                'view-tab-index' => 'valid',
                'view-merge' => -1,
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'editDataValGroup',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'editDataValGroup',
                    ],
                ],
            ],
            'editDataValGroup' => [
                'label' => '验证配置 (编辑)',
                'type' => 'textarea',
                'view-tab-index' => 'valid',
                'box-yee-module' => 'validgroup',
                'box-yee-depend' => '/tool/js/validtor.js',
            ],
        ];
    }

    public static function exportField(MakeInterface $maker, array &$field, array $extend)
    {
        foreach ($extend as $key => $item) {
            if ($key == 'dataHeader') {
                if (Utils::isJsonString($item)) {
                    $temp = json_decode($item, true);
                    $idx = 1;
                    foreach ($temp as $xitem) {
                        $field[$key . $idx] = $xitem['name'];
                        $idx++;
                    }
                }
                continue;
            }
            if ($key == 'dataSource') {
                if ($item[0] == '~' || $item[0] == '^') {
                    $citem = new CodeItem();
                    $citem->addUse('beacon\Route');
                    $citem->setCode('Route::url(' . var_export($item, true) . ')');
                    $item = $citem;
                }
            }
            $field[$key] = $item;
        }
    }

}