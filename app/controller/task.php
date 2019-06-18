<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/27
 * Time: 18:21
 */

namespace app\controller;

use app\process\processtask;

class task extends base
{
    protected $need_token = false;
    private $model = null;
    private $err_result = false;
    private $handleMap = [
        "start",
        "stop"
    ];

    public function init()
    {
        $this->model = new \app\model\task();
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
        $handle_type = $this->serverParams("handle_type");
        if (!in_array($handle_type , $this->handleMap)) {
            return $this->resultJson(-1,false,"不存在的操作类型");
        }
        $taskRes = $this->checkTask($this->serverParams('task_id') , $handle_type);
        if (!$taskRes) {
            return $this->err_result;
        }
        switch ($handle_type) {
            case $this->handleMap[0]:
                //开启 进程处理
//                go(function () {
//                    echo 111111;
//                });
                (new processtask("php:task-process" , $taskRes , false));
                break;
            case $this->handleMap[1]:
                //关闭  进程处理

                break;
                default;
        }
    }

    private function checkTask($taskId , $handle_type = false)
    {
        if (empty($taskId) or !is_numeric($taskId)) {
            $this->err_result = $this->resultJson(-1,false,"任务id格式不正确");
            return false;
        }
        $taskRes = \app\model\task::where([
            "id" => $taskId,
        ])->find();
        if (!$taskRes) {
            $this->err_result = $this->resultJson(-1,false,"该任务不存在");
            return false;
        }
        if (($this->handleMap[0] == $handle_type) and (1 == $taskRes['status'])){
            $this->err_result = $this->resultJson(-1,false,"该任务已经开启，不允许重复开启");
            return false;
        }
        if (($this->handleMap[1] == $handle_type) and (0 == $taskRes['status'])){
            $this->err_result = $this->resultJson(-1,false,"该任务已经关闭，不允许重复关闭");
            return false;
        }
        return $taskRes;
    }




}