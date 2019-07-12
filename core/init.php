<?php

if (!extension_loaded('swoole')) {
    exit("extension without swoole");
}

if (extension_loaded('xdebug')) {
    exit("extension with xdebug");
}
date_default_timezone_set("Asia/Shanghai");

define("ROOT_PATH", dirname(__DIR__));
define("VENDOR_PATH", ROOT_PATH . "/vendor");
define("APP_PATH", ROOT_PATH . "/app");
define("COMMON_PATH", APP_PATH . "/common");
define("CORE_PATH", ROOT_PATH . "/core");
define("CONFIG_PATH", APP_PATH . "/config");
define("PUBLIC_PATH", ROOT_PATH . "/public");
define("PID_FILE", APP_PATH . "/runtime/server.pid");

require_once CORE_PATH . "/loader.php";

// 加载核心文件
\core\loader::includeCore(CORE_PATH);

// 加载并注入配置文件
\core\loader::includeConfig(CONFIG_PATH);

// 自动加载
\core\loader::load();

// 加载公共类
\core\loader::includeConfig(COMMON_PATH);

// 加载并注入mysql连接池
\core\loader::initDatabases();

// 加载并注入redis
\core\loader::initRedis();

// 加载composer
\core\loader::includeComposer();

//注入简单log
\core\loader::initLogs();