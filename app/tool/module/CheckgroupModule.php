<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/19
 * Time: 13:33
 */

namespace app\tool\module;


use app\tool\libs\CodeItem;
use app\tool\libs\MakeForm;
use app\tool\libs\ModuleInterface;
use beacon\Form;
use beacon\Utils;

class CheckgroupModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],

            'bitComp' => [
                'label' => '是否按位存储',
                'type' => 'check', // 单选组
                'default' => 0,
                'after-text' => '勾选按位存储',
                'box-onclick' => "if($(this).is(':checked')){\$(':input[name=optionsType]:first').trigger('click');}",
                'dynamic' => [
                    [
                        'eq' => 1,
                        'hide' => 'optionsType,names',
                    ],
                    [
                        'neq' => 1,
                        'show' => 'optionsType,names',
                    ],
                ],
            ],
            'optionsType' => [
                'label' => '选项类型',
                'type' => 'radiogroup', // 单选组
                'options' => [
                    [1, '直填值'],
                    [2, 'SQL查询'],
                ],
                'default' => 1,
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'options',
                        'hide' => 'optionsMethod,optionsParam,optionsSql,optionsField',
                    ],
                    [
                        'eq' => 2,
                        'show' => 'optionsMethod,optionsParam,optionsSql,optionsField',
                        'hide' => 'options',
                    ],
                ],
            ],

            'options' => [
                'label' => '选项 [options]',
                'type' => 'plugin',
                'plug-name' => 'OptionPlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'view-show-insert-btn' => true,
                'view-show-sort-btn' => true,
            ],

            'optionsMethod' => [
                'label' => '请求参数',
                'type' => 'select',
                'options' => [
                    ['req', 'REQUEST'],
                    ['get', 'GET'],
                    ['post', 'POST'],
                ],
            ],

            'optionsParam' => [
                'label' => '字段',
                'type' => 'text',
                'tips' => '多个请用,隔开',
                'box-style' => 'width:220px;',
                'viewMerge' => -1,
            ],

            'optionsSql' => [
                'label' => '查询语句',
                'type' => 'textarea',
            ],

            'optionsField' => [
                'label' => '查询字段',
                'tips' => '多个请用,隔开',
                'box-style' => 'width:320px;',
            ],

            'itemType' => [
                'label' => 'item值类型',
                'type' => 'select',
                'options' => [
                    ['string', 'string'],
                    ['integer', 'integer'],
                    ['float', 'float'],
                ],
            ],

            'names' => [
                'label' => '拆分字段保存',
                'type' => 'plugin',
                'plug-name' => 'CheckgroupNamesPlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'viewShowInsertBtn' => true,
                'viewShowSortBtn' => true,
            ],
            'useUlList' => [
                'label' => '使用LI',
                'type' => 'check', // 单选组
                'default' => 0,
                'after-text' => '勾选使用LI',
            ]


        ];
    }

    /**
     * @param $maker \app\tool\libs\MakeForm
     * @param $field array
     * @param $extend array
     */
    public static function exportField(MakeForm $maker, array &$field, array $extend)
    {
        $field['itemType'] = isset($extend['itemType']) ? $extend['itemType'] : 'string';
        $field['bitComp'] = isset($extend['bitComp']) ? boolval($extend['bitComp']) : false;
        $field['useUlList'] = isset($extend['useUlList']) ? boolval($extend['useUlList']) : false;
        if (is_string($extend['options']) && Utils::isJsonString($extend['options'])) {
            $extend['options'] = json_decode($extend['options'], true);
        }
        if ($field['bitComp']) {
            $field['options'] = empty($extend['options']) ? [] : $extend['options'];
        } else {
            if (isset($extend['optionsType']) && $extend['optionsType'] == 2) {
                $code = [];
                $code[] = 'function(){';
                $code[] = '    $options=[];';
                if (!empty($extend['optionsMethod']) && !empty($extend['optionsParam'])) {
                    $param = explode(',', $extend['optionsParam']);
                    $code[] = '    $param=[];';
                    $code[] = '    $req=Request::instance();';
                    foreach ($param as $item) {
                        if ($extend['optionsMethod'] == 'post') {
                            $code[] = '    $param[]= $req->post(' . var_export(trim($item), true) . ');';
                        } else if ($extend['optionsMethod'] == 'get') {
                            $code[] = '    $param[]= $req->get(' . var_export(trim($item), true) . ');';
                        } else {
                            $code[] = '    $param[]= $req->param(' . var_export(trim($item), true) . ');';
                        }
                    }
                    $code[] = '    $rows = DB::getList(' . var_export(trim($extend['optionsSql']), true) . ',$param);';
                } else {
                    $code[] = '    $rows = DB::getList(' . var_export(trim($extend['optionsSql']), true) . ');';
                }
                $code[] = '    foreach($rows as $rs){';
                $code[] = '        $item=[];';
                if (!empty($extend['optionsField'])) {
                    $optfields = explode(',', $extend['optionsField']);
                    foreach ($optfields as $opt) {
                        $code[] = '        $item[] = isset($rs[' . var_export(trim($opt), true) . ']) ? $rs[' . var_export(trim($opt), true) . '] : \'\';';
                    }
                } else {
                    $code[] = '        $rs = array_value($rs);';
                    $code[] = '        $item[] = isset($rs[0]) ? $rs[0]: \'\';';
                    $code[] = '        $item[] = isset($rs[1]) ? $rs[1]: \'\';';
                }
                $code[] = '        $options[] = $item;';
                $code[] = '    }';
                $code[] = '    return $options;';
                $code[] = '}';
                $field['options'] = new CodeItem();
                $field['options']->addUse('beacon\Request');
                $field['options']->addUse('beacon\DB');
                $field['options']->setCode(join("\n", $code));
            } else {
                $field['options'] = empty($extend['options']) ? [] : $extend['options'];
            }
        }
    }
}