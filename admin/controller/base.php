<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 18:49
 */

namespace admin\controller;


use admin\common\result;

class base
{
    public function __construct(){}


    public function resultJson($code,$data,$msg)
    {
        return new result($code,$data,$msg);
    }
}