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
    public function loadTables($tasks)
    {
        $table = TableManager::shareInstance()->getTable(self::TABLE_NAME_TASK);
        foreach ($tasks as $id => $value) {
            if ($table) {
                $table->set($id, $value);
            }
        }
        $tableTasks = TableManager::shareInstance()->getTable(self::TABLE_NAME_TASK,1);
        foreach ($table as $taskId => $value) {
            if (!isset($value['id'])) continue;
            if ($value['status'] != 1) {
                continue;
            }
            // 及时修改状态和时间   需要同步内存表
            $task_next_time = CronExpression::factory($value['rule'])->getNextRunDate()->format("Y-m-d H:i:s");
            $task_pre_time = CronExpression::factory($value['rule'])->getPreviousRunDate()->format("Y-m-d H:i:s");
            $value['task_next_time'] = $task_next_time;
            $value['task_pre_time'] = $task_pre_time;
            $value['task_next_exec_time'] = CronExpression::factory($value['rule'])->getNextRunDate()->getTimestamp();
            $value['task_pre_exec_time'] = CronExpression::factory($value['rule'])->getPreviousRunDate()->getTimestamp();
            $table->set($taskId, $value);
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
                and $value['task_next_exec_time']
                and ($value['task_next_exec_time'] - time() <= 60)) {
                if ($run = $runTasks->get($value['id'])) {
                    !$run['running'] and $runTasks->set($value['id'], $value);
                }else{
                    $runTasks->set($value['id'], $value);
                }
            }
        }
    }

}