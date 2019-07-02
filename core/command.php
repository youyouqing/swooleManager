<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/9
 * Time: 19:23
 */

namespace core;

use core\task\task;
use core\server\http;

class command
{

    public function __construct()
    {
    }

    /**
     * 命令行处理方法
     * @param $argv
     */
    static public function handle($argv)
    {
        if (empty($argv) || count($argv) < 3) {
            self::help();
        }
        switch ($argv[1]) {
            case "http":
                self::httpHandle($argv);
                break;

            case "tcp":
            case "websocket":
            default:
                self::help();
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
                //初始化http服务 并托管至Server管理器
                ServerManager::shareInstance()->setSwooleServer(http::shareInstance()->httpInit(Di::shareInstance()->get(Di::DI_CONFIG.".server")));
                //注册初始化内存表
                TableManager::shareInstance()->initTables([task::TABLE_NAME_TASK,task::TABLE_RUN_TASK] , task::shareInstance()->getTablesRules());
                //启用主任务
                ServerManager::shareInstance()->getSwooleServer()->start();

                break;


            case "reload" :
                $httpServer = ServerManager::shareInstance()->getSwooleServer();
                if (!$httpServer) {
                    die("服务不存在");
                }
                $httpServer->reload();
                break;
            default:
                self::help();
                break;
        }

    }

    static public function help()
    {
        echo <<<HELP
        
-----------------------------------------------      
|目前只支持http服务                            |
|服务启动命令：php app/index.php http start    |
|服务重载命令：php app/index.php http reload   |
-----------------------------------------------  

HELP;
    exit();}
}
