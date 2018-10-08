<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\memcache\Memcache;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\Memcache;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var Memcache
     */
    private $checker;

    /**
     * @param string $host
     * @param int    $port
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($host = '127.0.0.1', $port = 11211)
    {
        $this->checker = new Memcache($host, $port);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
