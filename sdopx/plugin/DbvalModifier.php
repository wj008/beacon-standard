<?php

namespace sdopx\plugin;


use beacon\Console;
use beacon\DB;

class DbvalModifier
{
    private static $cache = [];

    public static function execute($string, string $tbname, string $field = '', string $where = '')
    {
        if (empty($field)) {
            $field = 'name';
        }
        if (empty($string)) {
            return '';
        }
        $sql = 'select `' . $field . '` from `' . $tbname . '`';
        if (!empty($where)) {
            $sql .= ' where ' . $where;
        } else {
            $sql .= ' where id=?';
        }
        $key = md5($tbname . '|' . $field . '|' . $where . '|' . $string);
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        $row = DB::getRow($sql, $string);
        if (!$row) {
            self::$cache[$key] = '';
            return self::$cache[$key];
        } else {
            self::$cache[$key] = isset($row[$field]) ? $row[$field] : '';
            return self::$cache[$key];
        }
    }
}