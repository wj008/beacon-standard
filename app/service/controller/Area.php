<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/20
 * Time: 20:22
 */

namespace app\service\controller;


use beacon\Controller;
use beacon\DB;
use beacon\Route;

class Area extends Controller
{

    public function indexAction($code = '0')
    {
        $data = [];
        $list = DB::getList('select * from @pf_area where parent_code=?', $code);
        foreach ($list as $item) {
            $data[] = ['value' => $item['code'], 'text' => $item['name'], 'childs' => Route::url('~/area', ['code' => $item['code']])];
        }
        $this->success('ok', $data);
    }

}