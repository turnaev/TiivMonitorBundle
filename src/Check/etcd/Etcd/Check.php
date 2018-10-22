<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\etcd\Etcd;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use JMS\Serializer\Annotation as JMS;
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
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $cert;

    /**
     * @var string
     */
    protected $sslKey;

    /**
     * @var string
     */
    protected $ca;

    /**
     * @var boolean
     */
    protected $verify;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param string $uri
     * @param bool   $verify
     * @param string $cert
     * @param string $sslKey
     * @param string $ca
     *
     * @throws \Exception
     */
    public function __construct(
        $url = 'https://localhost:2379',
        $verify = false,
        $cert = '/etc/etcd/cert/client-etcd.crt',
        $sslKey = '/etc/etcd/cert/client-etcd.key',
        $ca = '/etc/etcd/cert/ca.crt')
    {
        $this->url = $url;
        $this->verify = $verify;
        $this->cert = $cert;
        $this->sslKey = $sslKey;
        $this->ca = $ca;

        $this->client = $this->createClient();
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        try {
            $version = $this->getVersion();
            return new Success(null, json_decode($version, true));
        } catch (\Exception $e) {
            return new Failure($e->getMessage());
        }
    }

    /**
     * @return ClientInterface
     * @throws \Exception
     */
    private function createClient()
    {
        return new Client([
            'base_uri'   => $this->url,
            'verify'     => $this->verify,
            'exceptions' => true,
            'cert'       => $this->cert,
            'ssl_key'    => $this->sslKey,
        ]);
    }

    /**
     * @return string
     */
    private function getVersion()
    {
        $response = $this->client->get('/version');

        return $response->getBody().'';
    }
}
