<?php
namespace core\task;

use core\CronManager;
use core\Di;
use core\process\abstractProcess;
use core\process\taskRunProcess;
use core\ServerManager;
use core\TableManager;
use Cron\CronExpression;

class task
{
    const TABLE_NAME_TASK = "table_name_task";

    const TABLE_RUN_TASK = "table_run_task";

    private $tasks = [];

    static $instance;

    static public function shareInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    /**
     * 入内存表字段规则
     * @return array
     */
    public function getTablesRules()
    {
        return [
            'id'   => [
                'type' => TableManager::TYPE_INT,
                'size' => 4,
            ],
            'excute_times' => [
                'type' => TableManager::TYPE_INT,
                'size' => 4,
            ],
            'rule' => [
                'type' => TableManager::TYPE_STRING,
                'size' => 100,
            ],
            'cmd' => [
                'type' => TableManager::TYPE_STRING,
                'size' => 255,
            ],
            'task_next_time' => [
                'type' => TableManager::TYPE_STRING,
                'size' => 255,
            ],
            'task_pre_time' => [
                'type' => TableManager::TYPE_STRING,
                'size' => 255,
            ],
            'task_next_exec_time' => [
                'type' => TableManager::TYPE_STRING,
                'size' => 255,
            ],
            'task_pre_exec_time' => [
                'type' => TableManager::TYPE_STRING,
                'size' => 255,
            ],
            'running' => [
                'type' => TableManager::TYPE_INT,
                'size' => 4,
            ],
            'status'   => [
                'type' => TableManager::TYPE_INT,
                'size' => 4,
            ],
        ];
    }

    /**
     * 添加任务
     * @param $task
     * @return array
     */
    public function addTask($task)
    {
        if (!$this->tasks[$task['id']]) {
            $this->tasks[$task['id']] = $task;
        }
        return $this->tasks;
    }

    /**
     * 删除任务
     * @param $id
     * @return array
     */
    public function delTask($id)
    {
        if (isset($this->tasks[$id])) unset($this->tasks[$id]);
        return $this->tasks;
    }

    /**
     * 只读mysql表
     * 同步内存表
     */
    public function loadTables($tasks , $serv)
    {
        $table = TableManager::shareInstance()->getTable(self::TABLE_NAME_TASK);
        $tableIds = [];
        foreach ($table as $k=> $v) {
            $tableIds[] = $k;
        }
        foreach ($tasks as $id => $value) {
            if ($table //内存表实例存在
                and $value['status'] == 1 //任务状态是激活
            ) {
                if (isset($value['task_next_exec_time'])
                    and ($value['task_next_exec_time'] - time() > 10 or $value['task_next_exec_time'] - time() < 0)){
                    continue;
                }

                $cronInstance = CronExpression::factory($value['rule']);
                $value['task_next_exec_time'] = $cronInstance->getNextRunDate()->getTimestamp();
                if ($value['task_next_exec_time'] - time() > 1) continue;//抛弃下次执行时间超过10秒的任务
                $value['task_next_time'] = $cronInstance->getNextRunDate()->format("Y-m-d H:i:s");
                $value['task_pre_time'] = $cronInstance->getPreviousRunDate()->format("Y-m-d H:i:s");
                $value['task_pre_exec_time'] = $cronInstance->getPreviousRunDate()->getTimestamp();
                //投递任务
                $serv->task(json_encode($value), 1);
            }
        }
    }

    /**
     * 把内存表的数据拿出来 取出下一分钟的数据放到另一张内存表中
     */
    public function prepareTables()
    {
        $tableTasks = TableManager::shareInstance()->getTable(self::TABLE_NAME_TASK);
        $runTasks = TableManager::shareInstance()->getTable(self::TABLE_RUN_TASK);
        $runTaskIds = [];
        foreach ($runTasks as $runTask) {
            $runTaskIds[] = $runTask["id"];
        }
        foreach ($tableTasks as $taskId => $value) {
            if (!in_array($taskId , $runTaskIds)
                and $value['task_next_exec_time']) {
                //加入标示
                $value['running'] = 1;
                if ($run = $runTasks->get($value['id'])) {
                    !$run['running'] and $runTasks->set($value['id'], $value);
                }else{
                    $runTasks->set($value['id'], $value);
                }
            }
        }
    }

    public function getPushTasks()
    {
        $runTasks = TableManager::shareInstance()->getTable(self::TABLE_NAME_TASK);
        return $runTasks;
    }
}