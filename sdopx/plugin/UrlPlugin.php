<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/1/2
 * Time: 21:40
 */

namespace sdopx\plugin;


use beacon\Route;
use sdopx\lib\Outer;


class UrlPlugin
{
    public static function execute(array $param, Outer $out)
    {
        $uri = isset($param['path']) ? $param['path'] : '~/' . Route::get('ctl') . '/' . Route::get('act');
        if (!isset($param['path'])) {
            $ctl = isset($param['ctl']) ? $param['ctl'] : Route::get('ctl');
            $act = isset($param['act']) ? $param['act'] : Route::get('act');
            $uri = '~/' . $ctl . '/' . $act;
        }
        $args = (isset($param['args']) && is_array($param['args'])) ? $param['args'] : [];
        unset($param['path']);
        unset($param['ctl']);
        unset($param['act']);
        unset($param['args']);
        $args = array_merge($param, $args);
        $out->text(Route::url($uri, $args));
    }
}