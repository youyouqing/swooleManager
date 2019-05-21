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
    protected $processes;

    public function __construct($processName)
    {
        $this->process = new \swoole_process(function (){
            if ($this->process){
                $this->onStart($this->process);
            }
        });
        $this->processes[$processName] = $this->process;
        $this->handleCallback($this->process);
        $this->process->start();
        $this->process->name = $processName;
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
                    $this->onStart();
                });
                $p->start();
            }
        });

        \swoole_event_add($process->pipe, function ($pipe) use ($process) {
            $processMsg = $process->read();
            //回调管道消息
            $this->onPipRead($processMsg);
        });
    }


    public function setProcessName($oldNmae,$newName)
    {
        if (!empty($oldNmae) && !empty($newName)) {
            $this->processes[$newName] = $this->processes[$oldNmae];
            unset($this->processes[$oldNmae]);
        }
    }

    public function getProcessNameAll()
    {
        return array_keys($this->processes);
    }

    //进程执行任务
    abstract function onStart($process);

    //接受管道消息
    abstract function onPipRead($processMsg);

}