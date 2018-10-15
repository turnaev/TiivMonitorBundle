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
use Symfony\Component\HttpFoundation\Response;
use Tvi\MonitorBundle\Test\Base\WebTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class UIControllerTest extends WebTestCase
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

    public function test_ui_status_200()
    {
        $req = $this->router->generate('tvi_monitor.routing.ui.index', [], false);

        $this->client->request('GET', $req);
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
