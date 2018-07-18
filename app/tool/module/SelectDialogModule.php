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

class SelectDialogModule extends Form implements ModuleInterface
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

            'dataWidth' => [
                'label' => '对话框宽',
                'type' => 'integer',
                'default' => 0,
            ],

            'dataHeight' => [
                'label' => '对话框高',
                'type' => 'integer',
                'default' => 0,
                'view-merge' => -1,
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

            'dataBtnText' => [
                'label' => '按钮文本',
                'type' => 'text'
            ],

            'dataClearBtn' => [
                'label' => '清除按钮',
                'type' => 'check',
                'afterText' => '勾选显示清除按钮'
            ],

        ];
    }

    public static function exportField(MakeInterface $maker, array &$field, array $extend)
    {
        foreach ($extend as $key => $item) {

            if ($key == 'dataText') {
                if (isset($item[0]) && $item[0] == '@') {
                    $data = substr($item, 1);
                    if (Utils::isJsonString($data)) {
                        $temp = json_decode($data, true);
                        if (isset($temp['sql']) && isset($temp['field'])) {
                            $citem = new CodeItem();
                            $citem->addUse('beacon\DB');
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
                            $citem->setCode(join("\n", $code));
                            $field['textFunc'] = $citem;
                            continue;
                        } elseif (isset($temp['func'])) {
                            if (is_callable($temp['func'])) {
                                $citem = new CodeItem();
                                $code = 'function($value=0){ if(is_callable(' . var_export($temp['func'], true) . ')){return ' . $temp['func'] . '($value);} return null;}';
                                $citem->setCode($code);
                                $field['textFunc'] = $citem;
                                continue;
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