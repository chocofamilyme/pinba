<?php

namespace Chocofamily\Profiler;

interface ProfilerInterface
{
    public function start($tags);

    public function stop();

    public function script($url);
}
