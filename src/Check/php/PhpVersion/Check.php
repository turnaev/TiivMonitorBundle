<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\PhpVersion;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\PhpVersion;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var PhpVersion
     */
    private $checker;

    /**
     * @param string|array|\Traversable $expectedVersion The expected version
     * @param string                    $operator        One of: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($expectedVersion, $operator = '>=')
    {
        $this->checker = new PhpVersion($expectedVersion, $operator);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
