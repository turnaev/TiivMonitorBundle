<?php

namespace Tvi\MonitorBundle\Runner;

use Tvi\MonitorBundle\Check\Manager as CheckManager;

class Manager
{
    /**
     * @var CheckManager
     */
    protected $checkManager;

    /**
     * @param CheckManager $checkManager
     */
    public function __construct(CheckManager $checkManager)
    {
        $this->checkManager = $checkManager;
    }

    /**
     * @return Runner
     */
    public function getRunner(): Runner
    {
        $checks = $this->checkManager->toArray();

        $runner = new Runner(null, $checks);

        return $runner;
    }
}
