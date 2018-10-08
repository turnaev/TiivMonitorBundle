<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Tvi\MonitorBundle\Test\Base\WebTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class ApiControllerInfoGroupTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    protected function setUp()
    {
        $this->client = $this->getClient(false);
        $this->router = $this->client->getContainer()->get('router');
    }

    public function test_api_info_check()
    {
//        $params = ['check' => 'core:php_version'];
//        $params = [];
//        $req = $this->router->generate('tvi_monitor.routing.api.info.check(s)', $params, false);
//        $this->client->request('GET', $req);
//
//        $response = $this->client->getResponse();
//        $content = $response->getContent();
//
//        v($content);
//
//        v(json_decode($content));
//        exit;
    }
}
