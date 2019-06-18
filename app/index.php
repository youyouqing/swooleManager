<?php
defined('PATH_SYS') or define('PATH_SYS', str_replace('\\', '/', realpath(dirname(dirname(__FILE__) . ''))));

require_once PATH_SYS . "/core/init.php";
$args = $argv;

//\core\loader::load();

// 处理命令
\core\command::handle($args);