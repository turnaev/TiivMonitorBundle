<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\sys\CpuPerformance;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\CpuPerformance;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var CpuPerformance
     */
    private $checker;

    /**
     * @param float $minPerformance The minimum performance ratio, where 1 is equal the computational
     *                              performance of AWS EC2 Micro Instance. For example, a value of 2 means
     *                              at least double the baseline experience, value of 0.5 means at least
     *                              half the performance. Defaults to 0.5
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($minPerformance = 0.5)
    {
        $this->checker = new CpuPerformance($minPerformance);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
