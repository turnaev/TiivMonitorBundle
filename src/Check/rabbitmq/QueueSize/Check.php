<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\rabbitmq\QueueSize;

use JMS\Serializer\Annotation as JMS;
use Tvi\MonitorBundle\Check\rabbitmq\RabbitMQClient;
use Tvi\MonitorBundle\Check\CheckAbstract;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var RabbitMQClient
     */
    private $client;

    /**
     * @param string     $host
     * @param int        $port
     * @param string     $user
     * @param string     $password
     * @param string     $vhost
     * @param null|mixed $dsn
     */
    public function __construct(
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest',
        $vhost = '/',
        $dsn = null)
    {
        $this->client = new RabbitMQClient($host,
            $port,
            $user,
            $password,
            $vhost,
            $dsn);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        try {
            $conn = $this->client->getConnect();
            $conn->channel();

            $conn->isConnected();
            $version = $conn->getServerProperties()['version'][1];

            return new Success(null, $version);
        } catch (\Exception $e) {
            return new Failure($e->getMessage());
        }
    }
}
