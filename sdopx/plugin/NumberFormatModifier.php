<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/4/16
 * Time: 18:20
 */

namespace sdopx\plugin;

setlocale(LC_MONETARY, 'en_US.UTF-8');

class NumberFormatModifier
{
    public static function execute($number, int $decimals = 2, string $dec_point = ".", string $thousands_sep = ",")
    {
        $number = floatval($number);
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }
}