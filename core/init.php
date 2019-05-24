<?php

if (!extension_loaded('swoole')) {
    exit("extension without swoole");
}

if (extension_loaded('xdebug')) {
    exit("extension with xdebug");
}
date_default_timezone_set("Asia/Shanghai");

define("ROOT_PATH",dirname(__DIR__));
define("VENDOR_PATH",ROOT_PATH."/vendor");
define("ADMIN_PATH",ROOT_PATH."/admin");
define("CORE_PATH",ROOT_PATH."/core");
define("CONFIG_PATH",ADMIN_PATH."/config");
define("PUBLIC_PATH",ROOT_PATH."/public");
define("PID_FILE",ADMIN_PATH."/runtime/server.pid");

require_once CORE_PATH."/loader.php";

// 加载核心文件
\core\loader::includeCore(CORE_PATH);

// 加载并注入配置文件
\core\loader::includeConfig(CONFIG_PATH);

// 加载composer
\core\loader::includeComposer();