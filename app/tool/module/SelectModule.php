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

class SelectModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],
            'headerText' => [
                'label' => '选项头(文本)',
                'box-style' => 'width:200px;',
                'tips' => '下拉框的选项头'
            ],
            'headerValue' => [
                'label' => '选项头(值)',
                'box-style' => 'width:120px;',
                'viewMerge' => -1,
            ],
            'optionsType' => [
                'label' => '选项类型',
                'type' => 'radiogroup', // 单选组
                'options' => [
                    [1, '直填值'],
                    [2, 'SQL查询'],
                    [3, '远端链接'],
                    [4, '配置项'],
                ],
                'default' => 1,
                'dynamic' => [
                    [
                        'eq' => 1,
                        'show' => 'options',
                        'hide' => 'optionsMethod,optionsParam,optionsSql,optionsField,dataOptions,dataConfigName',
                    ],
                    [
                        'eq' => 2,
                        'show' => 'optionsMethod,optionsParam,optionsSql,optionsField',
                        'hide' => 'options,dataOptions,dataConfigName',
                    ],
                    [
                        'eq' => 3,
                        'show' => 'dataOptions',
                        'hide' => 'options,optionsMethod,optionsParam,optionsSql,optionsField,dataConfigName',
                    ],
                    [
                        'eq' => 4,
                        'show' => 'dataConfigName',
                        'hide' => 'options,optionsMethod,optionsParam,optionsSql,optionsField,dataOptions',
                    ],
                ],
            ],

            'options' => [
                'label' => '选项 [options]',
                'type' => 'plugin',
                'plug-name' => 'OptionPlugin',
                'plug-type' => 5,
                'plug-mode' => 'composite',
                'viewShowInsertBtn' => true,
                'viewShowSortBtn' => true,
                'viewShowRemoveBtn'=>true,
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

            'dataOptions' => [
                'label' => '选项链接',
                'type' => 'text',
                'box-style' => 'width:320px;',
            ],
            'configName' => [
                'label' => '配置项名称',
                'type' => 'text',
                'box-style' => 'width:200px;',
            ],

        ];
    }

    /**
     * @param $maker
     * @param array $field
     * @param array $extend
     */
    public static function exportField(MakeInterface $maker, array &$field, array $extend)
    {
        if (is_string($extend['options']) && Utils::isJsonString($extend['options'])) {
            $extend['options'] = json_decode($extend['options'], true);
        }
        if (!empty($extend['headerText']) && (!empty($extend['headerValue']) || $extend['headerValue'] === '0')) {
            $field['header'] = [$extend['headerValue'], $extend['headerText']];
        } elseif (!empty($extend['headerText'])) {
            $field['header'] = ['', $extend['headerText']];
        }

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
                $code[] = '        $rs = array_values($rs);';
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
        } else if (isset($extend['optionsType']) && $extend['optionsType'] == 3) {
            $field['dataOptions'] = empty($extend['dataOptions']) ? [] : $extend['dataOptions'];
        } else if (isset($extend['optionsType']) && $extend['optionsType'] == 4) {
            if (!empty($extend['configName'])) {
                $field['options'] = new CodeItem();
                $field['options']->addUse('beacon\Config');
                $field['options']->setCode('Config::get(' . var_export($extend['configName'], true) . ')');
            }
        } else {
            $field['options'] = empty($extend['options']) ? [] : $extend['options'];
        }
    }
}