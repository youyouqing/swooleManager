<?php

namespace app\controller;

use function app\common\getPoolConnection;
use app\extend\mysqlPool;
use app\extend\orm;
use Cron\CronExpression;

class index extends base
{
    public function __construct()
    {
    }

    public function testCrontime()
    {
        $task_next_time = CronExpression::factory("*/15 * * * * *")->getNextRunDate()->format("Y-m-d H:i:s");
        $task_pre_time = CronExpression::factory("*/15 * * * * *")->getPreviousRunDate()->format("Y-m-d H:i:s");

        return $this->resultJson(0,[
            'next'=>$task_next_time,
            'pre'=>$task_pre_time,
            'is_due'=>1111
        ],'');

    }


    public function testAbWithPool()
    {
        $conn = getPoolConnection();
        $res = orm::shareInstance()->setConn($conn)->table("t_task_log")->limit(100)->select();
        return $this->resultJson(0,$res);
    }

    public function testAbWithoutPool()
    {
        $conn = getPoolConnection();
        $res = orm::shareInstance()->setConn($conn)->table("t_task_log")->limit(100)->select();
        return $this->resultJson(0,$res);
    }
}