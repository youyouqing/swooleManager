<?php

return [
    'host' => "0.0.0.0",
    'port' => "8080",
    'document_root' => PUBLIC_PATH, // v4.4.0以下版本, 此处必须为绝对路径
    'enable_static_handler' => true,
    'pid_file' => PID_FILE,
];
