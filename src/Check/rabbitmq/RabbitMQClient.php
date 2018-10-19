<?php
/**
 * This file is part of the `monitor-bundle` project.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\rabbitmq;

use PhpAmqpLib\Connection\AMQPConnection;
use Tvi\MonitorBundle\Exception\FeatureRequired;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class RabbitMQClient
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $vhost;

    /**
     * @param ?string  $host
     * @param ?integer $port
     * @param ?string  $user
     * @param ?string  $password
     * @param ?string  $vhost
     * @param ?string  $dsn
     */
    public function __construct($host = 'localhost',
                                $port = 5672,
                                $user = 'guest',
                                $password = 'guest',
                                $vhost = '/',
                                $dsn = null
    ) {
        if($dsn) {
            $params = [
                "host" => $host,
                "port" => $port,
                "user" => $user,
                "pass" => $password,
                "path" => $vhost
            ];
            $dsn = parse_url($dsn);

            $dnsConfig = array_merge($params, $dsn);

            $host = $dnsConfig['host'] ?? $host;
            $port = $dnsConfig['port'] ?? $port;
            $user = $dnsConfig['user'] ?? $user;
            $password = $dnsConfig['pass'] ?? $password;
            $vhost = $dnsConfig['path'] ?? $vhost;
        }

        $this->host     = $host;
        $this->port     = $port;
        $this->user     = $user;
        $this->password = $password;
        $this->vhost    = $vhost;
    }

    public function getConnect(): AMQPConnection
    {
        if (! class_exists('PhpAmqpLib\Connection\AMQPConnection')) {
           throw new FeatureRequired('PhpAmqpLib is not installed');
        }

        $conn = new AMQPConnection(
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->vhost
        );

        return $conn;
    }
}
