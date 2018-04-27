<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/1/2
 * Time: 21:40
 */

namespace sdopx\plugin;


use beacon\Field;
use sdopx\lib\Outer;


class BoxPlugin
{
    public static function execute(array $param, Outer $out)
    {
        $field = isset($param['field']) ? $param['field'] : new Field(null, $param);
        unset($param['field']);
        $code = [];
        if ($field->beforeText) {
            $code[] = '<span class="before">' . htmlspecialchars($field->beforeText) . '</span>';
        }
        if ($field->type == 'check') {
            $code[] = '<label>';
        }
        $code[] = $field->box($param);
        if ($field->afterText) {
            $code[] = '<span class="after">' . htmlspecialchars($field->afterText) . '</span>';
        }
        if ($field->type == 'check') {
            $code[] = '</label>';
        }
        $out->html(join('', $code));
    }
}