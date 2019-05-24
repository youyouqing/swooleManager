<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 11:58
 */

namespace admin\controller;

use core\Di;
use admin\model\user as userModel;

/**
 * 用户管理
 * Class user
 * @package admin\controller
 */
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

    /**
     * 注册
     */
    public function register()
    {
        //INSERT INTO `t_user` (`id`, `user_name`, `email`, `password`, `salt`, `last_login`, `last_ip`, `status`)
        return $this->resultJson(0,$this->serverParams(),"");
    }

//    public function


}