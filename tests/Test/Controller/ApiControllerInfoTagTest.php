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
class ApiControllerInfoTagTest extends WebTestCase
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
     * @dataProvider tagsProvider
     */
    public function test_api_info_tags($params, $code, $count)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.info.tag(s)', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($content);

        $content = json_decode($content);

        $this->assertCount($count, $content, sprintf('%s=%s', $count, \count($content)));
    }

    public function test_api_info_tags500()
    {
        $managerMock = $this->createMock(RunnerManager::class);
        $managerMock->method('findTags')->willThrowException(new \Exception());
        $this->client->getContainer()->set('tvi_monitor.runner.manager', $managerMock);

        $req = $this->router->generate('tvi_monitor.routing.api.info.tag(s)', [], false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @dataProvider tagProvider
     */
    public function test_api_info_tag($params, $code, $res)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.info.tag', $params, false);
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

    public function test_api_info_tag500()
    {
        $managerMock = $this->createMock(RunnerManager::class);
        $managerMock->method('findTags')->willThrowException(new \Exception());
        $this->client->getContainer()->set('tvi_monitor.runner.manager', $managerMock);

        $req = $this->router->generate('tvi_monitor.routing.api.info.tag', ['id' => 'php'], false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @return array [$params, $code, $res]
     */
    public function tagsProvider()
    {
        return [
            'tags.1' => [
                [],
                Response::HTTP_OK, 11,
            ],
            'tags.2' => [
                ['id' => 'all'],
                Response::HTTP_OK, 1,
            ],
            'tags.3' => [
                ['tag' => 'all'],
                Response::HTTP_OK, 1,
            ],
            'tags.4' => [
                ['id[1]' => 'all'],
                Response::HTTP_OK, 1,
            ],
            'tags.5' => [
                ['tag[1]' => 'all'],
                Response::HTTP_OK, 1,
            ],
            'tags.6' => [
                ['tag[1]' => 'all', 'tag[2]' => 'bad'],
                Response::HTTP_OK, 2,
            ],
            'tags.empty.1' => [
                ['tag' => 'not_exist'],
                Response::HTTP_OK, 0,
            ],
        ];
    }

    /**
     * @return array [$params, $code, $res]
     */
    public function tagProvider()
    {
        return [
            'tag.1' => [
                ['id' => 'all'],
                Response::HTTP_OK, ['id' => 'all'],

            ],
            'tag.2' => [
                ['id' => 'bad'],
                Response::HTTP_OK, ['id' => 'bad'],
            ],
            'mot_exist.1' => [
                ['id' => 'not_exist'],
                Response::HTTP_NOT_FOUND, [],
            ],
        ];
    }
}
