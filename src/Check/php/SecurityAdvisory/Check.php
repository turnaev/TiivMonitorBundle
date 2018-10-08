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

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\SecurityAdvisory;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var SecurityAdvisory
     */
    private $checker;

    /**
     * @param string $lockFilePath Path to composer.lock
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($lockFilePath = null)
    {
        $this->checker = new SecurityAdvisory($lockFilePath);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
