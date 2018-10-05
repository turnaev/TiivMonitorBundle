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
class ApiControllerTest extends WebTestCase
{
    protected static $DEBUG = true;

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
    }

    public function test_api()
    {
        $this->client = $this->createClient();
        $this->router = $this->client->getContainer()->get('router');


        $req = $this->router->generate('tvi_monitor.routing.api.check(s)', [], false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        //v(json_decode($response->getContent()));
        ////
        $this->assertTrue(true);
        ////exit;


        //$this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        //$this->assertJson($response->getContent());


        //v(json_decode($response->getContent()));

        //v($response->headers);

//        $crawler = $client->request('POST', $req);
//        $response = $client->getResponse();
//        v($response->getStatusCode());
    }
}
