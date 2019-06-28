<?php
namespace core\process;

use core\Di;

/**
 * 任务执行进程
 * Class taskRunProcess
 * @package core\process
 */
class taskRunProcess extends abstractProcess
{

    //进程销毁
    public function onShutdown()
    {
        echo "cron onShutdown";
    }

    public function run($arg)
    {
        //TODO  开启进程读取
        print_r("发现任务".json_encode($arg,JSON_UNESCAPED_UNICODE));
        swoole_timer_after($arg['task_next_exec_time'] - time(), function () use ($arg) {
            echo "执行任务...".PHP_EOL;
            echo PHP_EOL . "测试成功,调用命令->" . $arg['cmd'] . "执行时间:" . date("Y-m-d H:i:s") . PHP_EOL;
        });
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
        echo "receive:" . $str;
        Di::shareInstance()->get(Di::DI_LOG)->log("接受管道：" . $str);
    }

}