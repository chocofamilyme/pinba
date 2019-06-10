<?php

namespace Chocofamily\Profiler;

use Phalcon\Config;

/**
 *
 * Профилирование в файл
 * Class File
 *
 * @package Chocofamily\Profiler
 */
class File implements ProfilerInterface
{
    private $logger;

    private $timers;

    private $incr = 0;

    /**
     * File constructor.
     *
     * @param Config $config
     *
     * @throws \Exception
     */
    public function __construct(Config $config)
    {
        $this->logger = $config->get('logger');

        if (empty($this->logger)) {
            throw new \ErrorException('Logger no found', 500);
        }
    }

    public function start(array $tags): int
    {
        $currentTags                         = $this->incr++;
        $this->timers[$currentTags]['tags']  = $tags;
        $this->timers[$currentTags]['start'] = microtime(true);

        return $currentTags;
    }

    public function stop(int $timerId = 0)
    {
        if ($timerId) {
            $currentTags = $timerId;
        } else {
            $currentTags = --$this->incr;
        }

        $this->timers[$currentTags]['stop'] = microtime(true);

        $time = $this->timers[$currentTags]['stop'] - $this->timers[$currentTags]['start'];

        $this->logger->debug(print_r($this->timers[$currentTags]['tags'], true).' : '.$time.' секунд');
    }

    public function stopAll()
    {
        while ($this->incr > 0) {
            $this->stop();
        }
    }

    public function script(string $url)
    {
        $this->logger->debug($url);
    }

    /**
     * @return mixed
     */
    public function getTimers()
    {
        return $this->timers;
    }
}
