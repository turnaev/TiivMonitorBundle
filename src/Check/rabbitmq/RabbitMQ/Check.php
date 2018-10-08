<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\rabbitmq\RabbitMQ;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\RabbitMQ;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var RabbitMQ
     */
    private $checker;

    /**
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $password
     * @param string $vhost
     */
    public function __construct(
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest',
        $vhost = '/')
    {
        $this->checker = new RabbitMQ($host,
            $port,
            $user,
            $password,
            $vhost);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
