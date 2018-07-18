<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/24
 * Time: 18:23
 */

namespace app\tool\libs;


class Helper
{
    public static function export($data, $sp = '')
    {
        $tabs[] = '[';
        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $tabs[] = $sp . '    ' . var_export($key, true) . ' => ' . self::export($item, $sp . '    ') . ',';
            } else {
                $tabs[] = $sp . '    ' . var_export($key, true) . ' => ' . var_export($item, true) . ',';
            }
        }
        $tabs[] = $sp . ']';
        return join("\n", $tabs);
    }

}