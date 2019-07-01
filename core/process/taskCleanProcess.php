<?php


namespace core\process;

use core\Di;
use core\ServerManager;

/**
 * 独立进程
 * 1.任务清理
 * 2.每5分钟执行一次，处理超时任务，已被删除的任务
 *
 * Class taskAsyncProcess
 * @package core\process
 */
class taskCleanProcess extends abstractProcess
{
    public function onShutdown()
    {

    }

    public function run($arg)
    {
//         ServerManager::shareInstance()->getSwooleServer()->taskCo(["name"=>"dzc"]);
    }

    public function onReceive(string $str)
    {

    }

}