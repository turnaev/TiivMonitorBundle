<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\redis\Redis;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Check\Redis;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var Redis
     */
    private $checker;

    /**
     * @param string      $host
     * @param int         $port
     * @param string|null $auth
     */
    public function __construct($host = 'localhost', $port = 6379, $auth = null)
    {
        $this->checker = new Redis($host, $port, $auth);
    }

    /**
     * Perform the check.
     *
     * @see \ZendDiagnostics\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            return $this->checker->check();
        } catch (\Exception $e) {
            return new Failure($e->getMessage());
        }
    }
}
