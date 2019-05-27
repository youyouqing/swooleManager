<?php
/**
 * Created by PhpStorm.
 * User: dzc
 * Date: 2019/5/27
 * Time: 18:21
 */

namespace admin\controller;


class taskGroup extends base
{
    private $model = null;

    public function init()
    {
        $this->model = new \admin\model\taskGroup();
        /**
         * CREATE TABLE `t_task_group` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
        `group_name` varchar(50) NOT NULL DEFAULT '' COMMENT '组名',
        `description` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
        `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
        PRIMARY KEY (`id`),
        KEY `idx_user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
         */
    }

    /**
     * 新增任务组
     */
    public function add()
    {

    }


    /**
     * 删除任务组
     */
    public function delete()
    {

    }


    /**
     * 编辑任务组
     */
    public function edit()
    {

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

    }

}