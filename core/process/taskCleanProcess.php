<?php


namespace core\process;

use core\Di;

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
        echo __CLASS__.PHP_EOL;
        print_r($arg);
    }

    public function onReceive(string $str)
    {

    }

}