<?php

namespace Ledc\Timer;

/**
 * 定时器接口
 */
interface TimerInterface
{
    /**
     * 供定时器周期调用的方法
     * @param bool|int $timer_id 当前定时器ID
     * @return void
     */
    public function invoke(bool|int $timer_id): void;

    /**
     * 获取定时间隔
     * - 单位：秒
     * @return int
     */
    public function interval(): int;
}
