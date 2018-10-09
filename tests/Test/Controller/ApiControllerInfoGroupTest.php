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
use Tvi\MonitorBundle\Runner\RunnerManager;
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

    /**
     * @dataProvider groupsProvider
     */
    public function test_api_info_groups($params, $code, $count)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.info.group(s)', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($content);

        $content = json_decode($content);

        $this->assertCount($count, $content, sprintf('%s=%s', $count, \count($content)));
    }

    public function test_api_info_groups500()
    {
        $managerMock = $this->createMock(RunnerManager::class);
        $managerMock->method('findGroups')->willThrowException(new \Exception());
        $this->client->getContainer()->set('tvi_monitor.runner.manager', $managerMock);

        $req = $this->router->generate('tvi_monitor.routing.api.info.group(s)', [], false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @dataProvider groupProvider
     */
    public function test_api_info_group($params, $code, $res)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.info.group', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($content);

        $content = json_decode($content);

        foreach ($res as $f => $v) {
            $this->assertSame($v, $content->{$f}, sprintf('%s=%s', print_r($f, true), print_r($v, true)));
        }
    }

    public function test_api_info_group500()
    {
        $managerMock = $this->createMock(RunnerManager::class);
        $managerMock->method('findGroups')->willThrowException(new \Exception());
        $this->client->getContainer()->set('tvi_monitor.runner.manager', $managerMock);

        $req = $this->router->generate('tvi_monitor.routing.api.info.group', ['id' => 'php'], false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @return array [$params, $code, $res]
     */
    public function groupsProvider()
    {
        return [
            'groups.1' => [
                [],
                Response::HTTP_OK, 8,
            ],
            'groups.2' => [
                ['id' => 'test:success'],
                Response::HTTP_OK, 1,
            ],
            'groups.3' => [
                ['group' => 'test:success'],
                Response::HTTP_OK, 1,
            ],
            'groups.4' => [
                ['id[1]' => 'test:success'],
                Response::HTTP_OK, 1,
            ],
            'groups.5' => [
                ['group[1]' => 'test:success'],
                Response::HTTP_OK, 1,
            ],
            'groups.empty.1' => [
                ['group[1]' => 'not_exist'],
                Response::HTTP_OK, 0,
            ],
        ];
    }

    /**
     * @return array [$params, $code, $res]
     */
    public function groupProvider()
    {
        return [
            'group.1' => [
                ['id' => 'test:success'],
                Response::HTTP_OK, ['id' => 'test:success'],

            ],
            'group.2' => [
                ['id' => 'php'],
                Response::HTTP_OK, ['id' => 'php'],
            ],
            'mot_exist.1' => [
                ['id' => 'not_exist'],
                Response::HTTP_NOT_FOUND, [],
            ],
        ];
    }
}
