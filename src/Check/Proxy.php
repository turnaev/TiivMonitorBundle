<?php

namespace MonitorBundle\Check;

use ZendDiagnostics\Check\CheckCollectionInterface;
use ZendDiagnostics\Check\CheckInterface;

class Proxy
{
    /**
     * @var string
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
     * @return CheckCollectionInterface|CheckInterface
     */
    public function __invoke()
    {
        return ($this->producer)();
    }
}
