<?php


namespace core\process;

use core\CronManager;
use core\Di;
use core\TableManager;
use core\task\task;

/**
 * 任务加载内存进程
 * Class taskRunProcess
 * @package core\process
 */
class taskLoadProcess extends abstractProcess
{

    //进程销毁
    public function onShutdown()
    {
        echo "cron onShutdown";
    }

    public function run($arg)
    {
        //每20秒加载完一次任务后，生成1分钟后需要执行的任务
        swoole_timer_tick(5 * 1000 , function (){
            echo "加载任务...".PHP_EOL;
            $this->loadTask();
        });
    }

    public function onReceive(string $str)
    {

    }

    private function loadTask()
    {
        task::shareInstance()->loadTables($this->loadDb());
    }

    /**
     * 数据库取值任务
     */
    private function loadDb()
    {
        $taskList = [
            [
                "id"   => 1,
                "rule" => "* * * * *",
                "excute_times" => 0,
                "cmd" => "usr/bin/php -f /home/docker-project/www/html/swooleManager/atestt.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "status" => 1 //激活
            ],
            [
                "id"   => 2,
                "rule" => "*/30 * * * *",
                "excute_times" => 0,
                "cmd" => "usr/bin/php -f /home/docker-project/www/html/swooleManager/test1.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "status" => 1 //激活
            ],
            [
                "id"   => 3,
                "rule" => "*/10 * * * *",
                "excute_times" => 0,
                "cmd" => "usr/bin/php -f /home/docker-project/www/html/swooleManager/test1.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "status" => 3 //删除
            ],
            [
                "id"   => 3,
                "rule" => "* */10 * * *",
                "excute_times" => 0,
                "cmd" => "usr/bin/php -f /home/docker-project/www/html/swooleManager/test1.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "status" => 3 //删除
            ]
        ];
        $tasks = [];
        foreach ($taskList as $item){
            $tasks[$item['id']] = $item;
        }
        return $tasks;
    }

}