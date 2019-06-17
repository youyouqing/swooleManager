<?php
/**
 * Created by PhpStorm.
 * User: zhangmin
 * Date: 2019/5/28
 * Time: 11:45 PM
 */
namespace app\process;
use core\Di;
use core\process\src\process;

class processtask extends process
{

    //进程执行任务
    public function onStart($process)
    {
        Di::shareInstance()->get("LOG")->log("onStart");
            $process->exec("/usr/local/bin/php",[
                "/var/www/html/swooleManagergit/test.php",
            ]);
            Di::shareInstance()->get("LOG")->log("exec");
    }

    //接受管道消息
    public function onPipRead($processMsg)
    {
        Di::shareInstance()->get("LOG")->log("接受管道信息111：".$processMsg);
    }



}