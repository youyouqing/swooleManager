<?php

namespace app\controller;

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


    public function aaa()
    {
        echo "aaa";
    }
}