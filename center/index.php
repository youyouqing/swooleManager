<?php

require_once "../core/init.php";

\core\loader::load();

//新增http进程
(new \center\process\processhttp("php:http-process"));

//新增监听进程
