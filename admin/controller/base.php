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
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
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