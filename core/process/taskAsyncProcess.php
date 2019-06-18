<?php


namespace core\process;

use core\Di;
use core\ServerManager;
use core\TableManager;
use core\task\task;
use Cron\CronExpression;

/**
 * 独立进程
 * 1.任务同步
 * 2.每分钟执行一次，产生下一分钟需要执行的任务
 *
 * Class taskAsyncProcess
 * @package core\process
 */
class taskAsyncProcess extends abstractProcess
{
    public function onShutdown()
    {

    }

    public function run($arg)
    {
        swoole_timer_after(2 * 1000, function (){
            $tableTasks = TableManager::shareInstance()->getTable(task::TABLE_NAME_TASK);
            print_r($tableTasks);
//            foreach ($tableTasks as $taskId => $value) {
//                $task_next_time = CronExpression::factory($value['rule'])->getNextRunDate()->format("Y-m-d H:i:s");
//                $task_pre_time = CronExpression::factory($value['rule'])->getPreviousRunDate()->format("Y-m-d H:i:s");
//                $value['task_next_time'] = $task_next_time;
//                $value['task_pre_time'] = $task_pre_time;
//                $value['task_next_exec_time'] = CronExpression::factory($value['rule'])->getNextRunDate()->getTimestamp();
//                $value['task_pre_exec_time'] = CronExpression::factory($value['rule'])->getPreviousRunDate()->getTimestamp();
//                TableManager::shareInstance()->setTable(task::TABLE_NAME_TASK , $taskId, $value);
//            }
//            ServerManager::shareInstance()->getSwooleServer()->sendMessage("dhdhdhdh",1);
        });

    }

    public function onReceive(string $str)
    {

    }

}