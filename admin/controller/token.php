<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/22
 * Time: 16:07
 */

namespace admin\controller;


use core\Di;

class token extends base
{
    public function __construct()
    {

    }

    public function index()
    {
        return $this->resultJson(200,Di::shareInstance()->get("config.redis"),'');
    }
}