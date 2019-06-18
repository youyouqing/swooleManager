<?php


namespace core\process;

use core\Di;

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

    }

    public function onReceive(string $str)
    {

    }

}