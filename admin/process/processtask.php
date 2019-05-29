<?php
/**
 * Created by PhpStorm.
 * User: zhangmin
 * Date: 2019/5/28
 * Time: 11:45 PM
 */
namespace admin\process;
use core\process\src\process;

class processtask extends process
{

    //进程执行任务
    public function onStart($process , $task)
    {
//        print_r($task);
//        \Swoole\Timer::tick(1000, function() use ($process){
            $res = $process->exec("/usr/local/bin/php",[
                "/var/www/html/swooleManagergit/test.php",
            ]);
            echo $res;
           echo $process->write($res);
//        });

    }

    //接受管道消息
    public function onPipRead($processMsg)
    {
        print_r("接受到管道消息".$processMsg);
    }

}