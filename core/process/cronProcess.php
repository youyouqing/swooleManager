<?php
namespace core\process;

use core\Di;

/**
 * 任务执行进程
 * Class cronProcess
 * @package core\process
 */
class cronProcess extends abstractProcess
{

    //进程销毁
    public function onShutdown()
    {
        echo "cron onShutdown";
    }

    public function run($arg)
    {
        //TODO  开启进程读取
        swoole_timer_after($arg['task_next_exec_time'] - time(), function () use ($arg) {
            echo PHP_EOL . "测试成功,调用命令->" . $arg['cmd'] . "执行时间:" . date("Y-m-d H:i:s") . PHP_EOL;
//            $this->getProcess()->write("1111");
//            Di::shareInstance()->get(Di::DI_LOG)->log('1111');
        });
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
        echo "receive:" . $str;
        Di::shareInstance()->get(Di::DI_LOG)->log("接受管道：" . $str);
    }

}