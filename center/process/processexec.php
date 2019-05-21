<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/21
 * Time: 18:53
 */
namespace center\process;

use core\process\src\process;

class processexec extends process
{
    //进程执行任务
    public function onStart($process)
    {
        print_r($process);
        echo "begin";
    }

    //接受管道消息
    public function onPipRead($processMsg)
    {
        echo "222222:".$processMsg;
    }

}