<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\http\HttpService;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\HttpService;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var HttpService
     */
    private $checker;

    /**
     * @param string $host       host name or IP address to check
     * @param int    $port       Port to connect to (defaults to 80)
     * @param string $path       The path to retrieve (defaults to /)
     * @param int    $statusCode (optional) Expected status code
     * @param null   $content    (optional) Expected substring to match against the page content
     */
    public function __construct($host, $port = 80, $path = '/', $statusCode = null, $content = null)
    {
        $this->checker = new HttpService($host, $port, $path, $statusCode, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
