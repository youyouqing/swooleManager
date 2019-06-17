<?php
namespace core\task;

use core\process\abstractprocess;
use core\process\cronprocess;
use core\ServerManager;
use core\TableManager;
use Cron\CronExpression;

class task
{
    const TABLE_NAME_TASK = "table_name_task";

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
     * 加载所有任务
     */
    public function loadTasks()
    {
        $taskList = [
            [
                "id"   => 1,
                "rule" => "* * * * *",
                "excute_times" => 0,
                "cmd" => "usr/bin/php -f /home/docker-project/www/html/swooleManager/atestt.php",
                "task_pre_time" => "",
                "task_next_time" => "",
            ],
            [
                "id"   => 2,
                "rule" => "*/30 * * * *",
                "excute_times" => 0,
                "cmd" => "usr/bin/php -f /home/docker-project/www/html/swooleManager/test1.php",
                "task_pre_time" => "",
                "task_next_time" => "",
            ]
        ];
        foreach ($taskList as $item){
            $this->tasks[$item['id']] = $item;
        }

        return $this->tasks;
    }

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
     * 同步内存表
     */
    public function syncTables()
    {
        $tasks = self::loadTasks();
        foreach ($tasks as $id => $value) {
            $table = TableManager::shareInstance()->addTable(self::TABLE_NAME_TASK , self::getTablesRules() , 1024);
            if ($table) {
                $table->setTable(self::TABLE_NAME_TASK , $id, $value);
            }
        }
        $tableTasks = TableManager::shareInstance()->getTable(self::TABLE_NAME_TASK);
        foreach ($tableTasks as $taskId => $value) {
            $task_next_time = CronExpression::factory($value['rule'])->getNextRunDate()->format("Y-m-d H:i:s");
            $task_pre_time = CronExpression::factory($value['rule'])->getPreviousRunDate()->format("Y-m-d H:i:s");
            $value['task_next_time'] = $task_next_time;
            $value['task_pre_time'] = $task_pre_time;
            //TODO 入库mysql
            $value['task_next_exec_time'] = CronExpression::factory($value['rule'])->getNextRunDate()->getTimestamp();
            $value['task_pre_exec_time'] = CronExpression::factory($value['rule'])->getPreviousRunDate()->getTimestamp();
            TableManager::shareInstance()->setTable(self::TABLE_NAME_TASK , $taskId, $value);
            $cronProcess = new cronprocess("php-定时任务process-".$taskId,$value);
            //一个任务一个进程   后续改成携程 TODO
            ServerManager::shareInstance()->getSwooleServer()->addProcess($cronProcess->getProcess());
        }
    }

}