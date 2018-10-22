<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\rabbitmq\QueueConsumer;

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
     * Count that will cause a warning.
     *
     * @var int
     */
    protected $warningThreshold;

    /**
     * Count that will cause a fail.
     *
     * @var int
     */
    protected $criticalThreshold;

    /**
     * check queue name.
     *
     * @var string
     */
    protected $queue;

    /**
     * @var RabbitMQClient
     */
    private $client;

    /**
     * @param ?string $queue
     * @param ?int    $criticalThreshold
     * @param ?int    $warningThreshold
     * @param ?string $host
     * @param ?int    $port
     * @param ?string $user
     * @param ?string $password
     * @param ?string $vhost
     * @param ?string $dsn
     */
    public function __construct(
        $queue,
        $criticalThreshold,
        $warningThreshold = null,
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest',
        $vhost = '/',
        $dsn = null)
    {
        $this->queue = $queue;
        $this->criticalThreshold = $criticalThreshold;
        $this->warningThreshold = $warningThreshold;

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

            $channel = $conn->channel();

            list($queue, $messageCount, $consumerCount) = $channel->queue_declare($this->queue, true);

            if ($consumerCount <= $this->criticalThreshold) {
                $msg = sprintf('Consumer(s) %d got queue to few less them critical level %d.', $consumerCount, $this->criticalThreshold);

                return new Failure($msg, $messageCount);
            }

            if ($this->warningThreshold != null && $consumerCount <= $this->warningThreshold) {
                $msg = sprintf('Consumer(s) %d got queue to few less them critical level %d.', $consumerCount, $this->warningThreshold);

                return new Warning($msg, $messageCount);
            }

            return new Success(sprintf('Consumer(s) %d for queue.', $consumerCount), $consumerCount);
        } catch (\Exception $e) {
            return new Failure($e->getMessage());
        }
    }
}
