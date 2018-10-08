<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\SecurityAdvisory;

use ZendDiagnostics\Check\SecurityAdvisory;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string $lockFilePath Path to composer.lock
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($lockFilePath = null)
    {
        $this->checker = new SecurityAdvisory($lockFilePath);
    }
}
