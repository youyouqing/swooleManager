<?php
require_once "../core/init.php";
$args = $argv;

//\core\loader::load();

// 处理命令
\core\command::handle($args);



