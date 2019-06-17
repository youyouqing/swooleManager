<?php


namespace core\process;

class cronprocess extends abstractprocess
{

    //进程销毁
    public function onShutdown()
    {
        echo "cron onShutdown";
    }

    public function run($arg)
    {
        //TODO  开启进程读取
        swoole_timer_after($arg['task_next_exec_time'] - time() , function () use ($arg){
            echo PHP_EOL."测试成功,调用命令->".$arg['cmd']."执行时间:".date("Y-m-d H:i:s").PHP_EOL;
        });
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
        echo "receive:".$str;
    }

}