<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/27
 * Time: 18:21
 */

namespace app\controller;

class task extends base
{
    private $model = null;
    private $handleMap = [
        "0" => "暂停",
        "1" => "开始",
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
        $params = $this->filterRequestFields(["rule","cmd","status","group_id","task_name","description","timeout"]);
        $res = $this->model->where([
            "group_id" => $params['group_id'],
            "task_name" => $params['task_name'],
        ])->find();
        if ($res) {
            $this->resultJson(-1,false,"该组存在同名任务");
        }
        $res = $this->model->insert([
            "group_id" => $params['group_id'],//任务组
            "user_id" => $this->user->id,//用户id
            "create_time" => time(),//创建时间
            "rule" => $params['rule'],//cron表达式
            "cmd" => $params['cmd'],//命令
            "status" => $params['status'],//状态 0暂停  1 开始 默认暂停
            "task_name" => $params['task_name'],//任务名称
            "timeout" => $params['timeout'],//超时时间
        ]);
        return $this->resultJson($res ? 0 : -1,$res,$res ? "添加成功":"添加失败");
    }


    /**
     * 删除任务
     */
    public function delete()
    {
        $params = $this->filterRequestFields(["id"]);
        $taskRes = $this->model->where(["id"=>$params['id']])->find();
        if (!$taskRes) {
            $this->resultJson(-1,false,"任务不存在");
        }
        if ($taskRes['user_id'] != $this->user->id) {
            $this->resultJson(-1,false,"没有权限删除非本人创建的任务");
        }
        $res = $this->model->where(["id" => $params['id']])->delete();
        return $this->resultJson($res ? 0 : -1, boolval($res), $res ? "删除成功" : "删除失败");
    }


    /**
     * 编辑任务
     */
    public function edit()
    {
        $params = $this->filterRequestFields(["id","rule","cmd","status","group_id","task_name","description","timeout"]);
        $taskRes = $this->model->where(["id"=>$params['id']])->find();
        if (!$taskRes) {
            $this->resultJson(-1,false,"任务不存在");
        }
        if ($taskRes['user_id'] != $this->user->id) {
            $this->resultJson(-1,false,"没有权限编辑非本人创建的任务");
        }
        $res = $this->model->where(["id"=>$params['id']])->save([
            "update_time" => time(),//更新时间
            "group_id" => $params['group_id'],//任务组
            "user_id" => $this->user->id,//用户id
            "rule" => $params['rule'],//cron表达式
            "cmd" => $params['cmd'],//命令
            "status" => $params['status'],//状态 0暂停  1 开始 默认暂停
            "task_name" => $params['task_name'],//任务名称
            "timeout" => $params['timeout'],//超时时间
        ]);
        return $this->resultJson($res ? 0 : -1, boolval($res), $res ? "修改成功" : "修改失败或者无需修改");
    }

    /**
     * 操作任务（暂停、开始、）
     */
    public function handleTask()
    {
        $params = $this->filterRequestFields(["id","status"]);
        $taskRes = $this->model->where(["id"=>$params['id']])->find();
        if (!$taskRes) {
            $this->resultJson(-1,false,"任务不存在");
        }
        if ($taskRes['user_id'] != $this->user->id) {
            $this->resultJson(-1,false,"没有权限操作非本人创建的任务");
        }
        if ($params['status'] == $taskRes['status']) {
            return $this->resultJson(-1,false,"任务已是".$this->handleMap[$params['status']]."无需修改");
        }
        $res = $this->model->where(["id"=>$params['id']])->save(["status"=>$params["status"]]);
        return $this->resultJson($res ? 0 : -1, boolval($res), $res ? "修改成功" : "修改失败或者无需修改");
    }

    /**
     * 任务列表
     */
    public function lists()
    {
        $params = $this->filterRequestFields();
        $page = 1;
        $pageNum = 10;
        $where = [];
        if ($params["page"] and $params["pageNum"]) {
            $page = ($params["page"] - 1) * $params['pageNum'];
        }
        if ($params['group_id']) {
            $where['group_id'] = $params['group_id'];
        }
        if ($params["start_time"] and $params["end_time"]) {
            $where[] = ['create_time','between time', [strtotime($params["start_time"]),strtotime($params["end_time"])]];
        }
        $res = $this->model->fetchSql()->where($where)->limit($page , $pageNum)->order("id desc")->select();
        return $this->resultJson($res ? 0 : -1, $res ?? [], "OK");
    }



}