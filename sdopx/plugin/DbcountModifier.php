<?php

namespace sdopx\plugin;


use beacon\Console;
use beacon\DB;

class DbcountModifier
{
    private static $cache = [];

    public static function execute($string, string $tbname, string $where = '')
    {
        if (empty($string)) {
            return '';
        }
        $sql = 'select count(1) as mc from `' . $tbname . '`';
        if (!empty($where)) {
            $sql .= ' where ' . $where;
        } else {
            $sql .= ' where id=?';
        }
        $key = md5($tbname . '|' . $where . '|' . $string);
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        $row = DB::getRow($sql, $string);
        if (!$row) {
            self::$cache[$key] = 0;
            return self::$cache[$key];
        } else {
            self::$cache[$key] = isset($row['mc']) ? $row['mc'] : 0;
            return self::$cache[$key];
        }
    }
}