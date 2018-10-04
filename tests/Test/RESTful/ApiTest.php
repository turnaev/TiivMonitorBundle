<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\RESTful;

use Symfony\Component\HttpFoundation\Response;
use Tvi\MonitorBundle\Test\Base\WebTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class ApiTest extends WebTestCase
{
    public function test()
    {
        $client = $this->createClient();

        $router = $client->getContainer()->get('router');

        $req = $router->generate('tvi_monitor.routing.api.check(s)', [], false);

        $client->request('GET', $req);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());


        //v($response->headers);

//        $crawler = $client->request('POST', $req);
//        $response = $client->getResponse();
//        v($response->getStatusCode());
    }
}
