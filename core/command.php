<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/9
 * Time: 19:23
 */

namespace core;

use core\task\task;

class command
{

    public function __construct()
    {
    }

    static public function handle($argv)
    {
        if (empty($argv) || count($argv) < 3) {
            exit("命令行格式不正确");
        }
        switch ($argv[1]) {
            case "http":
                self::httpHandle($argv);
                break;

            case "tcp":
            case "websocket":
            default:
                exit("目前只支持http");
        }
    }

    /**
     * 处理http命令
     * @param $argv 暂时保留 后续动态配置项
     */
    static private function httpHandle($argv)
    {
        switch ($argv[2]) {
            case "start" :
                $configServer = Di::shareInstance()->get(Di::DI_CONFIG.".server");

                \core\server\http::beforeRequest();
                $httpServer = new \Swoole\Http\Server($configServer['host'], $configServer['port']);

                $httpServer->set([
                    'document_root' => PUBLIC_PATH, // v4.4.0以下版本, 此处必须为绝对路径
                    'enable_static_handler' => true,
                    'pid_file' => PID_FILE,
                    'daemonize' => $configServer['daemonize']
                ]);
                $httpServer->on('request', 'core\\server\\http::onRequest');
                $httpServer->on('WorkerStart', 'core\\server\\http::onWorkerStart');
                $httpServer->on('pipeMessage', 'core\\server\\http::onPipeMessage');
                ServerManager::shareInstance()->setSwooleServer($httpServer);
                //注册初始化内存表
                TableManager::shareInstance()->initTables([task::TABLE_NAME_TASK,task::TABLE_RUN_TASK] , task::shareInstance()->getTablesRules());
                //加载任务
                CronManager::shareInstance()->taskLoadProcess();
//                //同步一分钟之后执行的任务
                CronManager::shareInstance()->taskAsyncProcess();
//                //执行任务
                CronManager::shareInstance()->taskRunProcess();


                //启用主任务
                ServerManager::shareInstance()->getSwooleServer()->start();
                break;


            case "reload" :
                //TODO 后续做服务重启
                $httpServer = ServerManager::shareInstance()->getSwooleServer();
                if (!$httpServer) {
                    die("服务不存在");
                }

                $httpServer->reload();
                break;
            default:
                die("参数错误");
                break;
        }

    }
}
