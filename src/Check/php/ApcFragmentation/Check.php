<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ApcFragmentation;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\ApcFragmentation;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var ApcFragmentation
     */
    private $checker;

    /**
     * @param int $warningThreshold  A number between 0 and 100
     * @param int $criticalThreshold A number between 0 and 100
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($warningThreshold, $criticalThreshold)
    {
        $this->checker = new ApcFragmentation($warningThreshold, $criticalThreshold);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
