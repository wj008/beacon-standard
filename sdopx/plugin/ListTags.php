<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/3/18
 * Time: 0:36
 */

namespace sdopx\plugin;

use beacon\SqlSelector;
use sdopx\lib\Outer;

class ListTags
{
    public static function execute(array $param, \Closure $func, Outer $out)
    {
        $tbname = isset($param['tbname']) ? '@pf_' . $param['tbname'] : '';
        if (empty($tbname)) {
            $out->rethrow('没有填写数据库名称 tbname');
        }
        $where = isset($param['where']) ? $param['where'] : '';
        $args = isset($param['args']) ? $param['args'] : [];
        $order = isset($param['order']) ? $param['order'] : '';
        $offset = isset($param['offset']) ? $param['offset'] : 0;
        $lenght = isset($param['lenght']) ? $param['lenght'] : 10;
        $selector = new SqlSelector($tbname);
        if (!empty($where)) {
            $selector->where($where, $args);
        }
        if (!empty($order)) {
            $selector->order($order);
        }
        $selector->limit($offset, $lenght);
        $list = $selector->getList();
        foreach ($list as $item) {
            $func($item);
        }
    }
}