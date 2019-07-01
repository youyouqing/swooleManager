<?php
namespace core\process;

use core\Di;
use core\TableManager;
use core\task\task;
use Cron\CronExpression;

/**
 * 任务执行进程
 * Class taskRunProcess
 * @package core\process
 */
class taskRunProcess extends abstractProcess
{

    //进程销毁
    public function onShutdown()
    {
        echo "cron onShutdown";
    }

    public function run($arg)
    {
        //TODO  开启进程读取
//        print_r("发现任务".json_encode($arg,JSON_UNESCAPED_UNICODE));
//        swoole_timer_after($arg['task_next_exec_time'] - time(), function () use ($arg) {
//            echo "执行任务...".PHP_EOL;
//            echo PHP_EOL . "测试成功,调用命令->" . $arg['cmd'] . "执行时间:" . date("Y-m-d H:i:s") . PHP_EOL;
//        });
        swoole_timer_tick(0.5 * 1000 , function () {

            $runTask = TableManager::shareInstance()->getTable(task::TABLE_RUN_TASK);
            foreach ($runTask as $taskId => $item) {
                if ($item['running'] == 1) continue;
                $seconds = $item['task_next_exec_time'] - time();
                if ($seconds <= 0) continue;
                echo $seconds."秒后执行任务".$taskId.PHP_EOL;
                $runTask->set($item['id'],["running"=>1]);

                swoole_timer_after(($seconds) * 1000 , function () use ($item , $runTask) {

                    echo PHP_EOL . "测试成功,调用命令->" . $item['cmd'] . "执行时间:" . date("Y-m-d H:i:s") . PHP_EOL;
                    $runTask->incr($item['id'],"excute_times");
                    $runTask->set($item['id'],["running"=>0]);
                    echo "任务".$item['id']."执行次数是".$runTask->get($item['id'],'excute_times').PHP_EOL;
                    $task_next_time = CronExpression::factory($item['rule'])->getNextRunDate()->format("Y-m-d H:i:s");
                    $task_pre_time = CronExpression::factory($item['rule'])->getPreviousRunDate()->format("Y-m-d H:i:s");
                    $item['task_next_time'] = $task_next_time;
                    $item['task_pre_time'] = $task_pre_time;
                    $item['task_next_exec_time'] = CronExpression::factory($item['rule'])->getNextRunDate()->getTimestamp();
                    $item['task_pre_exec_time'] = CronExpression::factory($item['rule'])->getPreviousRunDate()->getTimestamp();
                    $runTask->set($item['id'],$item);
                    print_r($item);
                    print_r(date("Y-m-d H:i:s").PHP_EOL);
                });
            }
        });
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
        echo "receive:" . $str;
        Di::shareInstance()->get(Di::DI_LOG)->log("接受管道：" . $str);
    }

}