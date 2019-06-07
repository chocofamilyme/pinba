<?php

namespace Chocofamily\Profiler;

interface ProfilerInterface
{
    public function start(array $tags);

    public function stop();

    public function stopAll();

    public function script(string $url);

    public function getTimer();
}
