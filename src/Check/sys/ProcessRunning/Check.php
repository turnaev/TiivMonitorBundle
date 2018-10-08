<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\sys\ProcessRunning;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\ProcessRunning;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var ProcessRunning
     */
    private $checker;

    /**
     * @param string|int $processNameOrPid name or ID of the process to find
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($processNameOrPid)
    {
        $this->checker = new ProcessRunning($processNameOrPid);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
