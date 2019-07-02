<?php

return [
    'host' => "0.0.0.0",
    'port' => "8080",
    'document_root' => PUBLIC_PATH, // v4.4.0以下版本, 此处必须为绝对路径
    'enable_static_handler' => true,
    'pid_file' => PID_FILE,
    'daemonize' => false, //是否后台运行
    'task_worker_num' => 8,
    'task_enable_coroutine' => true,

    'max_task_count' => 1000,//最大任务加载数
    'max_process_count' => 1000,//最大进程数
];
