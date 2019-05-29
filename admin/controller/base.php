<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/8
 * Time: 18:49
 */

namespace admin\controller;


use admin\common\result;
use core\Di;
use core\http;

class base
{
    public $request;
    public $response;
    public $custome;
    public $diModel;

    public $user;
    public $token;

    protected $need_token = true;

    private $skipMap = [
        'user' => [
            'login',
            'register',
        ]
    ];

    public function __construct($request , $response , $custome)
    {
        $this->request = $request;
        $this->response = $response;
        $this->custome = $custome;
        $this->need_token and $this->handle_token();
        $this->init();
    }

    public function init(){}

    public function handle_token()
    {
        $token = $this->serverParams('token');
        $controller = $this->custome['controller'];
        $method = $this->custome['method'];
        $this->token = $token;
        $skipToken = false;
        if (in_array($controller,array_keys($this->skipMap))) {
            if (in_array($method , array_values($this->skipMap[$controller]))) {
                //跳过token验证
                $skipToken = true;
            }
        }
        if (!$skipToken) {
            if (empty($token)) {
                return $this->tokenError("token不能为空");
            }
            $userId = Di::shareInstance()->get("REDIS")->get('token|'.$token);
            $userRes = \admin\model\user::where(['id' => $userId])->find();
            if (empty($userRes)) {
                return $this->tokenError("token非法");
            }
            if ($userRes['status'] != 1) {
                return $this->tokenError("用户被冻结");
            }
            $this->user = $userRes;
        }
    }

    private function tokenError($msg)
    {
        return http::responseHandle($this->response , $this->resultJson( -10 , false , $msg));
    }

    /**
     * 注入di模型
     * @return bool|mixed
     * @throws \ReflectionException
     */
    private function setModelDi()
    {
        if (!Di::shareInstance()->get('model.'.$this->custome['controller'])) {
            $class = new \ReflectionClass("\\admin\\model\\".$this->custome['controller']);
            Di::shareInstance()->set('model'.$this->custome['controller'],$class->newInstance());
        }
        return Di::shareInstance()->get('model.'.$this->custome['controller']);
    }


    public function resultJson($code,$data,$msg)
    {
        return new result($code,$data,$msg);
    }

    public function getServer($key = false)
    {
        return $key ? $this->request->server[$key] : $this->request->server;
    }

    public function getHeader($key = false)
    {
        return $key ? $this->request->header[$key] : $this->request->header;
    }

    public function getRequestGet($key = false)
    {
        return $key ? $this->request->get[$key] : $this->request->get;
    }

    public function getRequestPost($key = false)
    {
        return $key ? $this->request->post[$key] : $this->request->post;
    }

    public function getParams($key = false)
    {
        return $this->getRequestGet($key);
    }

    public function postParams($key = false)
    {
        return $this->getRequestPost($key);
    }

    public function serverParams($key = false)
    {
        if ($key) {
            return $this->getParams($key) ? $this->getParams($key) : $this->postParams($key);
        }
        return array_merge($this->getParams($key) ?? [] , $this->postParams($key) ?? []);
    }


    /**
     * getMethod
     * @return mixed
     */
    public function getMethod()
    {
        return $this->getServer('request_method');
    }

    /**
     * getRempteAddr
     * @return mixed
     */
    public function getRempteAddr()
    {
        return $this->getServer('remote_addr');
    }

    /**
     * getServerPort
     * @return mixed
     */
    public function getServerPort()
    {
        return $this->getServer('server_port');
    }


    /**
     * getPathInfo
     * @return mixed
     */
    public function getPathInfo()
    {
        return $this->getServer('path_info');
    }


    /**
     * getHost
     * @return mixed
     */
    public function getHost()
    {
        return $this->getHeader('host');
    }


    /**
     * getUserAgent
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->getServer('user-agent');
    }


}