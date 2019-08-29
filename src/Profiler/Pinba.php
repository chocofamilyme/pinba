<?php

namespace Chocofamily\Profiler;

use Phalcon\Config;

/**
 * Класс Pinba обеспечивает простой интерфейс, который позволяет создавать произвольные пакеты данных Pinba в PHP.
 */
class Pinba implements ProfilerInterface
{
    /**
     * @var bool
     */
    private $isPinbaInstalled = true;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $initTags = [];

    /**
     * @var
     */
    private $timers;

    /**
     * @var int
     */
    private $incr = 0;

    /**
     * @var TracerInterface
     */
    private $tracer;

    /**
     * Pinba constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        if (extension_loaded('pinba') == false) {
            $this->isPinbaInstalled = false;
        }

        $this->config = $config;
        $this->init();

        $this->setInitTags();
    }

    protected function init()
    {
        if ($this->isPinbaInstalled) {
            pinba_hostname_set($this->config->get('hostName', gethostname()));
            pinba_server_name_set($this->config->get('serverName', $_SERVER['SERVER_NAME'] ?? ''));

            if ($schema = $this->config->get('schema')) {
                pinba_schema_set($schema);
            }
        }
    }

    /**
     * Создает и запускает новый Таймер.
     *
     * @param string $group
     * @param string $type
     * @param string $method
     * @param string $category
     * @return mixed
     */
    public function start(string $group, string $type, string $method, string $category): int
    {
        if (!$this->isPinbaInstalled) {
            return 0;
        }

        if ($initTags = $this->getInitTags()) {
            $tags = array_merge($initTags, [
                'group' => $group,
                'type' => $type,
                'method' => $method,
                'category' => $category,
            ]);
        }

        $timerId = $this->incr++;
        $this->timers[$timerId] = pinba_timer_start($tags);

        return $timerId;
    }

    /**
     * Останавливает таймер
     *
     * @param int $timerId
     */
    public function stop(int $timerId)
    {
        if (!$this->isPinbaInstalled) {
            return;
        }

        if ($this->isPinbaInstalled && isset($this->timers[$timerId])) {
            pinba_timer_stop($this->timers[$timerId]);
            unset($this->timers[$timerId]);
        }
    }

    public function stopAll()
    {
        if (!$this->isPinbaInstalled) {
            return;
        }

        pinba_timers_stop();
        unset($this->timers);
    }

    /**
     * Установить имя скрипта.
     *
     * @param $request_uri
     */
    public function script(string $request_uri)
    {
        if (!$this->isPinbaInstalled) {
            return;
        }

        pinba_script_name_set($request_uri);
    }

    /**
     * Вернет информацию по все таймерам и метрикам
     *
     * @return array
     */
    public function getData(): array
    {
        if (!$this->isPinbaInstalled) {
            return [];
        }

        return (array) pinba_get_info();
    }

    public function setInitTags()
    {
        if ($this->tracer) {
            $this->initTags = [
                'correlation_id' => $this->tracer->getCorrelationId(),
                'span_id'        => $this->tracer->getSpanId(),
            ];
        }
    }

    public function getInitTags()
    {
        return $this->initTags;
    }

    /**
     * @return mixed
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * @return mixed
     */
    public function getTracer()
    {
        return $this->tracer;
    }

    /**
     * @param mixed $tracer
     */
    public function setTracer(TracerInterface $tracer)
    {
        $this->tracer = $tracer;
    }

    /**
     * @param string|null $scriptName
     * @param int|null    $flag
     */
    public function flush(?string $scriptName = null, ?int $flag = null): void
    {
        if (!$this->isPinbaInstalled) {
            return;
        }

        pinba_flush($scriptName, $flag);
    }
}
