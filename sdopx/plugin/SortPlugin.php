<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 18-7-19
 * Time: 上午5:28
 */

namespace sdopx\plugin;


use beacon\Route;
use sdopx\lib\Outer;

class SortPlugin
{
    public static function execute(array $param, Outer $out)
    {
        $param['sort'] = isset($param['sort']) ? $param['sort'] : 0;
        if (!isset($param['id'])) {
            $out->rethrow('没有填写记录 id');
        }
        $name = empty($param['name']) ? 'sort' : $param['name'];
        $url = Route::url('~/' . Route::get('ctl') . '/sort', ['id' => $param['id']]);
        $out->html('<input value="' . $param['sort'] . '" name="' . $name . '" type="text" class="form-inp snumber tc" yee-module="integer editbox" data-href="' . $url . '"/>');
    }
}