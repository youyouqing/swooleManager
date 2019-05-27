<?php
require_once "../core/init.php";
$args = $argv;
// 处理命令
\core\loader::initRedis();
\core\command::handle($args);

