<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/9
 * Time: 20:02
 */

namespace core\server;


use app\common\result;
use core\CronManager;
use core\Di;
use core\loader;
use core\ServerManager;

class http
{
    //回调方法
    const CALLBACK_REQUEST = "request";
    const CALLBACK_WORKERSTART = "WorkerStart";
    const CALLBACK_PIPEMESSAGE = "pipeMessage";
    const CALLBACK_TASK = "Task";

    static $instance;
    private $httpServer;
    private $setting;

    static public function shareInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 服务配置初始化
     * @param $configServer
     */
    public function httpInit($configServer)
    {
        $this->setting = $configServer;
        $this->beforeRequest();
        $this->httpServer = new \Swoole\Http\Server($this->setting['host'], $this->setting['port']);
        $this->httpSetting($this->setting);
        $this->httpCallback();
        $this->afterRequest();
        return $this->httpServer;
    }

    /**
     * 服务启动
     */
    public function httpStart()
    {

    }

    private function httpSetting($setting)
    {
        $this->httpServer->set([
            'document_root' => PUBLIC_PATH, // v4.4.0以下版本, 此处必须为绝对路径
            'enable_static_handler' => true,
            'pid_file' => PID_FILE,
            'daemonize' => $setting['daemonize'],
            'task_worker_num' => $setting['task_worker_num'],
        ]);
    }

    public function httpCallback()
    {
        $this->httpServer->on(self::CALLBACK_REQUEST, 'core\\server\\http::onRequest');
        $this->httpServer->on(self::CALLBACK_WORKERSTART, 'core\\server\\http::onWorkerStart');
        $this->httpServer->on(self::CALLBACK_PIPEMESSAGE, 'core\\server\\http::onPipeMessage');
        $this->httpServer->on(self::CALLBACK_TASK, 'core\\server\\http::onTask');
    }

    public function beforeRequest()
    {
        //TODO
    }

    public function afterRequest()
    {
        //TODO
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
        if ($worker_id == 2) {
            swoole_timer_tick(1 * 1000 , function () use ($serv) {
                CronManager::shareInstance()->sendTaskProcess($serv);
            });
        }


//        if ($worker_id == 0) {
//            $serv->tick(1000, function ($id) use ($serv,$worker_id){
                //TODO   热更新
                //$serv->reload();
//                echo $worker_id.PHP_EOL;
//            });
//        }
    }


    static public function onPipeMessage($serv, $src_worker_id, $data)
    {
        if ($src_worker_id == 1) {
            echo "#{$serv->worker_id} message from #$src_worker_id: $data\n";
        }
    }

    static public function onTask($serv, $task_id, $worker_id, $data)
    {
//        echo "#{$serv->worker_id}\tonTask: worker_id={$worker_id}, task_id=$task_id\n";

        if ($worker_id == 2) {
//            $data = json_decode($data,true);
            Di::shareInstance()->get("LOG")->log($data);
//            print_r($data.PHP_EOL);
            return $data;
        }
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