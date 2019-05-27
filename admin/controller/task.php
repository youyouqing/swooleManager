<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/27
 * Time: 18:21
 */

namespace admin\controller;


class task extends base
{
    private $model = null;

    public function init()
    {
        $this->model = new \admin\model\task();
    }

    /**
     * 新增任务
     */
    public function add()
    {

    }


    /**
     * 删除任务
     */
    public function delete()
    {

    }


    /**
     * 编辑任务
     */
    public function edit()
    {

    }

    /**
     * 操作任务（暂停、开始、）
     */
    public function handleTask()
    {

    }




}