<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/9
 * Time: 20:02
 */

namespace core\server;


use app\common\result;
use core\loader;

class http
{

    static public function beforeRequest()
    {

    }

    static public function afterRequest()
    {

    }

    static public function onRequest($request, $response)
    {
        //filter google icon
        if ($request->server['request_uri'] == '/favicon.ico') {
            return;
        }
        loader::load();
        $path_info = $request->server['path_info'];
        $controller = self::getController($path_info);
        $method = self::getMethod($path_info);
        $class = new \ReflectionClass("\\app\\controller\\".$controller);
        $custome['controller'] = $controller;
        $custome['method'] = $method;
        $instance = $class->newInstance($request , $response, $custome);
        try {
            $class->getMethod($method);
        }catch (\Exception $exception){
            return self::responseHandle($response,(new result("10000",false,$exception->getMessage())));
        }
        if (!$class->getMethod($method)->isPublic()) {
            return self::responseHandle($response,(new result("10000",false,"无权访问")));
        }
        try {
            $result = $class->getMethod($method)->invoke($instance);
        }catch (\Exception $exception) {
            return self::responseHandle($response,(new result("10000",false,$exception->getMessage())));
        }
        //默认json输出
        return self::responseHandle($response,$result);
    }

    static public function onWorkerStart($serv, $worker_id)
    {
        $serv->tick(1000, function ($id) use ($serv) {
            //TODO   热更新

            //$serv->reload();
        });
    }


    static public function getController($path_info)
    {
        $path_info_arr = explode("/",$path_info);
        if (count($path_info_arr) <= 2) {
            return "index";
        }
        return $path_info_arr[1];
    }

    static public function getMethod($path_info)
    {
        $path_info_arr = explode("/",$path_info);
        if (count($path_info_arr) <= 2) {
            return "index";
        }
        return $path_info_arr[2];
    }

    static public function responseHandle($response,$result)
    {
        $response->header('content-type', 'application/json; charset=utf-8', true);
        $response->end(json_encode((array)$result,JSON_UNESCAPED_UNICODE));
    }
}