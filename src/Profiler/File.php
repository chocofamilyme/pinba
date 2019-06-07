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

    private $timer;

    private $countTags = 0;

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

    public function start(array $tags)
    {
        $currentTags                        = $this->countTags++;
        $this->timer[$currentTags]['tags']  = $tags;
        $this->timer[$currentTags]['start'] = microtime(true);
    }

    public function stop()
    {
        $currentTags = --$this->countTags;

        $this->timer[$currentTags]['stop'] = microtime(true);

        $time = $this->timer[$currentTags]['stop'] - $this->timer[$currentTags]['start'];

        $this->logger->debug(print_r($this->timer[$currentTags]['tags'], true).' : '.$time.' секунд');
    }

    public function stopAll()
    {
        while ($this->countTags > 0) {
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
    public function getTimer()
    {
        return $this->timer;
    }
}
