<?php

namespace core\process;

use Swoole\Process;

class abstractprocess
{
    private $swooleProcess;
    private $processName;
    private $arg;
    private $maxExitWaitTime = 3;

    /**
     * @param int $maxExitWaitTime
     */
    public function setMaxExitWaitTime(int $maxExitWaitTime): void
    {
        $this->maxExitWaitTime = $maxExitWaitTime;
    }

    final function __construct(string $processName,$arg = null,$redirectStdinStdout = false,$pipeType = 2,$enableCoroutine = false)
    {
        $this->arg = $arg;
        $this->processName = $processName;
        $this->swooleProcess = new \swoole_process([$this,'__start'],$redirectStdinStdout,$pipeType,$enableCoroutine);
    }

    public function getProcess():Process
    {
        return $this->swooleProcess;
    }

    /*
     * 服务启动后才能获得到pid
     */
    public function getPid():?int
    {
        if(isset($this->swooleProcess->pid)){
            return $this->swooleProcess->pid;
        }else{
            return null;
        }
    }

    function __start(Process $process)
    {
        if(PHP_OS != 'Darwin'){
            $process->name($this->getProcessName());
        }

        Process::signal(SIGTERM,function ()use($process){
            go(function ()use($process){
                $new = iterator_to_array(\co::listCoroutines());
                try{
                    $this->onShutDown();
                }catch (\Throwable $throwable){
                    $this->onException($throwable);
                }
                swoole_event_del($process->pipe);
                Process::signal(SIGTERM,null);
                $old = iterator_to_array(\co::listCoroutines());
                $diff = array_diff($old,$new);
                if(empty($diff)){
                    $this->getProcess()->exit(0);
                    return;
                }
                $t = $this->maxExitWaitTime;
                while($t > 0){
                    $exit = true;
                    foreach ($diff as $cid){
                        if(\co::getBackTrace($cid,DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,1) == false){
                            $exit = true;
                        }else{
                            $exit = false;
                            continue;
                        }
                    }
                    if($exit){
                        break;
                    }
                    \co::sleep(0.01);
                    $t = $t - 0.01;
                }
                $this->getProcess()->exit(0);
            });
        });
        swoole_event_add($this->swooleProcess->pipe, function(){
            $msg = $this->swooleProcess->read(64 * 1024);
            $this->onReceive($msg);
        });
        try{
            $this->run($this->arg);
        }catch (\Throwable $throwable){
            $this->onException($throwable);
        }
    }

    public function getArg()
    {
        return $this->arg;
    }

    public function getProcessName()
    {
        return $this->processName;
    }

    protected function onException(\Throwable $throwable){
        throw $throwable;
    }

    public  function run($arg){}
    public  function onShutDown(){}
    public  function onReceive(string $str){}
}