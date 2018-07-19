<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 18-5-17
 * Time: 上午1:44
 */

namespace sdopx\plugin;


use beacon\Console;
use sdopx\lib\Outer;
use sdopx\SdopxException;

class PagebarPlugin
{

    private static function buildQuery(array &$data, array $param, string $key, int $val)
    {
        $param[$key] = $val;
        $queryStr = http_build_query($param);
        $scheme = isset($data['scheme']) ? $data['scheme'] . '://' : '';
        $host = isset($data['host']) ? $data['host'] : '';
        $port = isset($data['port']) ? ':' . $data['port'] : '';
        $user = isset($data['user']) ? $data['user'] : '';
        $pass = isset($data['pass']) ? ':' . $data['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($data['path']) ? $data['path'] : '';
        $query = !empty($queryStr) ? '?' . $queryStr : '';
        $fragment = isset($data['fragment']) ? '#' . $data['fragment'] : '';
        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    public static function execute(array $args, Outer $out)
    {
        if (!isset($args['info'])) {
            throw new SdopxException('pagebar 控件缺少分页信息  info ');
        }
        $info = $args['info'];
        $key = empty($args['key']) ? $info['keyname'] : $args['url'];
        $link = isset($args['url']) ? $args['url'] : $_SERVER['REQUEST_URI'];
        $data = parse_url($link);
        if (isset($args['fragment'])) {
            $data['fragment'] = $args['fragment'];
        }
        $param = [];
        if (isset($data['query'])) {
            parse_str($data['query'], $param);
        }
        $page = $info['page'];
        $pageCount = $info['pageCount'];
        $start = $page - 2 >= 1 ? $page - 2 : 1;
        $end = $start + 4 <= $pageCount ? $start + 4 : $pageCount;
        if ($end - 4 != $start) {
            $start = $end - 4;
            if ($start < 1) {
                $start = 1;
            }
        }
        if ($page - 1 < 1) {
            $out->html('<a href="javascript:;" class="prev disabled">上一页</a>');
        } else {
            $out->html('<a href="' . self::buildQuery($data, $param, $key, $page - 1) . '" class="prev">上一页</a>');
        }
        if ($start - 1 >= 1) {
            $out->html('<a href="' . self::buildQuery($data, $param, $key, 1) . '" class="num">1</a>');
            if ($start - 2 >= 1) {
                $out->html('<span class="more">...</span>');
            }
        }
        for ($i = $start; $i <= $end; $i++) {
            $out->html('<a href="' . self::buildQuery($data, $param, $key, $i) . '" class="num');
            if ($page == $i) {
                $out->html(' on');
            }
            $out->html('">' . $i . '</a>');
        }
        if ($end + 1 <= $pageCount) {
            if ($end + 2 <= $pageCount) {
                $out->html('<span class="more">...</span>');
            }
            $out->html('<a href="' . self::buildQuery($data, $param, $key, $pageCount) . '" class="num">' . $pageCount . '</a>');
        }
        if ($page + 1 > $pageCount) {
            $out->html('<a href="javascript:;" class="next disabled">下一页</a>');
        } else {
            $out->html('<a href="' . self::buildQuery($data, $param, $key, $page + 1) . '" class="next">下一页</a>');
        }

    }
}