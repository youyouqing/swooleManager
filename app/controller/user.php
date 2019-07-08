<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 11:58
 */

namespace app\controller;

use core\Di;
use app\model\user as userModel;

/**
 * 用户管理
 * Class user
 * @package app\controller
 */
class user extends base
{
    protected $need_token = false;

    public $model = null;

    public function init()
    {
        $this->model = new userModel();
    }

    /**
     * 注册
     * @return \app\common\result
     */
    public function register()
    {
        $this->filterRequestFields(["user_name", "password"]);
        $existRes = userModel::where([
            "user_name" => $this->serverParams('user_name'),
            "status"    => 1,//激活用户
        ])->find();
        if ($existRes) {
            return $this->resultJson(-1,false,$this->serverParams('user_name')."已存在");
        }
        //INSERT INTO `t_user` (`id`, `user_name`, `email`, `password`, `salt`, `last_login`, `last_ip`, `status`)
        $params = $this->serverParams();
        $params['password'] = md5($params['password']);
        $params['last_ip'] = $this->getRempteAddr();
        $params['status'] = 1;
        return $this->resultJson(0,$this->model->allowField([
            'user_name','email','password','status','last_ip'
        ])->save($params),"");

    }

    /**
     * 登录
     * @return \app\common\result
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login()
    {
        $params = $this->filterRequestFields(["user_name","password"]);
        $existRes = $this->model->where([
            "user_name" => $params['user_name'],
            "password"    => md5($params['password']),
        ])->find();
        if (!$existRes) {
            return $this->resultJson(-1,false,"用户名或密码不正确");
        }
        if ($existRes['status'] != 1) {
            return $this->resultJson(-1,false,"账号被冻结");
        }
        $token = md5($existRes['user_name'].time());
        $redisKey = 'token|'.$token;
        Di::shareInstance()->get(Di::DI_REDIS)->set($redisKey,$existRes['id']);
        Di::shareInstance()->get(Di::DI_REDIS)->expire($redisKey,self::TOKEN_AUTH_EXPIRE);
        return $this->resultJson(0, [
            "token" => $token
        ],"OK");
    }

    /**
     * 登出
     * @return \app\common\result
     */
    public function logout()
    {
        Di::shareInstance()->get("REDIS")->del('token|'.$this->token);
        return $this->resultJson(0, true,"登出成功");
    }

}