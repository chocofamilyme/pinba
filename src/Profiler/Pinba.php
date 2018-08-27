<?php

namespace Chocofamily\Profiler;

use Chocofamily\Http\CorrelationId;

/**
 * Класс Pinba обеспечивает простой интерфейс, который позволяет создавать произвольные пакеты данных Pinba в PHP.
 */
class Pinba implements ProfilerInterface
{
    private $host;
    private $server;
    private $initTags;
    private $timer;

    /** @var CorrelationId */
    private $correlationId;

    public function __construct($host, $server = null)
    {
        $this->host          = $host;
        $this->server        = $server;
        $this->correlationId = CorrelationId::getInstance();
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
    public function start($tags)
    {
        if (function_exists('pinba_timer_start')) {
            $allTags = array_merge($this->getInitTags(), $tags);

            $this->timer = pinba_timer_start($allTags);
        }
    }

    /**
     * Останвливает таймер
     *
     * @param $timer
     */
    public function stop()
    {
        if (function_exists('pinba_timer_stop')) {
            pinba_timer_stop($this->timer);
        }
    }

    /**
     * Установить имя скрипта.
     *
     * @param $request_uri
     */
    public function script($request_uri)
    {
        if (function_exists('pinba_script_name_set')) {
            pinba_script_name_set($request_uri);
        }
    }

    private function setInitTags()
    {
        $this->initTags = [
            '__hostname'    => $this->host,
            '__server_name' => $this->server,
            'server'        => $this->server,
            "correlation_id" => $this->correlationId->getCorrelationId(),
            "span_id"        => $this->correlationId->getSpanId(),
        ];
    }

    private function getInitTags()
    {
        return $this->initTags;
    }

    /**
     * @return null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param null $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return null
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param null $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }
}
