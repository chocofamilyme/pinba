<?php

namespace Chocofamily\Profiler;

interface ProfilerInterface
{
    public function start(array $tags): int;

    public function stop(int $timerId);

    public function stopAll();

    public function script(string $url);

    public function getTimers();

    public function getData(): array;

    public function flush(?string $scriptName = null, ?int $flag = null): void;
}
