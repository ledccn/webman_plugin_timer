<?php

return [
    // 轻量定时器组件
    'timer' => [
        'handler' => Ledc\Timer\Process::class,
        // 进程数只能为1
        'constructor' => [
            // 定时器类文件目录
            'timer_dir' => app_path() . '/timer',
        ],
    ],
];
