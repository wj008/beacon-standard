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
use beacon\Route;
use beacon\Utils;

class MultipleModule extends Form implements ModuleInterface
{
    protected function load()
    {
        return [
            'custom-line' => [
                'label' => '插件专属配置项',
                'type' => 'line',
            ],
            'dataHref' => [
                'label' => '对话框地址',
            ],
            'dataText' => [
                'label' => '选项值文本',
                'type' => 'textarea'
            ],
            'default_btn' => [
                'label' => '高级设置',
                'type' => 'button',
                'data-maxmin' => 'false',
                'data-width' => '700',
                'data-height' => '360',
                'box-style' => 'vertical-align: top;',
                'box-href' => Route::url('~/tool_field/text_set'),
                'box-yee-module' => 'dialog',
                'box-onsuccess' => 'if(ret){$(\'#dataText\').val(ret.code);}',
                'box-onbefore' => '$(this).data(\'assign\',$(\'#dataText\').val());',
                'view-merge' => -1,
            ],
            'itemType' => [
                'label' => 'item值类型',
                'type' => 'select',
                'options' => [
                    ['integer', 'integer'],
                    ['string', 'string'],
                    ['float', 'float'],
                ],
            ],
        ];
    }

    public static function exportField(MakeInterface $maker, array &$field, array $extend)
    {
        if (!empty($field['default']) && Utils::isJsonString($field['default'])) {
            $field['default'] = json_decode($field['default'], 1);
        }

        foreach ($extend as $key => $item) {
            if ($key == 'dataText') {
                if (isset($item[0]) && $item[0] == '@') {
                    $data = substr($item, 1);
                    if (Utils::isJsonString($data)) {
                        $temp = json_decode($data, true);
                        if (isset($temp['sql']) && isset($temp['field'])) {
                            $item = new CodeItem();
                            $item->addUse('beacon\DB');
                            $code = [];
                            $code[] = 'function($value=0){';
                            $code[] = '    $row = DB::getRow(' . var_export(trim($temp['sql']), true) . ',$value);';
                            $okey = trim($temp['field']);
                            if (!empty($okey)) {
                                $code[] = '    $text = ($row == null || !isset($row[' . var_export($okey, true) . '])) ? null : $row[' . var_export($okey, true) . '];';
                            } else {
                                $code[] = '    $row = $row == null ? [] : array_values($row);';
                                $code[] = '    $text = isset($row[0]) ? null : $row[0];';
                            }
                            $code[] = '    return $text;';
                            $code[] = '}';
                            $item->setCode(join("\n", $code));
                            $field['textFunc'] = $item;
                            continue;
                        } elseif (isset($temp['func'])) {
                            if (is_callable($temp['func'])) {
                                $item = new CodeItem();
                                $code = 'function($value=0){ if(is_callable(' . var_export($temp['func'], true) . ')){return ' . $temp['func'] . '($value);} return null;}';
                                $item->setCode($code);
                                $field['textFunc'] = $item;
                                continue;
                            } else {
                                $item = null;
                            }
                        }
                    }
                }
            }
            if ($key == 'dataHref') {
                if (isset($item[0]) && ($item[0] == '~' || $item[0] == '^')) {
                    $value = new CodeItem();
                    $value->addUse('beacon\Route');
                    $value->setCode('Route::url(' . var_export($item, true) . ')');
                    $item = $value;
                }
            }
            $field[$key] = $item;
        }
    }

}