<?php

namespace Tvi\MonitorBundle\Runner;

use Tvi\MonitorBundle\Check\Registry;

class Manager
{
    /**
     * @var Registry
     */
    protected $checkRegistry;

    /**
     * @param Registry $checkRegistry
     */
    public function __construct(Registry $checkRegistry)
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
