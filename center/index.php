<?php

if (!extension_loaded('swoole')) {
    exit("extension without swoole");
}

if (extension_loaded('xdebug')) {
    exit("extension with xdebug");
}
date_default_timezone_set("Asia/Shanghai");

define("ROOT_PATH",dirname(__DIR__));
define("CORE_PATH",ROOT_PATH."/core");
define("VENDOR_PATH",ROOT_PATH."/vendor");
define("ADMIN_PATH",ROOT_PATH."/admin");


require_once CORE_PATH."/loader.php";

// 加载核心文件
\core\loader::includeCore(CORE_PATH);
\core\loader::load();

new \center\process\processexec("dzc11111");