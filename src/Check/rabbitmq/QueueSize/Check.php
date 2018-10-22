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
     * @param ?string $loading
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
        $loading,
        $criticalThreshold,
        $warningThreshold = null,
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest',
        $vhost = '/',
        $dsn = null)
    {
        $this->loading = $loading;
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

            if ($messageCount >= $this->criticalThreshold) {
                $msg = sprintf('Message(s) %d in queue higher them critical level %d.', $messageCount, $this->criticalThreshold);

                return new Failure($msg, $messageCount);
            }

            if (null !== $this->warningThreshold && $messageCount >= $this->warningThreshold) {
                $msg = sprintf('Message(s) %d in queue higher them warning level %d.', $messageCount, $this->warningThreshold);

                return new Warning($msg, $messageCount);
            }

            return new Success(sprintf('Message(s) %d in queue.', $messageCount), $messageCount);
        } catch (\Exception $e) {
            return new Failure($e->getMessage());
        }
    }
}
