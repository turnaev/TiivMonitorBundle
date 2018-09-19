<?php

namespace Tvi\MonitorBundle\Runner;

use Tvi\MonitorBundle\Check\Manager;

class Manager
{
    /**
     * @var Manager
     */
    protected $checkRegistry;

    /**
     * @param Manager $checkRegistry
     */
    public function __construct(Manager $checkRegistry)
    {
        $this->checkRegistry = $checkRegistry;
    }

    public function getRunner()
    {
        $checks = $this->checkRegistry->toArray();

        $runner = new Runner(null, $checks);

        return $runner;

    }
}
