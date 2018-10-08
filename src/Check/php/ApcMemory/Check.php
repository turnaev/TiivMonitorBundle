<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ApcMemory;

use ZendDiagnostics\Check\ApcMemory;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param int $warningThreshold  A number between 0 and 100
     * @param int $criticalThreshold A number between 0 and 100
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($warningThreshold, $criticalThreshold)
    {
        $this->checker = new ApcMemory($warningThreshold, $criticalThreshold);
    }
}
