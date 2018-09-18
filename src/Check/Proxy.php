<?php

namespace Tvi\MonitorBundle\Check;

use ZendDiagnostics\Check\CheckCollectionInterface;
use ZendDiagnostics\Check\CheckInterface;

class Proxy
{
    /**
     * @var \Closure
     */
    protected $producer;

    /**
     * @param \Closure $producer
     */
    public function __construct(\Closure $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @return CheckInterface
     */
    public function __invoke()
    {
        return ($this->producer)();
    }
}
