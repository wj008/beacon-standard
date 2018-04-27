<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/4/23
 * Time: 1:14
 */

namespace sdopx\plugin;


class ThumbnailModifier
{
    public static function execute($image, $size, $mode = 1)
    {
        if (!preg_match('@^(.*\/upfiles\/images\/\d+)\.(jpg|jpeg|png|gif)$@', $image, $match)) {
            return $image;
        }
        return $match[1] . '_' . $size . '_' . $mode . '.' . $match[2];
    }
}