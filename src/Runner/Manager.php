<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Runner;

use Tvi\MonitorBundle\Check\Manager as CheckManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
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
