<?php

namespace Ledc\Timer;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Workerman\Timer;
use Workerman\Worker;

/**
 * 轻量定时器进程
 */
class Process
{
    /**
     * @var string
     */
    private string $timer_dir;

    /**
     * 构造函数
     * @param string $timer_dir 存放定时任务类的目录
     */
    public function __construct(string $timer_dir)
    {
        $this->timer_dir = $timer_dir;
    }

    /**
     * 进程启动时执行
     * @param Worker $worker
     * @return void
     */
    public function onWorkerStart(Worker $worker): void
    {
        $dir_iterator = new RecursiveDirectoryIterator($this->timer_dir);
        $iterator = new RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            if (is_dir($file)) {
                continue;
            }
            $file_info = new SplFileInfo($file);
            $ext = $file_info->getExtension();
            if ($ext === 'php') {
                $class = str_replace('/', "\\", substr(substr($file, strlen(base_path())), 0, -4));
                if (is_a($class, TimerInterface::class, true)) {
                    /** @var TimerInterface $timer */
                    $timer = new $class();
                    $time_interval = $timer->interval();
                    if (0 >= $time_interval) {
                        echo '间隔小于等于0，已忽略：' . $class;
                        continue;
                    }
                    $timer_id = Timer::add($time_interval, function (TimerInterface $timer) use (&$timer_id) {
                        $timer->invoke($timer_id);
                    }, [$timer]);
                }
            }
        }
    }
}
