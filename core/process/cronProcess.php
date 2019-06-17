<?php
namespace core;

use core\process\src\process;

class cronProcess extends process\AbstractProcess
{
    //进程销毁
    public function onShutdown()
    {
        echo "cron onShutdown";
    }

    public function run($arg)
    {
        swoole_timer_tick(1000,function (){
           $this->getProcess()->exec("/usr/bin/php",[
               "-m",
           ]);
        });

        // TODO: Implement run() method.
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
        echo "receive:".$str;
    }
}