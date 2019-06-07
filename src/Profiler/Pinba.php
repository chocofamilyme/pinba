<?php

namespace Chocofamily\Profiler;

use Phalcon\Config;

/**
 * Класс Pinba обеспечивает простой интерфейс, который позволяет создавать произвольные пакеты данных Pinba в PHP.
 */
class Pinba implements ProfilerInterface
{
    /**
     * @var array
     */
    private $initTags = [];

    private $timer;

    /**
     * @var
     */
    private $tracer;

    public function __construct(Config $config)
    {
        if (extension_loaded('pinba') == false) {
            throw new \ErrorException('pinba extensions not installing', 500);
        }

        pinba_hostname_set($config->get('hostName', gethostname()));
        pinba_server_name_set($config->get('serverName', $_SERVER['SERVER_NAME']));

        if ($tracer = $config->get('tracer')) {
            $this->tracer = $tracer;
        }

        $this->setInitTags();
    }

    /**
     * Создает и запускает новый Таймер.
     *
     * @param $tags      - теги массив тегов и их значений в виде "тег" => "значение". Не может содержать числовые
     *                   показатели по понятным причинам.
     *
     * @return mixed
     */
    public function start(array $tags)
    {
        if ($initTags = $this->getInitTags()) {
            $tags = array_merge($initTags, $tags);
        }

        $this->timer = pinba_timer_start($allTags);
    }

    /**
     * Останавливает таймер
     */
    public function stop()
    {
        pinba_timer_stop($this->timer);
    }

    public function stopAll()
    {
        unset($this->timer);
        pinba_timers_stop();
    }

    /**
     * Установить имя скрипта.
     *
     * @param $request_uri
     */
    public function script(string $request_uri)
    {
        pinba_script_name_set($request_uri);
    }

    protected function setInitTags()
    {
        if ($this->tracer) {
            $this->initTags = [
                'correlation_id' => $this->tracer->getCorrelationId(),
                'span_id'        => $this->tracer->getSpanId(),
            ];
        }
    }

    protected function getInitTags()
    {
        return $this->initTags;
    }

    /**
     * @return mixed
     */
    public function getTimer()
    {
        return $this->timer;
    }
}
