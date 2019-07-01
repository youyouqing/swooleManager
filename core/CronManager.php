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
use Swoole\Server\Task;

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

}