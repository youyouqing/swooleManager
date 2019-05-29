<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/21
 * Time: 15:43
 */

namespace core\process\src;

abstract class process
{
    protected $process;
    protected $worker;
    protected $task = [];
    protected $processes;

    public function __construct($processName , $task = [])
    {
        $this->process = new \swoole_process(function (\swoole_process $worker){
            $this->process and $this->onStart($worker , $this->task);
            $this->process and $this->handleCallback($worker);
            $worker        and $this->worker = $worker;
        });

        $this->task = $task;
        $this->processes[$processName] = $this->process;
        $this->process->start();
        $this->setProcessName($processName);
    }

    private function setProcessName($processName)
    {
        swoole_set_process_name($processName);
    }

    /**
     * 进程丢入实践循环
     * @param $process
     */
    private function handleCallback($process)
    {
        //注册异常死亡进程自动新起进程继续执行
        \swoole_process::signal(SIGTERM, function ($sig) {
            while ($ret = \Swoole\Process::wait(false)) {
                // create a new child process
                $p = new \Swoole\Process(function (){
                    $this->onStart($this->worker , $this->task);
                });
                $p->start();
            }
        });

        \swoole_event_add($process->pipe, function ($pipe) use ($process) {
            $processMsg = $process->read();
            echo $processMsg;
            //回调管道消息
            $this->onPipRead($processMsg);
        });
    }


    public function getProcessNameAll()
    {
        return array_keys($this->processes);
    }

    //进程执行任务
    abstract function onStart($process , $task);

    //接受管道消息
    abstract function onPipRead($processMsg);

}