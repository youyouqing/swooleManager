<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/21
 * Time: 18:14
 */

namespace app\model;


class taskGroup extends base
{
    protected $name = 't_task_group';

    public function getDataByName($groupName)
    {
        self::where([
            'group_name' => $groupName,

        ]);
    }
}