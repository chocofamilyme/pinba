<?php

namespace Chocofamily\Profiler;

class File implements ProfilerInterface
{

    private $logger;
    private $start;
    private $end;

    public function __construct($host = null, $server = null)
    {
        $this->logger = \Phalcon\Di::getDefault()->get('logger', ['profile.log']);
    }

    public function start($tags)
    {
        $this->start = microtime(true);
        $this->logger->debug(print_r($tags, true));
    }

    public function stop()
    {
        $this->end = microtime(true);
        $time      = $this->end - $this->start;

        $this->logger->debug('time: '.$time.' секунд');
    }

    public function script($url)
    {
        $this->logger->debug($url);
    }
}
