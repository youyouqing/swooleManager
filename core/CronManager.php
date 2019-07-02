<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 19-6-18
 * Time: 下午7:06
 */

namespace core;

use core\process\taskAsyncProcess;
use core\process\taskCleanProcess;
use core\process\taskLoadProcess;
use core\process\taskRunProcess;
use core\task\task;
use Cron\CronExpression;

/**
 * 定时任务管理
 * Class CronManager
 * @package core
 */
class CronManager
{
    static $instance;

    static public function shareInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 投递任务
     * @param $taskId
     * @param $task
     */
    public function sendTaskProcess($serv)
    {
        task::shareInstance()->loadTables($this->loadDb() , $serv);
    }

    /**
     * 读取内存进程
     * @param $taskId
     * @param $task
     */
    public function taskLoadProcess()
    {
        $taskLoadProcess = new taskLoadProcess("php-taskLoadProcess");
        $this->collectionProcess($taskLoadProcess->getProcess());
    }


    /**
     * 执行进程
     * @param $taskId
     * @param $task
     */
    public function taskRunProcess()
    {
        $taskRunProcess = new taskRunProcess("php-runTaskProcess-");
        $this->collectionProcess($taskRunProcess->getProcess());
    }

    /**
     * 同步进程
     * @param $taskId
     * @param $task
     */
    public function taskAsyncProcess()
    {
        $taskAsyncProcess = new taskAsyncProcess("php-taskAsyncProcess");
        $this->collectionProcess($taskAsyncProcess->getProcess());
    }

    /**
     * 清理进程
     * @param $taskId
     * @param $task
     */
    public function taskCleanProcess()
    {
        $taskCleanProcess = new taskCleanProcess("php-taskCleanProcess");
        $this->collectionProcess($taskCleanProcess->getProcess());
    }


    /**
     * 往swoole主进程添加子进程
     * @param $process
     */
    private function collectionProcess($process)
    {
        ServerManager::shareInstance()->getSwooleServer()->addProcess($process);
    }

    /**
     * 测试数据
     */
    private function loadDb()
    {
        $taskList = [
            [
                //每隔10秒
                "id"   => 1,
                "rule" => "*/5 * * * * *",
                "excute_times" => 0,
                "cmd" => "php -f /home/git/swooleManager/test1.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "running" => 0,
                "status" => 1 //激活
            ],
            [
                //每隔6秒
                "id"   => 2,
                "rule" => "*/10 * * * * *",
                "excute_times" => 0,
                "cmd" => "php -f /home/git/swooleManager/test2.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "running" => 0,
                "status" => 1 //激活
            ],
            [
                //每隔6秒
                "id"   => 3,
                "rule" => "*/15 * * * * *",
                "excute_times" => 0,
                "cmd" => "php -f /home/git/swooleManager/test3.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "running" => 0,
                "status" => 1 //激活
            ],
            [
                //每隔30秒
                "id"   => 4,
                "rule" => "*/20 * * * *",
                "excute_times" => 0,
                "cmd" => "php -f /home/git/swooleManager/test4.php",
                "task_pre_time" => "",
                "task_next_time" => "",
                "running" => 0,
                "status" => 1 //删除
            ]
        ];
        $tasks = [];
        foreach ($taskList as $item){
            $tasks[$item['id']] = $item;
        }
        return $tasks;
    }

}