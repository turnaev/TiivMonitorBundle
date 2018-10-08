<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\db\PDOCheck;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\PDOCheck;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param int    $timeout
     */
    public function __construct($dsn, $username, $password, $timeout = 1)
    {
        $this->checker = new PDOCheck($dsn, $username, $password, $timeout);
    }
}
