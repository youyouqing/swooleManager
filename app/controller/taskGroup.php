<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/27
 * Time: 18:21
 */

namespace app\controller;


class taskGroup extends base
{
    private $model = null;

    public function init()
    {
        $this->model = new \app\model\taskGroup();
    }

    /**
     * 新增任务组
     */
    public function add()
    {
        $params = $this->filterRequestFields(["group_name"]);
        $exist = $this->model->where(["group_name" => $params['group_name']])->find();
        if ($exist) {
            return $this->resultJson(-1,false,$params['group_name']."已经存在");
        }
        $res = $this->model->insert([
            "group_name" => $params['group_name'],
            "create_time" => time(),
            "user_id" => $this->user["id"],
            "description" => $params['description'] ?? ""
        ]);
        return $this->resultJson($res ? 0 : -1,$res,$res ? "添加成功":"添加失败");
    }


    /**
     * 删除任务组
     */
    public function delete()
    {
        $params = $this->filterRequestFields(["group_id"]);
        $exist = $this->model->where(["id" => $params['group_id']])->find();
        if (!$exist) {
            return $this->resultJson(-1, false, "分组不存在");
        }
        if ($this->user['id'] != $exist['user_id']) {
            return $this->resultJson(-1, false, "您没有权限删除该分组");
        }
        $res = $this->model->where(["id" => $params['group_id']])->delete();
        return $this->resultJson($res ? 0 : -1, boolval($res), $res ? "删除成功" : "删除失败");
    }


    /**
     * 编辑任务组
     */
    public function edit()
    {
        $params = $this->filterRequestFields(["group_id"]);
        $exist = $this->model->where(["id" => $params['group_id']])->find();
        if (!$exist) {
            return $this->resultJson(-1, false, "分组不存在");
        }
        if ($this->user['id'] != $exist['user_id']) {
            return $this->resultJson(-1, false, "您没有权限编辑该分组");
        }
        $res = $this->model->where(["id" => $params['group_id']])->save([
            "group_name" => $params['group_name'] ?? $exist['group_name'],
            "description" => $params['description'] ?? $exist['description'],
            "update_time" => time(),
        ]);
        return $this->resultJson($res ? 0 : -1, boolval($res), $res ? "修改成功" : "修改失败或者无需修改");
    }

    /**
     * 单任务组详情
     */
    public function detail()
    {

    }

    /**
     * 任务组列表
     */
    public function lists()
    {
        $res = $this->model->select();
        return $this->resultJson(0, $res, "");
    }

}