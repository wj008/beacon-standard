<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/4/28
 * Time: 16:53
 */

namespace app\tool\libs;


interface ModuleInterface
{
    public static function exportField(MakeInterface $maker, array &$field, array $extend);
}