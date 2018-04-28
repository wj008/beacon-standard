<?php
/**
 * Created by PhpStorm.
 * User: wj008
 * Date: 2018/2/20
 * Time: 15:15
 */

namespace app\tool\libs;


class CodeItem
{
    public $code = '';
    public $use = [];


    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function addUse($name)
    {
        $this->use[$name] = $name;
    }

}