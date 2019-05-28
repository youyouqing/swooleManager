<?php

require_once "../core/init.php";

\core\loader::load();

new \center\process\processhttp("php:http-process");