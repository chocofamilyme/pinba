<?php
/**
 * @package Chocolife.me
 * @author  Moldabayev Vadim <moldabayev.v@chocolife.kz>
 */

namespace Chocofamily\Profiler;


interface TracerInterface
{

    public function getCorrelationId(): string;

    public function getSpanId(): int;
}
