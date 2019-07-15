<?php
namespace app\common;

use app\extend\mysqlPool;

/**
 * 获取毫秒
 * @return bool|string
 */
function getMillisecond()
{
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    return $msectimes = substr($msectime, 0, 13);
}

/**
 * 获取数据库链接
 * @return mixed
 */
function getPoolConnection()
{
    return mysqlPool::shareInstance()->getPoolCon();
}

/**
 * 归还数据库链接
 */
function backPoolConnection($instance)
{
    mysqlPool::shareInstance()->backPool($instance);
}