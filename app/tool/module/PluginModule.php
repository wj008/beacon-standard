<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/16
 * Time: 16:55
 */

namespace app\tool\module;


use app\tool\libs\MakeInterface;
use app\tool\libs\ModuleInterface;
use beacon\DB;
use beacon\Form;

class PluginModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
                'tips' => '在数据库中的字段类型',
            ],
            'plugName' => [
                'label' => '插件名',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '属性名不能为空'],
                'type' => 'select',
                'header' => '选择插件',
                'options' => function () {
                    $list = DB::getList('select `key`,title,namespace from @pf_tool_form where extMode=1');
                    $out = [];
                    foreach ($list as $item) {
                        $val = $item['namespace'] . '\\zero\\form\\Zero' . $item['key'] . 'Plugin';
                        $out[] = [$val, $item['title'] . ' | ' . $val];
                    }
                    return $out;
                },
            ],
            'plugMode' => [
                'label' => '插件类型',
                'type' => 'radiogroup',
                'options' => [
                    ['simple', '简单(simple)'],
                    ['composite', '复合(composite)'],
                ],
                'default' => 'composite',
                'dynamic' => [
                    [
                        'eq' => 'simple',
                        'show' => 'simpleType',
                        'hide' => 'compositeType',
                    ],
                    [
                        'eq' => 'composite',
                        'show' => 'compositeType',
                        'hide' => 'simpleType',
                    ],
                ],
            ],
            'simpleType' => [
                'label' => '呈现方式',
                'type' => 'select',
                'options' => [
                    [0, '默认'],
                    [1, '单行'],
                    [2, '紧凑'],
                    [3, '换行'],
                ],
            ],
            'compositeType' => [
                'label' => '呈现方式',
                'type' => 'select',
                'options' => [
                    [0, '默认'],
                    [1, '单行(按钮左)'],
                    [2, '紧凑(按钮左)'],
                    [3, '换行'],
                    [4, '单行(按钮右)'],
                    [5, '紧凑(按钮右)'],
                ],
            ],

            'viewShowRemoveBtn' => [
                'label' => '移除按钮',
                'type' => 'check',
                'afterText' => '勾选显示移除按钮',
                'default'=>1
            ],
            'viewShowInsertBtn' => [
                'label' => '插入按钮',
                'type' => 'check',
                'afterText' => '勾选显示插入按钮'
            ],
            'viewShowSortBtn' => [
                'label' => '排序按钮',
                'type' => 'check',
                'afterText' => '勾选显示排序按钮'
            ],
            'autoSave' => [
                'label' => '是否自动分表保存',
                'type' => 'check',
                'afterText' => '勾选分表保存数据',
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'referenceField',
                    ],
                    [
                        'neq' => 1,
                        'hide' => 'referenceField',
                    ],
                ],
            ],
            'referenceField' => [
                'label' => '关联字段名称',
                'data-val' => ['r' => true],
                'data-val-msg' => ['r' => '关联字段名称不能为空'],
            ],
            'viewtplName' => [
                'label' => '插件模板',
            ],
            'dataMinSize' => [
                'label' => '最小行数',
                'type' => 'integer',
                'default' => 0,

            ],
            'dataMaxSize' => [
                'label' => '最大行数',
                'type' => 'integer',
                'default' => 1000,
                'view-merge' => -1,
            ]
        ];
    }

    public static function exportField(MakeInterface $maker, array &$field, array $extend)
    {
        $extend['plugType'] = 0;
        if ($extend['plugMode'] == 'simple') {
            $extend['plugType'] = $extend['simpleType'];
        } else {
            $extend['plugType'] = $extend['compositeType'];
        }
        unset($extend['compositeType']);
        unset($extend['simpleType']);
        foreach ($extend as $key => $item) {
            $field[$key] = $item;
        }
    }

}