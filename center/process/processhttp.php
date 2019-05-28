<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/21
 * Time: 18:53
 */

namespace center\process;

use core\process\src\process;

class processhttp extends process
{
    //进程执行任务
    public function onStart($process)
    {
        //开启http服务   http和调度中心一体化
        $process->exec("/usr/local/bin/php",[
            ADMIN_PATH."/index.php",
            "http"
        ]);
    }

    //接受管道消息
    public function onPipRead($processMsg)
    {
        echo "222222:" . $processMsg;
    }

}