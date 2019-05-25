<?php

return [
    "params" => [
        'worker_num' => 4,    //worker process num
        'backlog' => 128,   //listen backlog
        'max_request' => 50,
        'dispatch_mode' => 1,
        'daemonize' => false
    ],
    "listen" => [
        'host' => '0.0.0.0',
        'port' => '9898'
    ],
];