<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 11:58
 */

namespace admin\controller;

use admin\process\processexec;
use core\Di;
use admin\model\user as userModel;
class user extends base
{

    public function index()
    {
        return $this->resultJson(0,Di::shareInstance()->get("config.database"),"OK");
    }

    public function index1()
    {
        $res = userModel::select();
        return $this->resultJson(0,$res,"OK");
    }


}