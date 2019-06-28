<?php


namespace core\process;

use core\Di;
use core\ServerManager;
use core\TableManager;
use core\task\task;
use Cron\CronExpression;

/**
 * 独立进程
 * 1.任务同步
 * 2.每分钟执行一次，产生下一分钟需要执行的任务
 *
 * Class taskAsyncProcess
 * @package core\process
 */
class taskAsyncProcess extends abstractProcess
{
    public function onShutdown()
    {

    }

    public function run($arg)
    {
        swoole_timer_tick(2 * 1000, function (){
            echo "同步任务...".PHP_EOL;
            $this->prepareTask();
        });

    }

    private function prepareTask()
    {
        task::shareInstance()->prepareTables();
    }

    public function onReceive(string $str)
    {

    }

}