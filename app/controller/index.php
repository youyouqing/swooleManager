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
        $cron = CronExpression::factory('30 9 * * *');
        $next = $cron->getNextRunDate()->format("Y-m-d H:i:s");
        $pre = $cron->getPreviousRunDate()->format("Y-m-d H:i:s");
        return $this->resultJson(0,[
            'next'=>$next,
            'pre'=>$pre,
            'is_due'=>$cron->isDue()
        ],'');

    }


    public function aaa()
    {
        echo "aaa";
    }
}